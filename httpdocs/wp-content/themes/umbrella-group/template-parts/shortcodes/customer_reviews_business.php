<?php
function customer_reviews_busnisse_shortcode($args)
{
    ob_start();
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <?php echo '[section id="customer_reviews" class="customer_reviews"  padding="0px"]'; ?>
    <div class="customer_reviews_busnisse_screen pd d-none" id="customer_reviews">
        <div class="row">
            <div class="col">
                <div class="reviews_case_wrap">
                    <h2 class="reviews_title_bus">Отзывы клиентов</h2>
                </div>
            </div>
        </div>
        <div class="swiper swiper-reviews_bus">
            <div class="swiper-wrapper">
                <?php if (get_field('slider_reviews_bus', 'option')): ?>
                    <?php while (has_sub_field('slider_reviews_bus', 'option')): ?>
                        <div class="swiper-slide">
                            <div class="reviews_item_slide">
                                <div class="title_reviews_item">
                                    <?php the_sub_field('title_reviews_bus', 'option'); ?>
                                </div>
                                <div class="content_reviews_item">
                                    <p><?php the_sub_field('text_reviews_bus', 'option'); ?>
                                    </p>
                                </div>
                                <!-- <a href="<?php //the_sub_field('img_reviews_bus', 'option');?>" class="link_reviews_item">Читать полный отзыв</a> -->
                                <div class="client_info_reviews_item">
                                    <div class="logo_reviews"
                                         style="background:url('<?php the_sub_field('logo_company_reviews', 'option'); ?>')"></div>
                                    <div class="about_client_reviews">
                                        <p class="naming_company_reviews"><?php the_sub_field('title_company_reviews', 'option'); ?> </p>
                                        <p class="client_info_reviews"><?php the_sub_field('client_revirews_info', 'option'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <div class="pagination_case">
                <div class="reviews-swiper-button-prev">←</div>
                <div class="swiper-pagination"></div>
                <div class="reviews-swiper-button-next">→</div>
            </div>
        </div>
    </div>


    <?php echo '[/section]'; ?>

    <?php

    wp_reset_postdata();
    wp_reset_query();

    $output_string = ob_get_contents();
    ob_end_clean();
    umbrella_add_custom_css_files(['/assets/css/blocks/customer_reviews_business.css']);
    umbrella_add_custom_js_files(['/assets/js/blocks/customer_reviews_business.js']);
    return $output_string;
}

add_shortcode('reviews_busnisse', 'customer_reviews_busnisse_shortcode');
?>