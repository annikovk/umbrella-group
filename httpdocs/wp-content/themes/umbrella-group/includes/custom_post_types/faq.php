<?php

function faq_custom_post_type()
{
// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Вопросы', 'Post Type General Name'),
        'singular_name' => _x('Вопрос', 'Post Type Singular Name'),
        'menu_name' => __('Вопросы'),
        'all_items' => __('Все вопросы'),
        'add_new_item' => __('Добавить новый вопрос'),
        'add_new' => __('Добавить новый'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск вопросов'),
        'not_found' => __('Вопросов не найдено'),
    );

// Set other options for Custom Post Type
    $args = array(
        'label' => __('Вопросы'),
        'description' => __('Часто задаваемые вопросы'),
        'labels' => $labels,
        'rewrite' => array('slug' => 'faq'),
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-pressthis',
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );

// Registering your Custom Post Type
    register_post_type('faq', $args);

}


// Add the custom columns to the faq post type:
add_filter('manage_faq_posts_columns', 'set_custom_edit_faq_columns');
function set_custom_edit_faq_columns($columns)
{
    unset($columns['date']);

    $columns['faq_branch'] = 'Направление';
    $columns['faq_author'] = 'Автор';
    return $columns;
}

// Add the data to the custom columns for the faq post type:
add_action('manage_faq_posts_custom_column', 'custom_faq_column', 10, 2);
function custom_faq_column($column, $post_id)
{
    switch ($column) {

        case 'faq_branch' :
            print_r(get_post_meta($post_id, 'faq_branch', true));
            break;
        case 'faq_author' :
            print_r(get_post_meta($post_id, 'faq_author', true));
            break;
        case 'faq_tags':
            $terms = get_the_term_list($post_id, 'faq_tags', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

    }
}


add_action('init', 'faq_custom_post_type');
add_action('init', function () {
    add_ux_builder_post_type('faq');
});
function get_faq_metabox_value($post)
{
    ?>
    <div class="hcf_box">
        <style scoped>
            .hcf_box {
                display: grid;
                /*grid-template-columns: max-content 100%;*/
                grid-row-gap: 10px;
                grid-column-gap: 20px;
            }

            .hcf_field {
                display: contents;
            }
        </style>
        <p class="meta-options hcf_field">
            <label for="faq_branch">Направление</label>
            <select id="faq_branch" name="faq_branch">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'faq_branch', true))=="-") {echo "selected";}; ?> value="-">-</option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'faq_branch', true))=="Бухгалтерия") {echo "selected";}; ?> value="Бухгалтерия">Бухгалтерия</option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'faq_branch', true))=="Аудит") {echo "selected";}; ?> value="Аудит">Аудит</option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'faq_branch', true))=="Юридические услуги") {echo "selected";}; ?> value="Юридические услуги">Юридические услуги</option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'faq_branch', true))=="Лицензирование") {echo "лицензирование";}; ?> value="Лицензирование">Лицензирование</option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'faq_branch', true))=="Регистрация и ликвидация") {echo "selected";}; ?> value="Регистрация и ликвидация">Регистрация и ликвидация</option>
            </select>
        </p>
        <div class="meta-options hcf_field">
            <label for="faq_author">Автор</label>
            <textarea id="faq_author"
                   type="text"
                   name="faq_author"><?php echo esc_attr(get_post_meta(get_the_ID(), 'faq_author', true)); ?>
            </textarea>
        </div>
    </div>
    <?php
}

function faq_save_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }
    $fields = [
        'faq_branch',
        'faq_author',
    ];
    foreach ( $fields as $field ) {
        if ( array_key_exists( $field, $_POST ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }
}
add_action( 'save_post', 'faq_save_meta_box' );
add_action('add_meta_boxes', function () { add_meta_box('faq_form_metabox', 'Контактная форма и блок', 'get_faq_metabox_value', 'faq', 'side');});

