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
        'capability' => 'edit_posts',
        'redirect' => false
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Кейс из нашей практики',
        'menu_title' => 'Кейс из нашей практики',
        'menu_slug' => 'theme-case-bus',
        'parent_slug' => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Отзывы клиентов',
        'menu_title' => 'Отзывы клиентов',
        'menu_slug' => 'theme-reviews-bus',
        'parent_slug' => 'theme-general-settings',
    ));


}
