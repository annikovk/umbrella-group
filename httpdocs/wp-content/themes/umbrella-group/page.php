<?php
/**
 * The template for displaying all pages.
 *
 * Шаблон page-bussnise.php (страница 14148) — этот же файл с шапкой business.
 *
 * @package flatsome
 */

if (flatsome_option('pages_template') != 'default') {
    // Get default template from theme options.
    get_template_part('page', flatsome_option('pages_template'));
    return;
}

get_header();
do_action('flatsome_before_page'); ?>
    <div id="content" class="content-area page-wrapper" role="main">
        <?php
        // Без шапки с хлебными крошками — разделы услуг (26, 28, 30, 32, 34,
        // 12792) на первых двух уровнях вложенности и главная.
        $ug_sections = [26, 28, 32, 30, 34, 12792];
        $ug_parent   = (int) get_queried_object()->post_parent;
        $ug_grand    = $ug_parent ? (int) get_post_field('post_parent', $ug_parent) : 0;

        if (!in_array($ug_parent, $ug_sections, true) && !in_array($ug_grand, $ug_sections, true) && (!is_front_page() || is_paged())) {
            echo umbrella_draw_header_with_breadcrumbs();
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
