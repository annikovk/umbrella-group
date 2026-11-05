<?php
/**
 * Блок «Заказать услугу» в статье блога.
 *
 * Связь статья → услуга — мета записи `ug_service_page` (ID страницы под
 * /services/). Поле в редакторе записи даёт ACF (post_object, регистрируется
 * здесь кодом, в БД группы полей нет); без ACF мета читается так же.
 *
 *  - есть услуга: после вступления — компактная строка со ссылкой на услугу,
 *    после текста — карточка: название, цена «от …», «Подробнее об услуге»
 *    и «Оставить заявку»;
 *  - нет услуги: после текста — общая карточка «Бесплатная консультация»
 *    с той же кнопкой заявки, строки после вступления нет.
 *
 * Заявка — существующий попап #leave-request-contact-form-lightbox (CF7 2886,
 * лежит в блоке «Footer Bottom» на каждой странице), своей отправки нет.
 * Цена — атрибут price шорткода [su_welcome_screen] страницы услуги (то же
 * «от 33 000 руб.», что на первом экране услуги); нет атрибута — нет цены.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const UG_POST_SERVICE_META = 'ug_service_page';
const UG_POST_SERVICE_FIELD_KEY = 'field_ug_service_page';
const UG_SERVICES_ROOT_PATH = 'services';
const UG_LEAVE_REQUEST_LIGHTBOX = '#leave-request-contact-form-lightbox';

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group([
        'key' => 'group_ug_post_service',
        'title' => 'Услуга по теме статьи',
        'fields' => [[
            'key' => UG_POST_SERVICE_FIELD_KEY,
            'label' => 'Страница услуги',
            'name' => UG_POST_SERVICE_META,
            'type' => 'post_object',
            'instructions' => 'Блок «Заказать услугу» в статье: название, цена «от …» с первого экрана услуги, ссылка и кнопка заявки. Пусто — общий блок «Бесплатная консультация».',
            'post_type' => ['page'],
            'return_format' => 'id',
            'allow_null' => 1,
            'multiple' => 0,
            'ui' => 1,
        ]],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'post']]],
        'position' => 'side',
        'active' => true,
    ]);
});

// В выпадающем списке — только страницы под /services/.
add_filter('acf/fields/post_object/query/key=' . UG_POST_SERVICE_FIELD_KEY, static function (array $args): array {
    $root = get_page_by_path(UG_SERVICES_ROOT_PATH);
    if ($root) {
        $ids = wp_list_pluck(get_pages(['child_of' => $root->ID, 'post_status' => 'publish']), 'ID');
        $args['post__in'] = $ids ?: [0];
        $args['post_status'] = 'publish';
    }
    return $args;
});

/** Страница услуги, привязанная к записи; null — не задана, не опубликована или не под /services/. */
function ug_post_service_page(int $post_id): ?WP_Post
{
    $page = get_post((int) get_post_meta($post_id, UG_POST_SERVICE_META, true));
    if (!$page || $page->post_type !== 'page' || $page->post_status !== 'publish') {
        return null;
    }
    return str_starts_with(get_page_uri($page), UG_SERVICES_ROOT_PATH . '/') ? $page : null;
}

/** Цена с первого экрана услуги (атрибут price [su_welcome_screen]), «» — нет. */
function ug_service_price(WP_Post $page): string
{
    $content = $page->post_content;
    if (!has_shortcode($content, 'su_welcome_screen')
        || !preg_match_all('/' . get_shortcode_regex(['su_welcome_screen']) . '/s', $content, $m, PREG_SET_ORDER)) {
        return '';
    }
    foreach ($m as $sc) {
        $atts = shortcode_parse_atts($sc[3]);
        if (is_array($atts) && trim((string) ($atts['price'] ?? '')) !== '') {
            return trim(wp_strip_all_tags((string) $atts['price']));
        }
    }
    return '';
}

/**
 * Разметка блока. $variant: 'full' — карточка после текста, 'compact' — строка
 * после вступления (только при заданной услуге, иначе «»).
 */
function ug_post_service_cta(int $post_id, string $variant = 'full'): string
{
    // content-single.php рисует и одиночные страницы других типов (faq и т.п.) — там блока нет
    if (get_post_type($post_id) !== 'post') {
        return '';
    }
    $page = ug_post_service_page($post_id);
    if ($page === null && $variant === 'compact') {
        return '';
    }
    ug_enqueue_style('/assets/css/blocks/post-service-cta.css');
    return ug_render('post-service-cta', [
        'variant' => $variant,
        'title' => $page ? get_the_title($page) : '',
        'url' => $page ? get_permalink($page) : '',
        'price' => $page ? ug_service_price($page) : '',
        'request' => UG_LEAVE_REQUEST_LIGHTBOX,
    ]);
}

/**
 * Компактная строка — после вступления: перед первым <h2>, которому
 * предшествует хотя бы один абзац текста (оглавление «Содержание» в начале
 * статьи так пропускается). Подходящего <h2> нет — строку не ставим.
 */
function ug_post_content_with_service_cta(int $post_id, string $html): string
{
    if (!preg_match_all('~<h2\b~i', $html, $m, PREG_OFFSET_CAPTURE)) {
        return $html;
    }
    $prev = 0;
    foreach ($m[0] as [, $pos]) {
        if ($pos >= 200 && stripos(substr($html, $prev, $pos - $prev), '</p>') !== false) {
            $cta = ug_post_service_cta($post_id, 'compact');
            return $cta === '' ? $html : substr($html, 0, $pos) . $cta . substr($html, $pos);
        }
        $prev = $pos + 3;
    }
    return $html;
}
