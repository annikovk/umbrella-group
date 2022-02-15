<?php

function case_custom_post_type()
{
// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Кейсы', 'Post Type General Name'),
        'singular_name' => _x('Кейс', 'Post Type Singular Name'),
        'menu_name' => __('Кейсы'),
        'all_items' => __('Все кейсы'),
        'add_new_item' => __('Добавить новый кейс'),
        'add_new' => __('Добавить новый'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск кейсов'),
        'not_found' => __('Кейсов не найдено'),
    );

// Set other options for Custom Post Type
    $args = array(
        'label' => __('Кейсы'),
        'description' => __(''),
        'labels' => $labels,
        'rewrite' => array('slug' => 'case'),
        'supports' => array('title', 'thumbnail', 'page-attributes'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-excerpt-view',
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
    );

// Registering your Custom Post Type
    register_post_type('case', $args);

}


// Add the custom columns to the case post type:
add_filter('manage_case_posts_columns', 'set_custom_edit_case_columns');
function set_custom_edit_case_columns($columns)
{
    unset($columns['date']);

    $columns['case_branch'] = 'Направление';
    $columns['case_category'] = 'Категория';
    return $columns;
}

// Add the data to the custom columns for the case post type:
add_action('manage_case_posts_custom_column', 'custom_case_column', 10, 2);
function custom_case_column($column, $post_id)
{
    switch ($column) {
        case 'case_branch' :
            print_r(get_post_meta($post_id, 'case_branch', true));
            break;
        case 'case_category' :
            print_r(get_post_meta($post_id, 'case_category', true));
            break;
        case 'case_tags':
            $terms = get_the_term_list($post_id, 'case_tags', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

    }
}


add_action('init', 'case_custom_post_type');
add_action('init', function () {
    add_ux_builder_post_type('case');
});
function get_case_metabox_value($post)
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
            <label for="case_branch">Направление</label>
            <select id="case_branch" name="case_branch">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "-") {
                    echo "selected";
                }; ?> value="-">-
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "Бухгалтерия") {
                    echo "selected";
                }; ?> value="Бухгалтерия">Бухгалтерия
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "Аудит") {
                    echo "selected";
                }; ?> value="Аудит">Аудит
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "Юридические услуги") {
                    echo "selected";
                }; ?> value="Юридические услуги">Юридические услуги
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "Лицензирование") {
                    echo "selected";
                }; ?> value="Лицензирование">Лицензирование
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "Регистрация") {
                    echo "selected";
                }; ?> value="Регистрация">Регистрация
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_branch', true)) == "Ликвидация") {
                    echo "selected";
                }; ?> value="Ликвидация">Ликвидация
                </option>
            </select>
        </div>
        <div class="meta-options hcf_field">
            <label for="case_category">Категория</label>
            <input id="case_category"
                   type="text"
                   name="case_category"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_category', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_logo_url">Ссылка на лого автора / компании клиента</label>
            <input id="case_logo_url"
                   type="text"
                   name="case_logo_url"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_logo_url', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_author">Компания и автор отзыва</label>
            <input id="case_author"
                   type="text"
                   name="case_author"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_author', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_industry">Отрасль</label>
            <input id="case_industry"
                   type="text"
                   name="case_industry"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_industry', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_team">Сотрудники</label>
            <input id="case_team"
                   type="text"
                   name="case_team"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_team', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_issue">Проблема</label>
            <textarea id="case_issue"
                      type="text"
                      name="case_issue"><?php echo esc_attr(get_post_meta(get_the_ID(), 'case_issue', true)); ?></textarea>
        </div>
        <div class="meta-options hcf_field">
            <label for="case_solution">Решение</label>
            <input id="case_solution"
                   type="text"
                   name="case_solution"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_solution', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_proof">Вещдок</label>
            <input id="case_proof"
                   type="text"
                   name="case_proof"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_proof', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_proof_title">Подпись вещдока</label>
            <input id="case_proof_title"
                   type="text"
                   name="case_proof_title"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_proof_title', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_feedback_text">Текст ссылки отзыва</label>
            <input id="case_feedback_text"
                   type="text"
                   name="case_feedback_text"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_feedback_text', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_feedback_url">Ссылка на отзыв</label>
            <input id="case_feedback_url"
                   type="text"
                   name="case_feedback_url"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'case_feedback_url', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="case_common">Для общего</label>
            <select id="case_common" name="case_common">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_common', true)) == "Нет") {
                    echo "selected";
                }; ?> value="Нет">Нет
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'case_common', true)) == "Да") {
                    echo "selected";
                }; ?> value="Да">Да
                </option>
            </select>
        </div>

    </div>
    <?php
}

function case_save_meta_box($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ($parent_id = wp_is_post_revision($post_id)) {
        $post_id = $parent_id;
    }
    $fields = [
        'case_branch',
        'case_category',
        'case_author',
        'case_logo_url',
        'case_issue',
        'case_team',
        'case_solution',
        'case_industry',
        'case_common',
        'case_proof',
        'case_proof_title',
        'case_feedback_text',
        'case_feedback_url',
    ];
    foreach ($fields as $field) {
        if (array_key_exists($field, $_POST)) {
            update_post_meta($post_id, $field, $_POST[$field]);
        }
    }
}

add_action('save_post', 'case_save_meta_box');
add_action('add_meta_boxes', function () {
    add_meta_box('case_form_metabox', 'Данные', 'get_case_metabox_value', 'case', 'side');
});