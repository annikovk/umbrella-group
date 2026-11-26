<div class="entry-content single-page">


    <?php
    echo ' <p class="big">'. get_the_excerpt() . '</p>';
	// the_content() в строку: перед первым <h2> — строка «Услуга по теме» (inc/post-service-cta.php)
	ob_start();
	the_content();
	echo ug_post_content_with_service_cta( get_the_ID(), (string) ob_get_clean() );
	?>

	<?php
	wp_link_pages( array(
		'before' => '<div class="page-links">' . __( 'Pages:', 'flatsome' ),
		'after'  => '</div>',
	) );
	?>

	<?php echo ug_post_service_cta( get_the_ID() ); // «Заказать услугу» / общая заявка ?>

	<?php if ( get_theme_mod( 'blog_share', 1 ) ) {
		// SHARE ICONS
		echo '<div class="blog-share text-center">';
		echo '<div class="is-divider medium"></div>';
		echo do_shortcode( '[share]' );
		echo '</div>';
	} ?>
</div><!-- .entry-content2 -->

<?php if ( get_theme_mod( 'blog_single_footer_meta', 1 ) ) : ?>
	<footer class="entry-meta text-<?php echo get_theme_mod( 'blog_posts_title_align', 'center' ); ?>">
		<?php
		/* translators: used between list items, there is a space after the comma */
		$category_list = get_the_category_list( __( ', ', 'flatsome' ) );

		/* translators: used between list items, there is a space after the comma */
		$tag_list = get_the_tag_list( '', __( ', ', 'flatsome' ) );


        $date = get_the_date('j F Y');
        // Статьи блога: «Обновлено», когда правили позже публикации больше чем на
        // сутки; та же post_modified, что dateModified в JSON-LD Article от AIOSEO.
        // Шаблон рисует и одиночные faq, business_calculation и т.п. — там как было.
        if ( get_post_type() === 'post' ) {
            $date = '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . $date . '</time>';
            if ( (int) get_post_modified_time( 'U', true ) - (int) get_post_time( 'U', true ) > DAY_IN_SECONDS ) {
                $date .= '&emsp;&emsp;Обновлено: <time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . get_the_modified_date( 'j F Y' ) . '</time>';
            }
        }

        $date_and_tag = '<div style="font-size: 0.8em; color: #b2b2b2">'. $date .'&emsp;&emsp;&emsp;&emsp; Тэги: '. $tag_list . '</div>';


		// But this blog has loads of categories so we should probably display them here.
		if ( '' != $tag_list ) {
			$meta_text = __( 'This entry was posted in %1$s and tagged %2$s.', 'flatsome' );
		} else {
			$meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'flatsome' );
		}


        echo $date_and_tag;
		?>
	</footer><!-- .entry-meta -->
<?php endif; ?>

<?php if ( get_theme_mod( 'blog_author_box', 1 ) ) : ?>
	<div class="entry-author author-box">
		<div class="flex-row align-top">
			<div class="flex-col mr circle">
				<div class="blog-author-image">
					<?php
					$user = get_the_author_meta( 'ID' );
					echo get_avatar( $user, 90 );
					?>
				</div>
			</div><!-- .flex-col -->
			<div class="flex-col flex-grow">
				<h5 class="author-name uppercase pt-half">
					<?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?>
				</h5>
				<p class="author-desc small"><?php echo esc_html( get_the_author_meta( 'user_description' ) ); ?></p>
			</div><!-- .flex-col -->
		</div>
	</div>
<?php endif; ?>

<?php if ( get_theme_mod( 'blog_single_next_prev_nav', 1 ) ) :
	flatsome_content_nav( 'nav-below' );
endif; ?>

<style>
    #blog-side-contact-form.footer-form .cf7-footer-input  {
        width: 100%;
        margin-top: 44px;
    }

    #blog-side-contact-form.footer-form  .cf7-submit-button input  {
        width: 100%;
    }
    #blog-side-contact-form.footer-form  .cf7-submit-button input:hover  {
        background-color: #ec1c23;
        box-shadow: unset;
    }

    #blog-side-contact-form.footer-form .cf7-legal-notice {
        width: 100%;
    }

    #blog-side-contact-form.footer-form {
        width: 100%;
        position: relative;
        margin-top: 50px;
        height: 100%;
        padding: 40px;
        border: solid 2px #f9f9f9;
        /* border-color: red; */
        box-shadow: unset;
    }
    }
    </style>
