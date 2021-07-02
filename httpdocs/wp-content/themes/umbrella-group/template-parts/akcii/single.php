<?php if ( have_posts() ) : ?>

    <?php /* Start the Loop */ ?>

    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="article-inner <?php flatsome_blog_article_classes(); ?>">
                <?php
                if(flatsome_option('blog_post_style') == 'default' || flatsome_option('blog_post_style') == 'inline'){
                    get_template_part('template-parts/posts/partials/entry-header', flatsome_option('blog_posts_header_style') );
                    echo "<div style='font-weight: bolder;font-size: 18px;'> <span style='text-transform: uppercase; color: #ec1c23;'>Специальное предложение </span> до ".date("d.m.Y", strtotime(date('m', strtotime('+1 month')).'/01/'.date('Y').' 00:00:00')). '</div>';
                }
                ?>
                <?php
                get_template_part( 'template-parts/akcii/content', 'single' );
                umbrella_add_custom_css_files(['/assets/css/blocks/akcii.css']);
                ?>
            </div><!-- .article-inner -->
        </article><!-- #-<?php the_ID(); ?> -->

    <?php endwhile; ?>

<?php else : ?>

    <?php get_template_part( 'no-results', 'index' ); ?>

<?php endif; ?>