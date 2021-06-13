<?php
/**
 * The template for displaying all pages.
 *
 * @package flatsome
 */


if(flatsome_option('pages_template') != 'default') {
	// Get default template from theme options.
	get_template_part('page', flatsome_option('pages_template'));
	return;
} else {
    get_header();
    do_action('flatsome_before_page'); ?>
    <div id="content" class="content-area page-wrapper" role="main">


        <?php
        global $post;     // if outside the loop

        $page_id  = get_queried_object_id(); // Get current page id




        $page =  get_queried_object();
        $parentID = $page->post_parent; // Get current page parent id
        $args = array(
            'include' => $parentID,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $parentParentPages = get_pages($args);
        $parentParentPagesID = $parentParentPages[0]->post_parent;

        if (!in_array($parentID, array(26, 28, 32, 30, 34)) && !in_array($parentParentPagesID, array(26, 28, 32, 30, 34))) {
            umbrella_draw_header_with_breadcrumbs();
        } ?>

        <?php while (have_posts()) : the_post(); ?>
            <?php do_action('flatsome_before_page_content'); ?>

            <?php the_content(); ?>

            <?php if (comments_open() || '0' != get_comments_number()) {
                comments_template();
            } ?>

            <?php do_action('flatsome_after_page_content'); ?>
        <?php endwhile; // end of the loop. ?>
    </div>

    <?php
    do_action('flatsome_after_page');
    get_footer();

}

?>
