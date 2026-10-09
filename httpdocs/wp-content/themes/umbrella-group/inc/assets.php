<?php
/**
 * Подключение ассетов темы.
 *
 * Перенесено из functions.php:163-186, 251-257 дословно (WS-0): шрифты,
 * form-custom.css, js/script.js и коллектор css/css_collector.php (инлайн-CSS/JS
 * на wp_footer:100). Владелец — WS-2: здесь появляются реестр и API
 * ug_enqueue_style()/ug_enqueue_script() (спека §3.2), коллектор уходит.
 */

function add_custom_fonts()
{
    wp_enqueue_style('Inter-font', '/wp-content/themes/umbrella-group/fonts/inter.css');
    wp_enqueue_style('fonts-configuration', '/wp-content/themes/umbrella-group/fonts/fonts-configuration.css');
}

add_action('wp_enqueue_scripts', 'add_custom_fonts');

// Подключение стилей и скриптов для кастомной формы

add_action('wp_enqueue_scripts', 'custom_form_styles');

function custom_form_styles() 
{
	wp_enqueue_style('custom-form-style', '/wp-content/themes/umbrella-group/css/form-custom.css', array(), 5.4);
}


add_action('wp_enqueue_scripts', 'true_include_mailscript');
 
function true_include_mailscript() {
	wp_enqueue_script( 'mailscript', get_stylesheet_directory_uri() . '/js/script.js', array(), 4.1, true);
}

require get_theme_file_path() . '/css/css_collector.php';
function get_umbrella_customizations()
{
    $customizations = new umbrella_customization();
    echo $customizations->get_umbrella_custom_css() . $customizations->get_umbrella_custom_scripts();
}
add_action('wp_footer','get_umbrella_customizations', 100);
