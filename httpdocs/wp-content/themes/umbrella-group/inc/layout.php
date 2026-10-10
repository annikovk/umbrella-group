<?php
/**
 * Шапка и подвал: хуки вокруг header.php / footer.php (спека 1, WS-3).
 *
 *  - вариант шапки: 'business' у шаблона page-bussnise.php (он ставит query var
 *    ug_header_variant до get_header()), иначе 'default';
 *  - CSS шапки assets/css/header.css — бывший инлайн-<style> header.php
 *    (ul.tabs, h1 и первый экран услуг на ≤ 849px), первым в очереди стилей:
 *    раньше он стоял до wp_head(), то есть раньше всех <link>. У варианта
 *    business этого <style> не было — и CSS не подключается;
 *  - viewport: один meta, без maximum-scale/user-scalable=no (a11y; масштаб
 *    пальцами снова работает). Раньше их было два: свой в header.php и
 *    Flatsome на wp_head:1 — оба запрещали масштаб;
 *  - ссылки «Скачать анкету/памятку» → roistat.event.send: бывший
 *    jQuery-блок в начале <body>, теперь defer-файл после jQuery.
 */

declare(strict_types=1);

function ug_header_variant(): string
{
    return get_query_var('ug_header_variant') === 'business' ? 'business' : 'default';
}

add_filter('flatsome_viewport_meta', static fn(): string => '<meta name="viewport" content="width=device-width, initial-scale=1" />');

add_action('wp_enqueue_scripts', static function (): void {
    if (ug_header_variant() === 'default') {
        wp_enqueue_style('ug-header', get_theme_file_uri('assets/css/header.css'), [], ug_asset_ver('assets/css/header.css'));
    }
}, 0);

add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_script(
        'ug-roistat-links',
        get_theme_file_uri('assets/js/roistat-links.js'),
        ['jquery'],
        ug_asset_ver('assets/js/roistat-links.js'),
        ['in_footer' => true, 'strategy' => 'defer']
    );
});
