<?php
/**
 * Архивы: записи (категории, метки, даты), акции /akcii/ и вопросы.
 *
 * Один шаблон вместо archive.php / archive-akcii.php / archive-faq.php — они
 * отличались одной строкой. Тип выбирается так же, как WP выбирает
 * archive-{post_type}.php (get_archive_template): по query var post_type,
 * если он ровно один.
 */

$ug_archive_types = array_filter((array) get_query_var('post_type'));
$ug_archive_type  = count($ug_archive_types) === 1 ? reset($ug_archive_types) : '';

get_header(); ?>

<div id="content" class="content-area page-wrapper" role="main">
    <div class="row row-main">
        <div class="large-12 col">
            <div class="col-inner">
                <?php echo umbrella_draw_header_with_breadcrumbs();?>
                <?php if ( have_posts() ) :
                    match ($ug_archive_type) {
                        'akcii' => get_template_part('template-parts/posts/archive', '2-col', ['post_type' => 'akcii']),
                        'faq'   => get_template_part('template-parts/faq/archive', 'simple'),
                        default => get_template_part('template-parts/posts/archive', '2-col'),
                    };

                else : get_template_part( 'template-parts/posts/content','none');
                endif; ?>

            </div><!-- .col-inner -->
        </div><!-- .large-12 -->
    </div><!-- .row -->
</div>

<?php
do_action( 'flatsome_after_page' );
?>

<?php get_footer(); ?>
