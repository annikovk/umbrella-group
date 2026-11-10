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
 *    jQuery-блок в начале <body>, теперь defer-файл после jQuery;
 *  - плавающих кнопок Telegram/Max справа внизу больше нет (2026-11-10):
 *    мессенджеры — в выпадашке шапки и в мобильной панели (header-site.php,
 *    element-sidebar-extra.php). Их CSS .telegram-button/.max-button остался
 *    в assets/css/main.css без разметки.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

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

// Шапка default (template-parts/header/header-site.php): CSS в <head> сразу
// после ug-main и тем же якорем в конце <body> — перекрывает custom-css
// Flatsome из theme_mods; JS — defer в подвале.
add_action('wp_enqueue_scripts', static function (): void {
    if (ug_header_variant() !== 'default') {
        return;
    }
    if (is_404()) {
        // 404.php без get_footer(): ни якоря в <body>, ни скриптов подвала —
        // CSS обычной ссылкой в <head>, JS туда же (defer)
        wp_enqueue_style('ug-header-site', get_theme_file_uri('assets/css/header-site.css'), [], ug_asset_ver('assets/css/header-site.css'));
        wp_enqueue_script('ug-header-site-js', get_theme_file_uri('assets/js/header-site.js'), [], ug_asset_ver('assets/js/header-site.js'), ['in_footer' => false, 'strategy' => 'defer']);
        return;
    }
    ug_head_style('assets/css/header-site.css');
    ug_enqueue_script('assets/js/header-site.js', [], ['in_footer' => true, 'strategy' => 'defer']);
});

add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_script(
        'ug-roistat-links',
        get_theme_file_uri('assets/js/roistat-links.js'),
        ['jquery'],
        ug_asset_ver('assets/js/roistat-links.js'),
        ['in_footer' => true, 'strategy' => 'defer']
    );
});

