<?php if (have_posts()) : ?>

    <?php
    // Create IDS
    $ids = array();
    $tags_ids = array();
    $args = array_merge( $wp_query->query_vars, ['posts_per_page' => 10] );
    query_posts( $args );
    while (have_posts()) : the_post();
        array_push($ids, get_the_ID());
        if (!empty(get_the_tags())) {
            foreach (get_the_tags() as $tag) {
                $tag = $tag->term_id;
                if (empty ($tags_ids[$tag])) {
                    $tags_ids[$tag] = 1;
                } else {
                    $tags_ids[$tag] += 1;

                }
            }
        }
    endwhile; // end of the loop.
    $posts = get_post_by_ids($ids);

    //if (!is_tag()) echo umbrella_draw_filter_tabs($tags_ids, $posts);
    umbrella_draw_tiles($posts, 'blogposts');


    //$readmore = __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'flatsome' );

    ?>

    <?php //echo do_shortcode('[blog_posts type="masonry" depth="' . flatsome_option('blog_posts_depth') . '" depth_hover="' . flatsome_option('blog_posts_depth_hover') . '" text_align="' . get_theme_mod( 'blog_posts_title_align', 'center' ) . '" columns="2" ids="' . $ids . '"]'); ?>

    <?php flatsome_posts_pagination(); ?>
 
<?php else : ?>

    <?php get_template_part('template-parts/posts/content', 'none'); ?>

<?php endif; ?>


<?php
function get_post_by_ids($ids)
{
    $args = array(
        'numberposts' => sizeof($ids),
        'orderby' => 'date',
        'order' => 'DESC',
        'post__in' => $ids
    );
    return get_posts($args);
}


