<?php

function specialist_custom_post_type()
{
// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Специалисты', 'Post Type General Name'),
        'singular_name' => _x('Специалист', 'Post Type Singular Name'),
        'menu_name' => __('Специалисты'),
        'all_items' => __('Все специалисты'),
        'add_new_item' => __('Добавить нового специалиста'),
        'add_new' => __('Добавить нового'),
        'edit_item' => __('Редактировать'),
        'update_item' => __('Обновить'),
        'search_items' => __('Поиск специалистов'),
        'not_found' => __('Специалистов не найдено'),
    );

// Set other options for Custom Post Type
    $args = array(
        'label' => __('Специалисты'),
        'description' => __(''),
        'labels' => $labels,
        'rewrite' => array('slug' => 'specialist'),
        'supports' => array('title', 'thumbnail'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-businessperson',
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability_type' => 'post',
    );

// Registering your Custom Post Type
    register_post_type('specialist', $args);

}


// Add the custom columns to the specialist post type:
add_filter('manage_specialist_posts_columns', 'set_custom_edit_specialist_columns');
function set_custom_edit_specialist_columns($columns)
{
    unset($columns['date']);

    $columns['specialist_branch'] = 'Направление';
    $columns['specialist_name'] = 'Имя Отчество';
    return $columns;
}

// Add the data to the custom columns for the specialist post type:
add_action('manage_specialist_posts_custom_column', 'custom_specialist_column', 10, 2);
function custom_specialist_column($column, $post_id)
{
    switch ($column) {

        case 'specialist_branch' :
            print_r(get_post_meta($post_id, 'specialist_branch', true));
            break;
        case 'specialist_name' :
            print_r(get_post_meta($post_id, 'specialist_name', true));
            break;
        case 'specialist_tags':
            $terms = get_the_term_list($post_id, 'specialist_tags', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                echo '';
            break;

    }
}


add_action('init', 'specialist_custom_post_type');
add_action('init', function () {
    add_ux_builder_post_type('specialist');
});
function get_specialist_metabox_value($post)
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
            <label for="specialist_branch">Направление</label>
            <select id="specialist_branch" name="specialist_branch">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "-") {
                    echo "selected";
                }; ?> value="-">-
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "Бухгалтерия") {
                    echo "selected";
                }; ?> value="Бухгалтерия">Бухгалтерия
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "Аудит") {
                    echo "selected";
                }; ?> value="Аудит">Аудит
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "Юридические услуги") {
                    echo "selected";
                }; ?> value="Юридические услуги">Юридические услуги
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "Лицензирование") {
                    echo "selected";
                }; ?> value="Лицензирование">Лицензирование
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "Регистрация и ликвидация") {
                    echo "selected";
                }; ?> value="Регистрация и ликвидация">Регистрация и ликвидация
                </option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_branch', true)) == "Сделки с недвижимостью") {
                    echo "selected";
                }; ?> value="Сделки с недвижимостью">Сделки с недвижимостью
                </option>
            </select>
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_name">Имя Отчество</label>
            <input id="specialist_name"
                   type="text"
                   name="specialist_name"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_name', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_position">Должность</label>
            <input id="specialist_position"
                   type="text"
                   name="specialist_position"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_position', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_experience">Опыт</label>
            <input id="specialist_experience"
                   type="text"
                   name="specialist_experience"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_experience', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_description">Описание</label>
            <textarea id="specialist_description"
                      type="text"
                      name="specialist_description"><?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_description', true)); ?></textarea>
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_education">Образование</label>
            <input id="specialist_education"
                   type="text"
                   name="specialist_education"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_education', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_specialization">Специализация</label>
            <input id="specialist_specialization"
                   type="text"
                   name="specialist_specialization"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_specialization', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_achievements">Достижения</label>
            <input id="specialist_achievements"
                   type="text"
                   name="specialist_achievements"
                   value="<?php echo get_post_meta(get_the_ID(), 'specialist_achievements', true); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_office">Офис</label>
            <input id="specialist_office"
                   type="text"
                   name="specialist_office"
                   value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'specialist_office', true)); ?>">
        </div>
        <div class="meta-options hcf_field">
            <label for="specialist_main">Ведущий</label>
            <select id="specialist_main" name="specialist_main">
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_main', true))=="Нет") {echo "selected";}; ?> value="Нет">Нет</option>
                <option <?php if (esc_attr(get_post_meta(get_the_ID(), 'specialist_main', true))=="Да") {echo "selected";}; ?> value="Да">Да</option>
            </select>
        </div>

    </div>
    <?php
}

function specialist_save_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }
    $fields = [
        'specialist_branch',
        'specialist_office',
        'specialist_description',
        'specialist_education',
        'specialist_specialization',
        'specialist_achievements',
        'specialist_name',
        'specialist_position',
        'specialist_experience',
        'specialist_main',
    ];
    foreach ( $fields as $field ) {
        if ( array_key_exists( $field, $_POST ) ) {
            update_post_meta($post_id, $field, $_POST[$field]);
        }
    }
}
add_action( 'save_post', 'specialist_save_meta_box' );
add_action('add_meta_boxes', function () { add_meta_box('specialist_form_metabox', 'Данные', 'get_specialist_metabox_value', 'specialist', 'side');});

