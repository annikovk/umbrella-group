<?php
## ШОРТКОД вывода записи из категории по id
function func_result_clinets($args)
{
    ob_start();

    $posts = get_posts(array(
        'orderby' => 'date',
        'order' => 'DESC',
        'post_type' => 'result',
        'suppress_filters' => true,
    ));
    global $post;
    //обертка для записей например <ul>
    ?>
    <?php echo '[section class="result_clinets_section"]'; ?>
    <div class="result_client pd">
        <div class="row">
            <div class="col">
                <div class="result_case_wrap">
                    <h2 class="result_title_bus">Результаты других клиентов</h2>
                </div>
            </div>
            <div class="col col-mobile_no">
                <div class="swiper swiper-result">
                    <div class="swiper-wrapper">
                        <?php
                        //выводим в записи в цикле
                        foreach ($posts as $post) {
                            setup_postdata($post);
                            $category = get_the_category();
                            ?>
                            <div class="swiper-slide">
                                <div class="result_item_slide">
                                    <div class="bg_result_slide"
                                         style="background:url('<?php echo get_the_post_thumbnail_url(); ?>')"></div>

                                    <div class="result_content">
                                        <div class="result_header">
                                            <div class="result_logo"
                                                 style="background:url('<?php the_field('logo_company'); ?>')"></div>
                                            <div class="result_city">Новосибирск</div>
                                        </div>
                                        <div class="result_inner_text">
                                            <div class="title_result"><?php the_title(); ?></div>
                                            <div class="direction_result"><?php the_field('activity_res'); ?></div>
                                            <div class="result_status">
                                                <span>✓ действующее</span> <?php the_field('current_res'); ?></div>
                                            <div class="result_doing"><?php the_field('made_res'); ?></div>
                                        </div>
                                        <a href="<?php the_field('link_company_res'); ?>" target="_blank"
                                           class="slider_link_reviews" target="_blank"><span class="icon_social_link"
                                                                                             style="background:url('<?php the_field('icon_company_res'); ?>')"></span> <?php the_field('text_link_res'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="pagination_case">
                        <div class="swiper-scrollbar"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    <?php echo '[/section]'; ?>
    <?php

    wp_reset_postdata();
    wp_reset_query();

    $output_string = ob_get_contents();
    ob_end_clean();
    umbrella_add_custom_js_files(['/assets/js/blocks/result_clinet_business.js']);
    umbrella_add_custom_css_files(["/assets/css/blocks/result_clinet_business.css"]);
    return $output_string;
}

add_shortcode('result_client', 'func_result_clinets');