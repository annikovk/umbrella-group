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


