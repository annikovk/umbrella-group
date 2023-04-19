<?php
function case_busnisse_shortcode($args)
{
    ob_start();
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"/>
    <?php echo '[section id="case_busnisse" class="case_busnisse"  padding="0px"]'; ?>
    <div class="form_calculate_busnisse_screen pd">
        <div class="row">
            <div class="col">
                <div class="case_wrap">
                    <h2 class="case_title_bus">Кейс из нашей практики </h2>
                    <h3 class="case_sub_title_bus">Открыли в Новосибирске магазин продуктов из Киргизии «Салам».<br>Посмотрите,
                        как это было:
                    </h3>

                    <div class="mobile_case_bus">
                        <div class="case_item_slide">
                            <div class="bg_slide"
                                 style="background:url('https://taxlab.ru/wp-content/uploads/salam.jpg')"></div>

                            <div class="case_content">
                                <div class="date_case_bus">август 2021</div>
                                <div class="text_case">
                                    <span style="font-weight:700">Особенности:</span>
                                    <ul>
                                        <li>Директор — гражданин Киргизии. Ему требовалась помощь юриста по тонкостям
                                            законодательства РФ, составлению документов и договоров.
                                        </li>
                                        <li>Сотрудники — граждане Киргизии. Не все говорят на русском языке, нужен
                                            особенный расчёт больничных и отпускных. На въезде в Россию неверно
                                            переводили ФИО сотрудников, что осложняло дело.
                                        </li>
                                    </ul>
                                </div>
                                <a href="#" class="slider_link_reviews btn_history_modal" data-modal="history_case"
                                   target="_blank">Читать всю историю</a>
                            </div>
                        </div>
                    </div>

                    <div class="swiper case_business_slider">
                        <div class="swiper-wrapper">
                            <?php if (get_field('slider_case_bus', 'option')): ?>
                                <?php while (has_sub_field('slider_case_bus', 'option')): ?>
                                    <div class="swiper-slide">
                                        <div class="case_item_slide">
                                            <div class="bg_slide"
                                                 style="background:url('<?php the_sub_field('img_slide_case', 'option'); ?>')"></div>

                                            <div class="case_content">
                                                <div
                                                    class="date_case_bus"><?php the_sub_field('date_case', 'option'); ?></div>
                                                <div class="text_case">
                                                    <?php the_sub_field('desc_slide_case', 'option'); ?>
                                                </div>
                                                <?php if (get_sub_field('link_reviews_vis', 'option')): ?>
                                                    <a href="<?php the_sub_field('link_reviews', 'option'); ?>"
                                                       class="slider_link_reviews" target="_blank">
                                                        <img src="<?php the_sub_field('img_link_case', 'option'); ?>"
                                                             class="reviews_news"> <?php the_sub_field('text_link_reviews', 'option'); ?>
                                                    </a>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>

                        <div class="pagination_case">
                            <div class="case-swiper-button-prev">←</div>
                            <div class="swiper-pagination"></div>
                            <div class="case-swiper-button-next">→</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <?php echo '[/section]'; ?>
    <div id="history_case" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <div class="modal-body">
                <div class="content_modal">
                    <?php the_field('slider_case_bus_modal', 'option'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php

    wp_reset_postdata();
    wp_reset_query();

    $output_string = ob_get_contents();
    ob_end_clean();
    umbrella_add_custom_css_files(['/assets/css/blocks/case_business.css']);
    umbrella_add_custom_js_files(['/assets/js/blocks/case_business.js']);
    return $output_string;
}

add_shortcode('case_busnisse', 'case_busnisse_shortcode');
?>