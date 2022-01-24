<?php

function feedback_custom_post_type()
{
// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Отзывы', 'Post Type General Name'),
        'singular_name' => _x('Отзыв', 'Post Type Singular Name'),
        'menu_name' => __('Отзывы'),
        'all_items' => __('Все отзывы'),
        'add_new_item' => __('Добавить новый отзыв'),
        'add_new' => __('Добавить новый'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск отзывов'),
        'not_found' => __('Отзывов не найдено'),
    );

// Set other options for Custom Post Type
    $args = array(
        'label' => __('Отзывы'),
        'description' => __(''),
        'labels' => $labels,
        'rewrite' => array('slug' => 'feedback'),
        'supports' => array('title', 'thumbnail', 'page-attributes'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-feedback',
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
    );

// Registering your Custom Post Type
    register_post_type('feedback', $args);

}


// Add the custom columns to the feedback post type:
add_filter('manage_feedback_posts_columns', 'set_custom_edit_feedback_columns');
function set_custom_edit_feedback_columns($columns)
{
    unset($columns['date']);

    $columns['feedback_branch'] = 'Направление';
    $columns['feedback_category'] = 'Категория';
    return $columns;
}

// Add the data to the custom columns for the feedback post type:
add_action('manage_feedback_posts_custom_column', 'custom_feedback_column', 10, 2);
function custom_feedback_column($column, $post_id)
{
    switch ($column) {
        case 'feedback_branch' :
            print_r(get_post_meta($post_id, 'feedback_branch', true));
            break;
        case 'feedback_category' :
            print_r(get_post_meta($post_id, 'feedback_category', true));
            break;
        case 'feedback_tags':
            $terms = get_the_term_list($post_id, 'feedback_tags', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

    }
}


add_action('init', 'feedback_custom_post_type');
add_action('init', function () {
    add_ux_builder_post_type('feedback');
});
function get_feedback_metabox_value($post)
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
        <div class="meta-options hcf_field">
            <label for="feedback_branch">Направление</label>
            <select id="feedback_branch" name="feedback_branch">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "-") {
                    echo "selected";
                }; ?> value="-">-
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "Бухгалтерия") {
                    echo "selected";
                }; ?> value="Бухгалтерия">Бухгалтерия
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "Аудит") {
                    echo "selected";
                }; ?> value="Аудит">Аудит
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "Юридические услуги") {
                    echo "selected";
                }; ?> value="Юридические услуги">Юридические услуги
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "Лицензирование") {
                    echo "selected";
                }; ?> value="Лицензирование">Лицензирование
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "Регистрация") {
                    echo "selected";
                }; ?> value="Регистрация">Регистрация
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_branch', true)) == "Ликвидация") {
                    echo "selected";
                }; ?> value="Ликвидация">Ликвидация
                </option>
            </select>
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_category">Категория</label>
            <input id="feedback_category"
                   type="text"
                   name="feedback_category"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_category', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_logo_url">Ссылка на лого автора / компании клиента</label>
            <input id="feedback_logo_url"
                   type="text"
                   name="feedback_logo_url"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_logo_url', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_author">Компания и автор отзыва</label>
            <input id="feedback_author"
                   type="text"
                   name="feedback_author"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_author', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_quote">Цитата</label>
            <textarea id="feedback_quote"
                      type="text"
                      name="feedback_quote"><?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_quote', true)); ?></textarea>
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_proof">Картинка-вещдок / отзыв на бланке</label>
            <input id="feedback_proof"
                   type="text"
                   name="feedback_proof"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_proof', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_proof_title">Подпись вещдока/картинки</label>
            <input id="feedback_proof_title"
                   type="text"
                   name="feedback_proof_title"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_proof_title', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_url">Ссылка на Фламп/Яндекс</label>
            <input id="feedback_url"
                   type="text"
                   name="feedback_url"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_url', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_url_text">Текст ссылки отзыва</label>
            <input id="feedback_url_text"
                   type="text"
                   name="feedback_url_text"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'feedback_url_text', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="feedback_common">Для общего</label>
            <select id="feedback_common" name="feedback_common">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_common', true)) == "Нет") {
                    echo "selected";
                }; ?> value="Нет">Нет
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'feedback_common', true)) == "Да") {
                    echo "selected";
                }; ?> value="Да">Да
                </option>
            </select>
        </div>

    </div>
    <?php
}

function feedback_save_meta_box($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ($parent_id = wp_is_post_revision($post_id)) {
        $post_id = $parent_id;
    }
    $fields = [
        'feedback_branch',
        'feedback_category',
        'feedback_author',
        'feedback_logo_url',
        'feedback_quote',
        'feedback_common',
        'feedback_proof',
        'feedback_proof_title',
        'feedback_url_text',
        'feedback_url',
    ];
    foreach ($fields as $field) {
        if (array_key_exists($field, $_POST)) {
            update_post_meta($post_id, $field, $_POST[$field]);
        }
    }
}

add_action('save_post', 'feedback_save_meta_box');
add_action('add_meta_boxes', function () {
    add_meta_box('feedback_form_metabox', 'Данные', 'get_feedback_metabox_value', 'feedback', 'side');
});