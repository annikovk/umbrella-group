<?php get_header(); ?>

<div id="content" class="content-area page-wrapper" role="main">
    <div class="row row-main">
        <div class="large-12 col">
            <div class="col-inner">
                <?php echo umbrella_draw_header_with_breadcrumbs();?>
                <?php if ( have_posts() ) :
                    get_template_part( 'template-parts/akcii/archive','2-col');

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
