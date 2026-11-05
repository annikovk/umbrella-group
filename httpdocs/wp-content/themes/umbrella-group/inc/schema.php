<?php
/**
 * Автоматическая schema-разметка (JSON-LD) для страниц услуг.
 * AIOSEO даёт этим страницам только общий WebPage — здесь добавляем Service.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function umbrella_is_service_page() {
    if ( ! is_page() ) {
        return false;
    }

    $path = trim( parse_url( get_permalink(), PHP_URL_PATH ), '/' );

    return strpos( $path, 'services/' ) === 0 && $path !== 'services';
}

function umbrella_get_service_description( $post ) {
    // 1. Ручной excerpt, если он задан у страницы.
    $excerpt = get_the_excerpt( $post );
    if ( ! empty( trim( $excerpt ) ) ) {
        return wp_trim_words( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $excerpt ) ) ), 30 );
    }

    // 2. Выполняем только шорткоды (без хуков breadcrumbs/виджетов на the_content).
    $rendered = do_shortcode( $post->post_content );
    $clean    = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $rendered ) ) );

    if ( ! empty( $clean ) ) {
        return wp_trim_words( $clean, 30 );
    }

    // 3. Фолбэк, если текста не нашлось вообще.
    return get_the_title( $post );
}

function umbrella_add_service_schema() {
    if ( ! umbrella_is_service_page() ) {
        return;
    }

    global $post;

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => get_the_title( $post ),
        'description' => umbrella_get_service_description( $post ),
        'provider'    => array(
            '@type' => 'Organization',
            'name'  => 'Umbrella Group',
            'url'   => home_url( '/' ),
        ),
        'areaServed'  => 'RU',
        'url'         => get_permalink( $post ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'umbrella_add_service_schema' );


/* ============================================================
 *  FAQPage на страницах услуг
 * ============================================================
 *
 * Источники — только блоки, которые на странице и есть «Частые вопросы»:
 *  - [faq_block] — те же 5 записей faq, что выводит шорткод;
 *  - [accordion] в секции, где заголовок <h2>/<h3> или label секции
 *    говорит о вопросах («Частые вопросы…», «Вопрос-Ответ», «FAQ»).
 *    Аккордеоны цен, этапов, видов работ и т.п. не берутся.
 * Всё вместе — один FAQPage на страницу. Ответы — текст без шорткодов и HTML.
 * Если AIOSEO уже выдал FAQPage (тип страницы или своя схема) — не выводим.
 */

/** Текст для JSON-LD: без шорткодов (их содержимое остаётся), тегов и лишних пробелов. */
function umbrella_schema_plain_text( $html ) {
    $text = preg_replace( '/\[\/?[a-z_][\w-]*(?:\s[^\]]*)?\]/iu', ' ', (string) $html );
    // блочные теги — границы слов: «…лицензию</li><li>— Если» не склеивать
    $text = preg_replace( '~<(?:/?(?:p|li|ul|ol|div|h[1-6]|tr|td|th|table|blockquote)\b[^>]*|br\s*/?)>~i', ' ', (string) $text );
    $text = wp_strip_all_tags( (string) $text );
    $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/** Вопросы и ответы страницы услуги: [['q' => …, 'a' => …], …] в порядке страницы. */
function umbrella_service_faq_items( $post ) {
    // закомментированные куски (<!-- [faq_block …] -->) на странице не видны
    $content = (string) preg_replace( '/<!--.*?-->/s', '', (string) $post->post_content );
    $found   = array(); // смещение в контенте => пары

    // [accordion] в «вопросной» секции
    if ( preg_match_all( '/\[accordion(?=[\s\]])[^\]]*\](.*?)\[\/accordion\]/s', $content, $acc, PREG_OFFSET_CAPTURE ) ) {
        foreach ( $acc[0] as $i => $match ) {
            $before  = substr( $content, 0, $match[1] );
            $section = strrpos( $before, '[section' );
            $scope   = $section === false ? substr( $before, -2000 ) : substr( $before, $section );
            $marker  = '';
            if ( preg_match( '/^\[section[^\]]*\blabel="([^"]*)"/', $scope, $label ) ) {
                $marker .= $label[1] . ' ';
            }
            if ( preg_match_all( '/<h[23][^>]*>(.*?)<\/h[23]>/su', $scope, $heads ) ) {
                $marker .= end( $heads[1] );
            }
            if ( ! preg_match( '/вопрос|faq/iu', $marker ) ) {
                continue;
            }
            $pairs = array();
            if ( preg_match_all( '/' . get_shortcode_regex( array( 'accordion-item' ) ) . '/s', $acc[1][ $i ][0], $items, PREG_SET_ORDER ) ) {
                foreach ( $items as $item ) {
                    $atts    = shortcode_parse_atts( $item[3] );
                    $pairs[] = array(
                        'q' => umbrella_schema_plain_text( is_array( $atts ) ? ( $atts['title'] ?? '' ) : '' ),
                        // wptexturize — как в the_content: «-» → «—», кавычки → «ёлочки»
                        'a' => umbrella_schema_plain_text( wptexturize( $item[5] ) ),
                    );
                }
            }
            $found[ $match[1] ] = $pairs;
        }
    }

    // [faq_block] (кроме main — вкладки всех разделов, это страница /faq/)
    if ( has_shortcode( $content, 'faq_block' ) && class_exists( 'umbrella_faq' )
        && preg_match_all( '/' . get_shortcode_regex( array( 'faq_block' ) ) . '/s', $content, $blocks, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
        foreach ( $blocks as $block ) {
            $atts = shortcode_parse_atts( $block[3][0] );
            $atts = is_array( $atts ) ? $atts : array();
            if ( ! empty( $atts['main'] ) ) {
                continue;
            }
            $faq       = new umbrella_faq();
            $faq->atts = $atts;
            $pairs     = array();
            foreach ( $faq->faq_posts() as $faq_post ) {
                $pairs[] = array(
                    'q' => umbrella_schema_plain_text( $faq_post->post_title ),
                    'a' => umbrella_schema_plain_text( $faq_post->post_content ),
                );
            }
            $found[ $block[0][1] ] = $pairs;
        }
    }

    ksort( $found );
    $result = array();
    $seen   = array();
    foreach ( $found as $pairs ) {
        foreach ( $pairs as $pair ) {
            $key = mb_strtolower( $pair['q'] );
            if ( $pair['q'] === '' || $pair['a'] === '' || isset( $seen[ $key ] ) ) {
                continue;
            }
            $seen[ $key ] = true;
            $result[]     = $pair;
        }
    }
    return $result;
}

// Выдал ли AIOSEO свой FAQPage: его схема печатается на wp_head:1, раньше нашей.
$GLOBALS['umbrella_aioseo_has_faqpage'] = false;
add_filter( 'aioseo_schema_output', function ( $graphs ) {
    foreach ( (array) $graphs as $graph ) {
        $type = is_array( $graph ) ? ( $graph['@type'] ?? '' ) : '';
        if ( in_array( 'FAQPage', (array) $type, true ) ) {
            $GLOBALS['umbrella_aioseo_has_faqpage'] = true;
        }
    }
    return $graphs;
}, 99 );

function umbrella_add_service_faq_schema() {
    if ( ! umbrella_is_service_page() || ! empty( $GLOBALS['umbrella_aioseo_has_faqpage'] ) ) {
        return;
    }
    $items = umbrella_service_faq_items( get_queried_object() );
    if ( ! $items ) {
        return;
    }
    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map( static function ( $pair ) {
            return array(
                '@type'          => 'Question',
                'name'           => $pair['q'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $pair['a'],
                ),
            );
        }, $items ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'umbrella_add_service_faq_schema', 20 );
