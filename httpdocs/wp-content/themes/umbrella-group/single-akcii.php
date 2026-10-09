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
        get_template_part('template-parts/posts/layout', 'right-sidebar', ['post_type' => 'akcii']);
        ?>
    </div><!-- #content .page-wrapper -->
<?php get_footer();
