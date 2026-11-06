<?php
/**
 * Сниппет insert-php #1031 «Наши клиенты | плитки клиентов» → [ug_client_tiles].
 * Вкладки-фильтр по отраслям и карточки всех клиентов; страница /about/clients/ (1000).
 *
 * Вкладки — та же разметка и классы, что на /about/cases/ (cases_tabs.css):
 * «Все клиенты» первой, дальше отрасли client_industry, в которых есть хотя бы
 * один опубликованный клиент, со счётчиком. Порядок отраслей — term_order
 * плагина taxonomy-terms-order, при равенстве term_id: раньше get_categories()
 * сортировал только по term_order, у всех он 0, и MySQL отдавал вкладки
 * в случайном порядке. Фильтр и листалка по 6 карточек (3 × 2) — общие
 * с кейсами: cases_tabs.js, cases_tabs.css.
 *
 * Карточка: логотип (серый, цвет на наведении), название, «индивидуальная
 * отрасль», внизу «таблетки» услуг и стрелка. Услуги — client_category клиента
 * без служебной «Главная» (в админке «Отображать на»: Юридические услуги,
 * Аудит). Карточка целиком ведёт туда же, куда раньше плитка: есть отрывок —
 * окно с отзывом и сканом (<dialog>, ug_client_review), нет — сайт клиента.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/** Термины client_category, которые не услуга, а место показа. */
const UG_CLIENT_TILES_NOT_SERVICE = ['main'];

/**
 * Вкладка «Другие клиенты компании» — последняя: клиенты без отрасли
 * (client_industry). Термина такого нет; нет таких клиентов — вкладки нет,
 * как у пустых отраслей. Серая подпись с тем же текстом на карточке — другое
 * условие: у клиента нет услуги (см. ug_client_tiles_card).
 */
const UG_CLIENT_TILES_OTHER = ['slug' => 'other-clients', 'name' => 'Другие клиенты компании'];

/** Услуги клиента — термины client_category без служебных. @return WP_Term[] */
function ug_client_tiles_services(WP_Post $post): array
{
    $terms = get_the_terms($post->ID, 'client_category');
    return array_values(array_filter(
        is_array($terms) ? $terms : [],
        static fn($t) => !in_array($t->slug, UG_CLIENT_TILES_NOT_SERVICE, true)
    ));
}

function ug_snippet_client_tiles(array $a): void
{
    $posts = get_posts([
        'numberposts' => 99,
        'post_type' => 'client',
    ]);
    // Без логотипа — в конец (usort устойчив: внутри групп свежие первыми, как было)
    usort($posts, static fn($x, $y) => (int) !has_post_thumbnail($x) <=> (int) !has_post_thumbnail($y));

    $counts = [];
    $others = 0;
    $cards = '';
    $lightboxes = '';
    foreach ($posts as $post) {
        $industries = get_the_terms($post->ID, 'client_industry');
        $industries = is_array($industries) ? $industries : [];
        foreach ($industries as $term) {
            $counts[$term->term_id] = ($counts[$term->term_id] ?? 0) + 1;
        }
        $services = ug_client_tiles_services($post);
        $others += $industries ? 0 : 1;
        [$card, $lightbox] = ug_client_tiles_card($post, $industries, $services);
        $cards .= $card;
        $lightboxes .= $lightbox;
    }

    $terms = get_terms(['taxonomy' => 'client_industry', 'hide_empty' => false]);
    $terms = array_filter(is_array($terms) ? $terms : [], static fn($t) => isset($counts[$t->term_id]));
    usort($terms, static fn($x, $y) => [(int) ($x->term_order ?? 0), $x->term_id] <=> [(int) ($y->term_order ?? 0), $y->term_id]);

    $tabs = '<button type="button" class="cases-page__tab is-active" data-branch="all" aria-selected="true">Все клиенты</button>';
    foreach ($terms as $term) {
        $tabs .= '<button type="button" class="cases-page__tab" data-branch="' . esc_attr($term->slug) . '" aria-selected="false">'
            . esc_html($term->name) . '<span class="cases-page__tab-count">' . $counts[$term->term_id] . '</span></button>';
    }
    if ($others) {
        $tabs .= '<button type="button" class="cases-page__tab" data-branch="' . UG_CLIENT_TILES_OTHER['slug'] . '" aria-selected="false">'
            . esc_html(UG_CLIENT_TILES_OTHER['name']) . '<span class="cases-page__tab-count">' . $others . '</span></button>';
    }

    ug_enqueue_style('assets/css/blocks/cases_tabs.css');
    ug_enqueue_style('assets/css/blocks/client_tiles.css');
    ug_enqueue_script('assets/js/blocks/cases_tabs.js');
    ug_enqueue_script('assets/js/blocks/client_tiles.js', [ug_style_handle('assets/js/blocks/cases_tabs.js')]);

    echo '<div class="client-tiles">';
    echo '<div class="cases-page__tabs" role="tablist">' . $tabs . '</div>';
    echo '<div class="client-tiles__grid">' . $cards . '</div>';
    echo '<nav class="cases-page__pager" aria-label="Страницы клиентов"></nav>';
    echo '</div>';
    echo $lightboxes;
    echo $lightboxes !== '' ? ug_client_review_zoom() : '';
}

/**
 * Карточка клиента и окно его отзыва (пустая строка, если отрывка нет).
 *
 * @param WP_Term[] $industries
 * @param WP_Term[] $services  ug_client_tiles_services()
 * @return array{0: string, 1: string}
 */
function ug_client_tiles_card(WP_Post $post, array $industries, array $services): array
{
    $branches = array_map(static fn($t) => $t->slug, $industries);
    if (!$industries) {
        $branches[] = UG_CLIENT_TILES_OTHER['slug'];
    }
    $branches = implode(' ', $branches);

    $lightbox = '';
    $attrs = '';
    if (get_the_excerpt($post->ID) !== '') {
        $id = 'client-feedback-' . $post->ID;
        $attrs = ' href="#' . $id . '" aria-haspopup="dialog" aria-label="' . esc_attr('Отзыв клиента ' . $post->post_title) . '"';
        $lightbox = ug_client_review($post, $id, $services);
    } else {
        $site = (string) get_post_meta($post->ID, 'umbrella_link_to_client_website', true);
        if ($site !== '') {
            $attrs = ' href="' . esc_url($site) . '" target="_blank" rel="noopener nofollow" aria-label="' . esc_attr('Сайт клиента ' . $post->post_title) . '"';
        }
    }
    $tag = $attrs !== '' ? 'a' : 'div';

    $logo = ug_client_tiles_logo($post);
    // Логотипа нет (Дамаст, БИТ-2000) — серый значок здания, чтобы ряд не прыгал
    $logo = $logo !== '' ? $logo : '<svg class="client-tiles__no-logo" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path d="M4.75 20.25V5.5c0-.4.35-.75.75-.75h8c.4 0 .75.35.75.75v14.75M14.25 9.75h4.25c.4 0 .75.35.75.75v9.75M3.25 20.25h17.5M8 8.25h3M8 11.75h3M8 15.25h3M16.75 13.25h0M16.75 16.25h0" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    $pills = '';
    foreach ($services as $term) {
        $pills .= '<span class="client-tiles__service">' . esc_html($term->name) . '</span>';
    }
    // Клиент без услуги — общая подпись, чтобы низ карточки не пустовал
    if ($pills === '') {
        $pills = '<span class="client-tiles__service">' . esc_html(UG_CLIENT_TILES_OTHER['name']) . '</span>';
    }
    $arrow = $tag === 'a'
        ? '<svg class="client-tiles__arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        : '';
    $foot = ($pills !== '' || $arrow !== '')
        ? '<span class="client-tiles__foot"><span class="client-tiles__services">' . $pills . '</span>' . $arrow . '</span>'
        : '';

    $title = esc_html($post->post_title);
    $activity = esc_html(ug_client_tiles_activity($post));
    $card = '<' . $tag . ' class="client-tiles__card" data-branch="' . esc_attr($branches) . '"' . $attrs . '>'
        . '<span class="client-tiles__logo">' . $logo . '</span>'
        . '<span class="client-tiles__name">' . $title . '</span>'
        . ($activity !== '' ? '<span class="client-tiles__activity">' . $activity . '</span>' : '')
        . $foot
        . '</' . $tag . '>';

    return [$card, $lightbox];
}

/**
 * Размер логотипа — как на макете, по «зрительному весу»: площадь картинки
 * около UG_CLIENT_TILES_LOGO_AREA px², но не больше зоны UG_CLIENT_TILES_LOGO_BOX
 * (ширина × высота). Иначе длинные (Алтай West) выходят тонкой полоской,
 * а квадратные (Сиблом, Кари) — огромными. Файлы от 91 до 1264 px; растр
 * больше чем в UG_CLIENT_TILES_LOGO_UP раза не растягиваем — мылится
 * (Аквалоджик 141 px, Автомаркет 91 px). Ширина — style, CSS (max-width)
 * ужимает её на узкой карточке, высота — по пропорции. SVG без размеров —
 * квадрат площадью AREA.
 */
const UG_CLIENT_TILES_LOGO_AREA = 13000;
const UG_CLIENT_TILES_LOGO_BOX = [270, 110];
const UG_CLIENT_TILES_LOGO_UP = 1.5;

function ug_client_tiles_logo(WP_Post $post): string
{
    $id = (int) get_post_thumbnail_id($post);
    if (!$id) {
        return '';
    }
    [$box_w, $box_h] = UG_CLIENT_TILES_LOGO_BOX;
    $full = wp_get_attachment_image_src($id, 'full');
    [$w, $h] = [(int) ($full[1] ?? 0), (int) ($full[2] ?? 0)];
    $style = 'width:' . (int) round(sqrt(UG_CLIENT_TILES_LOGO_AREA)) . 'px';
    if ($w > 0 && $h > 0) {
        $scale = min(sqrt(UG_CLIENT_TILES_LOGO_AREA / ($w * $h)), $box_w / $w, $box_h / $h, UG_CLIENT_TILES_LOGO_UP);
        $style = 'width:' . (int) round($w * $scale) . 'px';
    }
    // ux-skip-lazy: lazy-load Flatsome оставил бы до прокрутки svg-заглушку,
    // а с родным loading="lazy" логотипы догружает сам браузер
    return wp_get_attachment_image($id, 'medium_large', false, [
        'class' => 'ux-skip-lazy',
        'loading' => 'lazy',
        'alt' => $post->post_title,
        'style' => $style,
    ]);
}

/** «Индивидуальная отрасль» клиента: в данных бывает со строчной и с переносом строки внутри. */
function ug_client_tiles_activity(WP_Post $post): string
{
    $activity = trim((string) preg_replace('/\s+/u', ' ', (string) get_post_meta($post->ID, 'umbrella_client_personal_industry', true)));
    return mb_strtoupper(mb_substr($activity, 0, 1)) . mb_substr($activity, 1);
}

/**
 * Вопрос внизу окна отзыва — по услуге клиента, в порядке ключей: у Гулливера
 * и Сибирской кошки обе услуги, вопрос юридический. Нет услуги — 'default'.
 */
const UG_CLIENT_REVIEW_QUESTION = [
    'services-le' => 'Нужна юридическая поддержка бизнеса?',
    'audit' => 'Нужен аудит бизнеса?',
    'register-elimination' => 'Нужна помощь с регистрацией бизнеса?',
    'default' => 'Нужна помощь с задачами бизнеса?',
];

/**
 * Скан благодарственного письма. В umbrella_feedback_scan лежит полный адрес
 * файла (мета-бокс просит ID, но заполнен адресами, все с https://taxlab.ru),
 * файлы — вложения медиатеки: находим вложение по пути в uploads, чтобы
 * превью брать уменьшенным, а адрес — от текущего сайта. Не нашли —
 * адрес из поля как есть.
 *
 * @return array{full: string, preview: string, w: int, h: int}|null
 */
function ug_client_tiles_scan(WP_Post $post): ?array
{
    $url = trim((string) get_post_meta($post->ID, 'umbrella_feedback_scan', true));
    if ($url === '') {
        return null;
    }
    $id = is_numeric($url) ? (int) $url : 0;
    if (!$id && preg_match('~/wp-content/uploads/(.+)$~u', $url, $m)) {
        global $wpdb;
        $id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
            rawurldecode($m[1])
        ));
    }
    $full = $id ? wp_get_attachment_image_src($id, 'full') : false;
    if (!$full) {
        return is_numeric($url) ? null : ['full' => $url, 'preview' => $url, 'w' => 0, 'h' => 0];
    }
    $preview = wp_get_attachment_image_src($id, 'medium_large') ?: $full;
    return ['full' => $full[0], 'preview' => $preview[0], 'w' => (int) $preview[1], 'h' => (int) $preview[2]];
}

/**
 * Окно отзыва клиента (<dialog>, открывает client_tiles.js) по макету:
 * шапка — логотип, название, отрасль, таблетки услуг; слева отзыв, справа
 * скан письма с «Увеличить документ»; внизу вопрос и «Обсудить задачу».
 *
 * Раньше это был Flatsome [lightbox] со сканом [ux_image lightbox="true"]:
 * клик по скану закрывал окно отзыва и не открывал картинку (Magnific Popup
 * держит одно окно, второе поверх открытого схлопывается), а на ≤849 px
 * колонка со сканом была скрыта вовсе. Крупный скан теперь — второй <dialog>
 * поверх отзыва (ug_client_review_zoom): Esc и крестик закрывают только его.
 *
 * Данные: отрывок (post_excerpt) — первый абзац крупнее, с красной кавычкой;
 * дальше post_content как есть. Отдельных полей заголовка отзыва, автора
 * и должности нет — подпись «Должность, ФИО» лежит в конце post_content
 * в разном виде (серый <p>, таблица, курсив, просто строка), её не разбираем.
 *
 * @param WP_Term[] $services ug_client_tiles_services()
 */
function ug_client_review(WP_Post $post, string $id, array $services): string
{
    $title = esc_html($post->post_title);
    $activity = esc_html(ug_client_tiles_activity($post));

    $logo = '';
    $thumb = (int) get_post_thumbnail_id($post);
    if ($thumb) {
        // ux-skip-lazy: иначе lazy-load Flatsome оставит svg-заглушку; loading="lazy"
        // в закрытом <dialog> не грузит картинку, пока окно не откроют
        $logo = '<span class="client-review__logo">' . wp_get_attachment_image($thumb, 'medium', false, [
            'class' => 'ux-skip-lazy', 'loading' => 'lazy', 'alt' => $post->post_title,
        ]) . '</span>';
    }

    $pills = '';
    $question = UG_CLIENT_REVIEW_QUESTION['default'];
    $slugs = array_map(static fn($t) => $t->slug, $services);
    foreach ($services as $term) {
        $pills .= '<span class="client-review__service">' . esc_html($term->name) . '</span>';
    }
    foreach (UG_CLIENT_REVIEW_QUESTION as $slug => $text) {
        if (in_array($slug, $slugs, true)) {
            $question = $text;
            break;
        }
    }
    $meta = ($activity !== '' || $pills !== '')
        ? '<span class="client-review__meta">' . ($activity !== '' ? '<span class="client-review__activity">' . $activity . '</span>' : '') . $pills . '</span>'
        : '';

    $lead = esc_html(get_the_excerpt($post->ID));
    $text = wpautop((string) $post->post_content);
    // Пустые абзацы-распорки (&nbsp;, у Автомаркета — экранированный &amp;nbsp;)
    $text = (string) preg_replace('~<p>(?:\s|&nbsp;|&amp;nbsp;|<br\s*/?>)*</p>~u', '', $text);

    $scan = ug_client_tiles_scan($post);
    $aside = '';
    if ($scan) {
        $size = $scan['w'] ? ' width="' . $scan['w'] . '" height="' . $scan['h'] . '"' : '';
        $full = esc_url($scan['full']);
        $zoom = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5 20 20" stroke-linecap="round"/></svg>';
        $aside = '<aside class="client-review__doc">'
            . '<p class="client-review__doc-title">Благодарственное письмо</p>'
            . '<a class="client-review__scan" href="' . $full . '" data-zoom aria-label="Благодарственное письмо — увеличить">'
            . '<img class="ux-skip-lazy" src="' . esc_url($scan['preview']) . '"' . $size . ' loading="lazy" decoding="async" alt="' . esc_attr('Благодарственное письмо ' . $post->post_title) . '"></a>'
            . '<a class="client-review__zoom" href="' . $full . '" data-zoom>' . $zoom . '<span>Увеличить документ</span></a>'
            . '</aside>';
    }

    $arrow = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    return '<dialog class="client-review' . ($scan ? '' : ' client-review--no-doc') . '" id="' . esc_attr($id) . '" aria-labelledby="' . esc_attr($id) . '-title">'
        . '<div class="client-review__box" tabindex="-1">'
        . '<button type="button" class="client-review__close" data-close aria-label="Закрыть"></button>'
        . '<header class="client-review__head' . ($logo !== '' ? '' : ' client-review__head--no-logo') . '">' . $logo
        . '<span class="client-review__who"><span class="client-review__name" id="' . esc_attr($id) . '-title">' . $title . '</span>' . $meta . '</span>'
        . '</header>'
        . '<div class="client-review__body">'
        . '<div class="client-review__main">'
        . '<p class="client-review__label">Отзыв клиента</p>'
        . '<blockquote class="client-review__quote">'
        . '<p class="client-review__lead">' . $lead . '</p>'
        . '<div class="client-review__text">' . $text . '</div>'
        . '</blockquote>'
        . '</div>'
        . $aside
        . '</div>'
        . '<footer class="client-review__foot">'
        . '<p class="client-review__question">' . esc_html($question) . '</p>'
        . '<a href="#header-contact-form-lightbox" class="client-review__btn">Обсудить задачу' . $arrow . '</a>'
        . '</footer>'
        . '</div>'
        . '</dialog>';
}

/** Крупный скан письма — одно окно на страницу, адрес подставляет client_tiles.js. */
function ug_client_review_zoom(): string
{
    return '<dialog class="client-review-zoom" aria-label="Благодарственное письмо">'
        . '<button type="button" class="client-review-zoom__close" data-close aria-label="Закрыть документ"></button>'
        . '<img class="client-review-zoom__img ux-skip-lazy" alt="">'
        . '</dialog>';
}

ug_snippet_shortcode('ug_client_tiles', 'ug_snippet_client_tiles');
