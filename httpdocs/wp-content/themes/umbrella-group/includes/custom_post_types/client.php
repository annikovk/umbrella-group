<?php
/*
* Adding post types support for 'clients' posts.
*/

function client_post_type()
{
    $labels = array(
        'name' => _x('Клиенты', 'Post Type General Name'),
        'singular_name' => _x('Клиент', 'Post Type Singular Name'),
        'menu_name' => __('Клиенты'),
        'all_items' => __('Все клиенты'),
        'add_new_item' => __('Добавить нового клиента'),
        'add_new' => __('Добавить нового'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск клиенты'),
        'not_found' => __('Клиентов не найдено'),
    );
    $args = array(
        'label' => __('Клиенты'),
        'description' => __('Клиенты и их отзывы'),
        'labels' => $labels,
        'rewrite' => array('slug' => 'client'),
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'),
        'taxonomies' => array('industry', 'category'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-admin-users',
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type('client', $args);
    add_filter('manage_client_posts_columns', 'set_custom_edit_client_columns');
    add_action('manage_client_posts_custom_column', 'custom_client_column', 10, 2);

    new client_shortcode_options();


}

function register_taxonomies_for_client_post_type()
{
    $labels_industry = array(
        'name' => _x('Отрасль', 'taxonomy general name'),
        'singular_name' => _x('Отрасль', 'taxonomy singular name'),
        'search_items' => __('Поиск отрасли'),
        'all_items' => __('Все отралси'),
        'parent_item' => __('Parent'),
        'parent_item_colon' => __('Parent:'),
        'edit_item' => __('Редактировать отрасль'),
        'update_item' => __('Обновить отрасль'),
        'add_new_item' => __('Добавить новую отрасль'),
        'new_item_name' => __('Новая отрасль'),
        'menu_name' => __('Отрасли'),
        'not_found' => __('Отраслей не найдено'),
    );
    $args_industry = array(
        'hierarchical' => true,
        'label' => 'Отрасль',
        'labels' => $labels_industry,
    );
    $labels_category = array(
        'name' => _x('Отображать на', 'taxonomy general name'),
        'singular_name' => _x('Отображать на', 'taxonomy singular name'),
        'search_items' => __('Поиск отметок'),
        'all_items' => __('Все отметки'),
        'parent_item' => __('Parent'),
        'parent_item_colon' => __('Parent:'),
        'edit_item' => __('Редактировать отметку'),
        'update_item' => __('Обновить отметку'),
        'add_new_item' => __('Добавить новую отметку'),
        'new_item_name' => __('Новая отметка'),
        'menu_name' => __('Отображение'),
        'not_found' => __('Не найдено'),
    );
    $args_category = array(
        'hierarchical' => false,
        'label' => 'Отображать на',
        'labels' => $labels_category,
    );
    $labels_tags = array(
        'name' => _x('Специальные отметки', 'taxonomy general name'),
        'singular_name' => _x('Отметка', 'taxonomy singular name'),
        'search_items' => __('Поиск отметок'),
        'all_items' => __('Все отметки'),
        'edit_item' => __('Редактировать отметку'),
        'update_item' => __('Обновить отметку'),
        'add_new_item' => __('Добавить новую отметку'),
        'new_item_name' => __('Новая отметка'),
        'menu_name' => __('Специальные отметки'),
        'not_found' => __('Отметок не найдено'),
    );
    $args_tags = array(
        'hierarchical' => false,
        'label' => 'Отметки',
        'labels' => $labels_tags,
    );
//    register_taxonomy('client_tags', array('client'), $args_tags);
    register_taxonomy('client_industry', array('client'), $args_industry);
    register_taxonomy('client_category', array('client'), $args_category);
}

function set_custom_edit_client_columns($columns)
{
    unset($columns['date']);

    $columns['client_industry'] = 'Отрасль';
    $columns['client_category'] = 'Отображать на';
    $columns['umbrella_client_personal_industry'] = 'Индивидуальная отрсаль';
    $columns['umbrella_link_to_client_website'] = 'Сайт';
    $columns['is_feedback'] = 'Отзыв';
    $columns['umbrella_feedback_scan'] = 'Скан отзыва';
//    $columns['client_tags'] = 'Специальные отметки';

    return $columns;
}

function custom_client_column($column, $post_id)
{
    switch ($column) {

        case 'client_industry' :
            $terms = get_the_term_list($post_id, 'client_industry', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;
        case 'client_category' :
            $terms = get_the_term_list($post_id, 'client_category', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

        case 'umbrella_feedback_scan' :
            print_r(get_post_meta($post_id, 'umbrella_feedback_scan', true));
            break;
        case 'is_feedback' :
            if (get_the_excerpt($post_id)) {
                echo 'есть';
            }
            break;
        case 'umbrella_client_personal_industry' :
            print_r(get_post_meta($post_id, 'umbrella_client_personal_industry', true));
            break;
        case 'umbrella_link_to_client_website' :
            if (!empty(get_post_meta($post_id, 'umbrella_link_to_client_website', true))) {
                echo '<a href="' . get_post_meta($post_id, 'umbrella_link_to_client_website', true) . '"  target="_blank">перейти на сайт </a>';
            }
            break;
        case 'client_tags':
            $terms = get_the_term_list($post_id, 'client_tags', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

    }
}

class client_shortcode_options
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_submenu_page_to_post_type'));
        add_action('admin_init', array($this, 'sub_menu_page_init'));
    }

    /**
     * Add sub menu page to the custom post type
     */
    public function add_submenu_page_to_post_type()
    {
        add_submenu_page(
            'edit.php?post_type=client',
            'Опции шорткода',
            'Шорткод',
            'manage_options',
            'projects_archive',
            array($this, 'client_options_display'));
    }

    /**
     * Options page callback
     */
    public function client_options_display()
    {
        $this->options = get_option('client_shortcode_options');

//        wp_enqueue_media();

        echo '<div class="wrap">';

        printf('<h1>%s</h1>', 'Натсройки отзывов');

        echo '<form method="post" action="options.php">';

        settings_fields('projects_archive');

        do_settings_sections('client_shortcode_options_page');

        submit_button();

        echo '</form></div>';
    }

    /**
     * Register and add settings
     */
    public function sub_menu_page_init()
    {
        register_setting(
            'projects_archive', // Option group
            'client_shortcode_options', // Option name
//        array($this,'sanitize'),
        );

        add_settings_section(
            'header_settings_section', // ID
            'Опции шорткода [umbrella_feedback]', // Title
            array($this, 'print_section_info'), // Callback
            'client_shortcode_options_page', // Page
        );

        add_settings_field(
            'excluded_pages', // ID
            'Исключить вывод шорткода на', // Title
            array($this, 'excluded_pages_callback'), // Callback
            'client_shortcode_options_page', // Page
            'header_settings_section' // Section
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        print_r($input);
        $new_input = array();

        if (isset($input['excluded_pages']))
            $new_input['excluded_pages'] = absint($input['excluded_pages']);

        if (isset($input['image_attachment_id']))
            $new_input['image_attachment_id'] = absint($input['image_attachment_id']);

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {

    }

    /**
     * Get the settings option array and print one of its values
     */
    public function excluded_pages_callback()
    {
        $pages = get_pages();
        foreach ($pages as $page) {
            $id = $page->ID;
            $selected = isset($this->options) && strlen($this->options > 0) && in_array($id, $this->options['excluded_pages']) ? 'selected' : '';
            if (!isset($options)) $options = '';
            $options .= '<option value="' . $id . '" ' . $selected . '>';
            $options .= $page->post_title;
            $options .= '</option>';
        }
        $pages = <<<EOHTML
                        <select  multiple="multiple" class="client_shortcode_options" name="client_shortcode_options[excluded_pages][]"> 
                            $options
                        </select>
                        <script src="/wp-content/themes/umbrella-group/assets/js/admin/chosen.jquery.min.js"></script>
                        <link rel="stylesheet" href="/wp-content/themes/umbrella-group/assets/css/admin/chosen.min.css">
                        <script>
                            jQuery(document).ready(function() {
                                jQuery('select.client_shortcode_options').chosen();
                            });
                        </script>
                    EOHTML;
        echo $pages;

    }
}

add_action('init', 'client_post_type');
add_action('init', 'register_taxonomies_for_client_post_type');
add_action('init', function () {
    add_ux_builder_post_type('client');
});