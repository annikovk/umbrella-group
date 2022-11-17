<?php if (have_posts()) : ?>

    <?php
    // Create IDS
    $ids = array();
    $tags_ids = array();
    $args = array_merge($wp_query->query_vars, ['posts_per_page' => 10]);
    query_posts($args);
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
    $posts = get_akcii_by_ids($ids);
    //if (!is_tag()) echo umbrella_draw_filter_tabs($tags_ids, $posts);
    umbrella_draw_akcii_tiles($posts);


    //$readmore = __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'flatsome' );

    ?>

    <?php //echo do_shortcode('[blog_posts type="masonry" depth="' . flatsome_option('blog_posts_depth') . '" depth_hover="' . flatsome_option('blog_posts_depth_hover') . '" text_align="' . get_theme_mod( 'blog_posts_title_align', 'center' ) . '" columns="2" ids="' . $ids . '"]'); ?>

    <?php flatsome_posts_pagination(); ?>

<?php else : ?>

    <?php get_template_part('template-parts/posts/content', 'none'); ?>

<?php endif; ?>


<?php
function get_akcii_by_ids($ids)
{
    $args = array(
        'numberposts' => sizeof($ids),
        'orderby' => 'date',
        'order' => 'DESC',
        'post__in' => $ids,
        'post_type' => 'akcii',
    );
    return get_posts($args);
}

function umbrella_draw_akcii_tiles($posts)
{
    $ru_month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

    echo '<div style="padding-top:48px" id="tiles">';
    $tile = "";
    //echo count($posts);

    foreach ($posts as $post) {
        $date = "До " . str_replace($en_month, $ru_month, date("d.m.Y", strtotime(date('m', strtotime('+1 month')).'/31/'.date('Y').' 00:00:00')));
        $date_and_tag = '<div class="case-item-date-and-tag">' . $date . '</div>';


        $excerpt = get_the_excerpt($post->ID);
        if (empty($excerpt)) {
            $excerpt = "<span style='color: red'> Нужно добавить краткое описание записи</span>";
        }
        if (strlen($excerpt) > 150) {
            $excerpt = implode(' ', array_slice(explode(' ', $excerpt), 0, 10)) . '...';
        }
        $excerpt = '<div class="cases-excerpt">' . $excerpt . '</div>';
        $title_and_excerpt = '<div class="cases-item-header"> ' . $post->post_title . '</div>' . $excerpt ;
        $thumbnail = get_the_post_thumbnail($post->ID);
        $thumbnail = '<div class="cases-item-image">' . $thumbnail . '</div>';

        // $permalink = get_post_permalink($post->ID);

        $permalink = urldecode(get_permalink($post->ID));


        $tile = $tile . '[col_inner span="6" span__sm="12" align="left" margin="0px 0px 8px 0px" class="all"]<div class="cases-tile tile-akcii" ><a href="' . $permalink . '">' . $thumbnail . $title_and_excerpt  . $date_and_tag . '</a></div>[/col_inner]';


    }

    $tiles = '[row_inner padding="0px 0 0px 0" style="normal" v_align="top" ]' . $tile . '[/row_inner]';
    echo do_shortcode($tiles);
    echo '</div>';
    umbrella_add_custom_css_files(['/assets/css/blocks/cases-tiles.css']);
}


