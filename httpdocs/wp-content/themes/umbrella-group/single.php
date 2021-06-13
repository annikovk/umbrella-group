<?php
/**
 * The blog template file.
 *
 * @package flatsome
 */

get_header();

?>

    <div id="content" class="blog-wrapper blog-single page-wrapper">
        <?php
        if (get_post_format( get_the_ID() ) == 'aside') {
            get_template_part('template-parts/posts/layout', 'aside');
        } else {
            get_template_part('template-parts/posts/layout', get_theme_mod('blog_post_layout', 'right-sidebar'));
        }
        ?>
    </div><!-- #content .page-wrapper -->

<?php get_footer();
