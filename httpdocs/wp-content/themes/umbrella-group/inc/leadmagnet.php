<?php
/**
 * Лид-магнит «Чек-листы для клиник» (QR-код на конференции).
 *
 * [ug_checklists_form] печатает Flatsome lightbox #checklists: заголовок, текст,
 * CF7-форма «Чек-листы для клиник (конференция)» и скрытое состояние «Готово»
 * с двумя кнопками скачивания PDF из uploads/checklists/. Само по себе на
 * странице ничего не видно — поп-ап открывает assets/js/blocks/leadmagnet.js
 * по ?checklist=1 или #checklists (на клиенте: страницы кэширует WP Rocket),
 * «Готово» он показывает по wpcf7mailsent только этой формы.
 *
 * Форма ищется по заголовку, а не по ID: ID в локальной базе и на проде
 * расходятся. Свой ID можно передать атрибутом: [ug_checklists_form form="123"].
 * Допродажу (includes/upsell.php) форма не показывает — data-no-upsell на поп-апе.
 * reCAPTCHA для неё мягкая — см. UG_CF7_RECAPTCHA_SOFT_FORMS в inc/cf7.php.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const UG_LEADMAGNET_FORM_TITLE = 'Чек-листы для клиник (конференция)';

/**
 * Чек-листы: ключ для ?ug_checklist= => файл в uploads/checklists/, имя при
 * сохранении, подпись кнопки.
 */
const UG_LEADMAGNET_FILES = [
    'tm'       => [
        'file'  => 'checklist-tovarnyj-znak-kliniki.pdf',
        'name'  => 'Чек-лист_Товарный_знак_клиники.pdf',
        'label' => 'Скачать: Товарный знак клиники (PDF)',
    ],
    'passport' => [
        'file'  => 'checklist-pasport-bezopasnosti-kliniki.pdf',
        'name'  => 'Чек-лист_Паспорт_безопасности_клиники.pdf',
        'label' => 'Скачать: Паспорт безопасности клиники (PDF)',
    ],
];

/*
 * Отдача PDF с Content-Disposition: attachment — иначе iPhone открывает файл
 * во вкладке и не предлагает сохранить. Статику из uploads nginx (Plesk) отдаёт
 * сам, мимо Apache, так что .htaccess в uploads/checklists/ не сработал бы —
 * поэтому PHP: /?ug_checklist=tm|passport, только два файла из списка.
 */
add_action('init', static function (): void {
    if (!isset($_GET['ug_checklist'])) {
        return;
    }
    $key = sanitize_key(wp_unslash((string) $_GET['ug_checklist']));
    $item = UG_LEADMAGNET_FILES[$key] ?? null;
    $path = $item ? trailingslashit(wp_upload_dir()['basedir']) . 'checklists/' . $item['file'] : '';
    if ($path === '' || !is_file($path)) {
        status_header(404);
        nocache_headers();
        exit;
    }
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    // octet-stream, а не application/pdf: с pdf iPhone (Safari, iOS 18) даже при
    // attachment открывает файл во встроенном просмотрщике, «Поделиться» отдаёт
    // только ссылку (24.11). С octet-stream Safari спрашивает «Загрузить?» и
    // кладёт файл в «Файлы» → Загрузки; имя с .pdf сохраняется.
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $item['file'] . '"; filename*=UTF-8\'\'' . rawurlencode($item['name']));
    header('Content-Length: ' . (string) filesize($path));
    header('Cache-Control: no-store, max-age=0');
    header('X-Robots-Tag: noindex');
    header('X-Content-Type-Options: nosniff');
    readfile($path);
    exit;
}, 0);

/** ID опубликованной формы лид-магнита или 0. */
function ug_leadmagnet_form_id(): int
{
    static $id = null;
    if ($id === null) {
        $ids = get_posts([
            'post_type'        => 'wpcf7_contact_form',
            'title'            => UG_LEADMAGNET_FORM_TITLE,
            'post_status'      => 'publish',
            'posts_per_page'   => 1,
            'orderby'          => 'ID',
            'order'            => 'ASC',
            'fields'           => 'ids',
            'no_found_rows'    => true,
            'suppress_filters' => true,
        ]);
        $id = $ids ? (int) $ids[0] : 0;
    }
    return $id;
}

add_filter('ug_cf7_recaptcha_soft_forms', static function ($ids) {
    $id = ug_leadmagnet_form_id();
    return $id ? array_merge((array) $ids, [$id]) : $ids;
});

add_shortcode('ug_checklists_form', static function ($atts): string {
    $atts = shortcode_atts(['form' => ''], (array) $atts, 'ug_checklists_form');
    $form_id = (int) $atts['form'] ?: ug_leadmagnet_form_id();
    if (!$form_id || get_post_type($form_id) !== 'wpcf7_contact_form') {
        return '';
    }

    ug_enqueue_style('assets/css/blocks/leadmagnet.css');
    ug_enqueue_script('assets/js/blocks/leadmagnet.js', ['jquery', 'flatsome-js']);

    // Прямые ссылки на .pdf с теми же атрибутами, что у «Скачать памятку»
    // (inc/snippets/10644-download-memo.php): на iPhone PDF открывается, и
    // «Поделиться» отдаёт сам файл. Через /?ug_checklist= (адрес без .pdf)
    // «Поделиться» предлагал только ссылку (24.11) — эндпоинт выше не используется.
    // ?v= — версия файлов: PDF отдаются без Cache-Control, и телефон, уже
    // открывавший старый чек-лист, мог бы показать его из кэша. Заменили PDF —
    // увеличь число. v=2 — 24.11, юрист Артём Франк вместо А. Леоновой;
    // v=3 — 24.11, без прозрачности (серые плашки на iOS); v=4 — 24.11, фото
    // Артёма Франка в паспорте безопасности.
    $base = trailingslashit(wp_upload_dir()['baseurl']) . 'checklists/';
    $links = '';
    foreach (UG_LEADMAGNET_FILES as $item) {
        $links .= '<a class="ug-lm-dl" target="_self" download rel="noopener" href="' . esc_url($base . $item['file'] . '?v=4') . '">'
            . '<span class="ug-lm-dl__ic" aria-hidden="true"></span><span>' . esc_html($item['label']) . '</span></a>';
    }
    $links .= '<p class="ug-lm__hint">На iPhone: откройте файл → «Поделиться» → «Сохранить в&nbsp;Файлы» или отправьте в&nbsp;мессенджер</p>';

    // Лайтбокс Flatsome = magnificPopup; разметку отдаём через [lightbox], как
    // поп-ап расчёта в inc/price.php.
    return do_shortcode(
        '[lightbox id="checklists" width="480px" padding="0px" class="ug-lm-pop"]'
        . '<div class="ug-lm" data-no-upsell>'
        . '<div class="ug-lm__intro">'
        . '<p class="ug-lm__title">Скачайте 2&nbsp;чек-листа для клиник</p>'
        . '<p class="ug-lm__text">Товарный знак и&nbsp;паспорт безопасности — самопроверка за&nbsp;10&nbsp;минут. '
        . 'После заполнения формы файлы откроются для скачивания, а&nbsp;юрист Артём Франк свяжется '
        . 'для бесплатной консультации по&nbsp;интеллектуальной собственности.</p>'
        . '</div>'
        . '[contact-form-7 id="' . $form_id . '" html_class="ug-lm-cf7"]'
        . '<div class="ug-lm__done" hidden>'
        . '<div class="ug-lm__check" aria-hidden="true">✓</div>'
        . '<p class="ug-lm__title">Готово! Чек-листы ваши</p>'
        . '<p class="ug-lm__text">Юрист Артём Франк свяжется с&nbsp;вами в&nbsp;рабочее время: '
        . 'с&nbsp;09:00 до&nbsp;18:00 по&nbsp;Новосибирску.</p>'
        . '<div class="ug-lm__files">' . $links . '</div>'
        . '</div>'
        . '</div>'
        . '[/lightbox]'
    );
});
