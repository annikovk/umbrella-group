<?php

function result_custom_post_type()
{
// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Результаты клиентов', 'Post Type General Name'),
        'singular_name' => _x('Результат', 'Post Type Singular Name'),
        'menu_name' => __('Результаты'),
        'all_items' => __('Все результаты'),
        'add_new_item' => __('Добавить новый результат'),
        'add_new' => __('Добавить новый'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск результата'),
        'not_found' => __('Результат не найдено'),
    );

// Set other options for Custom Post Type
    $args = array(
        'label' => __('Результаты'),
        'description' => __('Результаты других клиентов'),
        'labels' => $labels,
        'rewrite' => array('slug' => 'result'),
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-pressthis',
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );

// Registering your Custom Post Type
    register_post_type('result', $args);

}
add_action( 'init', 'result_custom_post_type' );
?>