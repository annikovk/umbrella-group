<?php
/**
 * Настройки темы и платформы: классический редактор, excerpt, redirect_canonical
 * для 404, mime webp, страницы опций ACF.
 *
 * Перенесено из functions.php:144-146, 154-161, 259-264, 1068-1093 дословно (WS-0).
 * Владелец дальнейших правок — WS-2 (отключение block-library, emoji и т.п.).
 */

add_filter('use_block_editor_for_post', '__return_false', 10);
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
// Add custom Theme Functions here

function remove_redirect_guess_404_permalink($redirect_url)
{
    if (is_404() && !isset($_GET['p']))
        return false;
    return $redirect_url;
}

add_filter('redirect_canonical', 'remove_redirect_guess_404_permalink');

//** *Enable upload for webp image files.*/
function webp_upload_mimes($existing_mimes) {
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
}
add_filter('mime_types', 'webp_upload_mimes');

if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Основные настройки',
        'menu_title' => 'Настройки темы',
        'menu_slug' => 'theme-general-settings',
        // Тексты и WhatsApp на всех страницах: администратор и редактор
        // (их 6 и 2), автор и участник — нет (было edit_posts).
        'capability' => 'edit_others_posts',
        'redirect' => false
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Кейс из нашей практики',
        'menu_title' => 'Кейс из нашей практики',
        'menu_slug' => 'theme-case-bus',
        'parent_slug' => 'theme-general-settings',
        'capability' => 'edit_others_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Отзывы клиентов',
        'menu_title' => 'Отзывы клиентов',
        'menu_slug' => 'theme-reviews-bus',
        'parent_slug' => 'theme-general-settings',
        'capability' => 'edit_others_posts',
    ));


}


/**
 * Лишний CSS/JS ядра и Flatsome на фронте (WS-2, спека §3.4, шаг 7).
 *
 * - wp-block-library (113 КБ), classic-theme-styles, global-styles (8,9 КБ):
 *   редактор блоков выключен, в HTML 602 рендеров нет ни одного класса
 *   wp-block-* / has-*-color / is-layout-*, в CSS темы, Flatsome и CF7 нет
 *   var(--wp--preset…). То же, что theme_mod disable_blockcss, но для всех
 *   трёх хэндлов и без настройки в БД.
 * - Эмодзи: скрипт-детектор (wp_head:7), стили и dns-prefetch s.w.org — то же,
 *   что theme_mod disable_emoji. Эмодзи в тексте остаются символами.
 * - flatsome_add_fallbacks: условные комментарии <!--[if IE]> с html5shiv и
 *   ie-flexibility — IE их только и читает.
 */
add_action('wp_enqueue_scripts', function (): void {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');
}, 100);
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
remove_action('wp_print_styles', 'print_emoji_styles');
add_filter('emoji_svg_url', '__return_false');

// Родительская тема грузится после дочерней — снимаем её хук, когда он уже стоит.
add_action('after_setup_theme', function (): void {
    remove_action('wp_head', 'flatsome_add_fallbacks');
});
