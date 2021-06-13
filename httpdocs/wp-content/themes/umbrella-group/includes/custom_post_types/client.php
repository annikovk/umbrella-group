<?php
/*
* Adding post types support for 'clients' posts.
*/

function custom_post_type() {

// Set UI labels for Custom Post Type
$labels = array(
'name'                => _x( 'Клиенты', 'Post Type General Name' ),
'singular_name'       => _x( 'Клиент', 'Post Type Singular Name' ),
'menu_name'           => __( 'Клиенты' ),
'all_items'           => __( 'Все клиенты' ),
'add_new_item'        => __( 'Добавить нового клиента' ),
'add_new'             => __( 'Добавить нового' ),
'edit_item'           => __( 'Редактировать' ),
'update_item'         => __( 'Обновить' ),
'search_items'        => __( 'Поиск клиенты' ),
'not_found'           => __( 'Клиентов не найдено' ),
);

// Set other options for Custom Post Type

$args = array(
'label'               => __( 'Клиенты' ),
'description'         => __( 'Клиенты и их отзывы'),
'labels'              => $labels,
'rewrite'            => array( 'slug' => 'client' ),
// Features this CPT supports in Post Editor
'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'),
'taxonomies'          => array('industry'),
// You can associate this CPT with a taxonomy or custom taxonomy.
//'taxonomies'          => array( 'industry' ),
/* A hierarchical CPT is like Pages and can have
* Parent and child items. A non-hierarchical CPT
* is like Posts.
*/
'hierarchical'        => false,
'public'              => true,
'show_ui'             => true,
'show_in_menu'        => true,
'show_in_nav_menus'   => true,
'show_in_admin_bar'   => true,
'menu_position'       => 5,
'menu_icon'           => 'dashicons-admin-users',
'can_export'          => true,
'has_archive'         => true,
'exclude_from_search' => false,
'publicly_queryable'  => true,
'capability_type'     => 'post',
);

// Registering your Custom Post Type
register_post_type( 'client', $args );

}


function register_taxonomies_for_client_post_type() {
$labels_industry = array(
'name' => _x( 'Отрасль', 'taxonomy general name' ),
'singular_name' => _x( 'Отрасль', 'taxonomy singular name' ),
'search_items' =>  __( 'Поиск отрасли' ),
'all_items' => __( 'Все отралси' ),
'parent_item' => __( 'Parent' ),
'parent_item_colon' => __( 'Parent:' ),
'edit_item' => __( 'Редактировать отрасль' ),
'update_item' => __( 'Обновить отрасль' ),
'add_new_item' => __( 'Добавить новую отрасль' ),
'new_item_name' => __( 'Новая отрасль' ),
'menu_name' => __( 'Отрасли' ),
'not_found' => __( 'Отраслей не найдено' ),
);
$args_industry = array(
'hierarchical' => true,
'label' => 'Отрасль',
'labels' => $labels_industry,
);

$labels_tags = array(
'name' => _x( 'Специальные отметки', 'taxonomy general name' ),
'singular_name' => _x( 'Отметка', 'taxonomy singular name' ),
'search_items' =>  __( 'Поиск отметок' ),
'all_items' => __( 'Все отметки' ),
'edit_item' => __( 'Редактировать отметку' ),
'update_item' => __( 'Обновить отметку' ),
'add_new_item' => __( 'Добавить новую отметку' ),
'new_item_name' => __( 'Новая отметка' ),
'menu_name' => __( 'Специальные отметки' ),
'not_found' => __( 'Отметок не найдено' ),
);
$args_tags = array(
'hierarchical' => false,
'label' => 'Отметки',
'labels' => $labels_tags,
);
register_taxonomy( 'client_tags', array( 'client' ), $args_tags );
register_taxonomy( 'client_industry', array( 'client' ), $args_industry );
}

// Add the custom columns to the client post type:
add_filter( 'manage_client_posts_columns', 'set_custom_edit_client_columns' );
function set_custom_edit_client_columns($columns) {
unset( $columns['date'] );

$columns['client_industry'] = 'Отрасль';
$columns['umbrella_client_personal_industry'] = 'Индивидуальная отрсаль';
$columns['umbrella_link_to_client_website'] = 'Сайт';
$columns['is_feedback'] = 'Отзыв';
$columns['umbrella_feedback_scan'] = 'Скан отзыва';
$columns['client_tags'] = 'Специальные отметки';

return $columns;
}

// Add the data to the custom columns for the client post type:
add_action( 'manage_client_posts_custom_column' , 'custom_client_column', 10, 2 );
function custom_client_column( $column, $post_id ) {
switch ( $column ) {

case 'client_industry' :
$terms = get_the_term_list( $post_id , 'client_industry' , '' , ',' , '' );
if ( is_string( $terms ) )
echo $terms;
else
echo '';
break;

case 'umbrella_feedback_scan' :
print_r (get_post_meta( $post_id , 'umbrella_feedback_scan' , true ));
break;
case 'is_feedback' :
if (get_the_excerpt($post_id)) { echo 'есть';}
break;
case 'umbrella_client_personal_industry' :
print_r (get_post_meta( $post_id , 'umbrella_client_personal_industry' , true ));
break;
case 'umbrella_link_to_client_website' :
//echo  get_post_meta( $post_id , 'umbrella_link_to_client_website' , true );
if (!empty( get_post_meta( $post_id , 'umbrella_link_to_client_website' , true ))) {
echo '<a href="' . get_post_meta( $post_id , 'umbrella_link_to_client_website' , true ) . '"  target="_blank">перейти на сайт </a>';
}
break;
case 'client_tags':
$terms = get_the_term_list( $post_id , 'client_tags' , '' , ',' , '' );
if ( is_string( $terms ) )
echo $terms;
else
echo '';
break;

}
}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'custom_post_type' );
add_action('init', 'register_taxonomies_for_client_post_type');
add_action( 'init', function () {
    add_ux_builder_post_type( 'client' );
} );

//Set other options for Custom Post Type
