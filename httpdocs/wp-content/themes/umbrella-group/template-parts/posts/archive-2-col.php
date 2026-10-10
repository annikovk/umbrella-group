<?php
/**
 * Архив плитками по 2 в ряд: записи блога и акции.
 *
 * $args['post_type'] — 'post' (по умолчанию) или 'akcii' (из archive.php);
 * раньше акции жили в копии этого файла template-parts/akcii/archive-2-col.php.
 */

$ug_post_type = $args['post_type'] ?? 'post';
?>
<?php if (have_posts()) : ?>

    <?php
    // Create IDS
    $ids = array();
    $args = array_merge( $wp_query->query_vars, ['posts_per_page' => 10] );
    query_posts( $args );
    while (have_posts()) : the_post();
        array_push($ids, get_the_ID());
    endwhile; // end of the loop.

    if ($ug_post_type === 'akcii') {
        umbrella_draw_akcii_tiles(ug_get_posts_by_ids($ids, 'akcii'));
    } else {
        umbrella_draw_tiles(ug_get_posts_by_ids($ids), 'blogposts');
    }
    ?>

    <?php flatsome_posts_pagination(); ?>
 
<?php else : ?>

    <?php get_template_part('template-parts/posts/content', 'none'); ?>

<?php endif; ?>
