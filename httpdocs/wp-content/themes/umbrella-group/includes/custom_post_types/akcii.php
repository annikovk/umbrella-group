<?php
/*
* Adding post types support for 'akcii' posts.
*/

function akcii_custom_post_type()
{
// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Акции', 'Post Type General Name'),
        'singular_name' => _x('Акция', 'Post Type Singular Name'),
        'menu_name' => __('Акции'),
        'all_items' => __('Все акции'),
        'add_new_item' => __('Добавить новую акцию'),
        'add_new' => __('Добавить новую'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск акций'),
        'not_found' => __('Акций не найдено'),
    );

// Set other options for Custom Post Type
    $args = array(
        'label' => __('Акции'),
        'description' => __('Акции компании taxlab'),
        'labels' => $labels,
        'rewrite' => array('slug' => 'akcii'),
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-money-alt',
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );

// Registering your Custom Post Type
    register_post_type('akcii', $args);

}



// Add the custom columns to the akcii post type:
add_filter('manage_akcii_posts_columns', 'set_custom_edit_akcii_columns');
function set_custom_edit_akcii_columns($columns)
{
    unset($columns['date']);

    $columns['akcii_form_header'] = 'Заголовок формы';
    $columns['akcii_form_subheader'] = 'Подзаголовок формы';
    $columns['akcii_form_button_text'] = 'Кнопка формы';
    $columns['akcii_block_icon'] = 'Иконка блока';
    return $columns;
}

// Add the data to the custom columns for the akcii post type:
add_action('manage_akcii_posts_custom_column', 'custom_akcii_column', 10, 2);
function custom_akcii_column($column, $post_id)
{
    switch ($column) {

        case 'akcii_form_header' :
            print_r(get_post_meta($post_id, 'akcii_form_header', true));
            break;
        case 'akcii_form_subheader' :
            print_r(get_post_meta($post_id, 'akcii_form_subheader', true));
            break;
        case 'akcii_form_button_text' :
            print_r(get_post_meta($post_id, 'akcii_form_button_text', true));
            break;
        case 'akcii_block_icon' :
            print_r(get_post_meta($post_id, 'akcii_block_icon', true));
            break;
        case 'akcii_tags':
            $terms = get_the_term_list($post_id, 'akcii_tags', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

    }
}


add_action('init', 'akcii_custom_post_type');
add_action('init', function () {
    add_ux_builder_post_type('akcii');
});

function get_metabox_value($post)
{
    ?>
    <div class="hcf_box">
        <style scoped>
            .hcf_box {
                display: grid;
                grid-template-columns: max-content 1fr;
                grid-row-gap: 10px;
                grid-column-gap: 20px;
            }

            .hcf_field {
                display: contents;
            }
        </style>
        <p class="meta-options hcf_field">
            <label for="akcii_form_header">Заголовок формы</label>
            <input id="akcii_form_header"
                   type="text"
                   name="akcii_form_header"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'akcii_form_header', true)); ?>">
        </p>
        <p class="meta-options hcf_field">
            <label for="akcii_form_subheader">Подзаголовок формы</label>
            <input id="akcii_form_subheader"
                   type="text"
                   name="akcii_form_subheader"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'akcii_form_subheader', true)); ?>">
        </p>
        <p class="meta-options hcf_field">
            <label for="akcii_form_button_text">Текст кнопки формы</label>
            <input id="akcii_form_button_text"
                   type="text"
                   name="akcii_form_button_text"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'akcii_form_button_text', true)); ?>">
        </p>
        <p class="meta-options hcf_field">
            <label for="akcii_block_icon">Иконка блока</label>
            <input id="akcii_block_icon"
                   type="text"
                   name="akcii_block_icon"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'akcii_block_icon', true)); ?>">
        </p>
    </div>
    <?php
}

function akcii_save_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }
    $fields = [
        'akcii_form_header',
        'akcii_form_subheader',
        'akcii_form_button_text',
        'akcii_block_icon',
    ];
    foreach ( $fields as $field ) {
        if ( array_key_exists( $field, $_POST ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }
}
add_action( 'save_post', 'akcii_save_meta_box' );
add_action('add_meta_boxes', function () { add_meta_box('akcii_form_metabox', 'Контактная форма и блок', 'get_metabox_value', 'akcii', 'side');});

