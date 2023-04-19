<?php
function calculate_busnisse_shortcode($args)
{
    ob_start();
    echo '[lightbox width="400px" padding="0" id="calc_form"]
            [contact-form-7 id="14329" title="Раcчёт открытия бизнеса"]
            [/lightbox]';
    ?>

    <?php
    $terms = get_terms(
        array(
            'taxonomy' => '_sfer_uslug',
            'hide_empty' => true,
            'hierarchical' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        )
    );

    foreach ($terms as $term) { ?>
        <div id="<?php echo $term->slug; ?>-tarif" class="modal modal_tarif_bus">
            <div class="modal-content">
                <div class="modal-body">
                    <ul class="dynamic_tariff_sub_menu">
                        <?php

                        $args = array(
                            'post_type' => 'business_calculation',
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                            'orderby' => 'date',
                            'order' => 'DESC',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => '_sfer_uslug',
                                    'field' => 'name',
                                    'terms' => $term->name
                                )
                            )
                        );

                        //the query
                        $partnersList = new WP_Query($args);

                        //loop through query
                        if ($partnersList->have_posts()) {

                            while ($partnersList->have_posts()) {
                                $partnersList->the_post();
                                ?>
                                <li class="<?php if (get_field('star_tabs')): ?>active<?php endif; ?>"
                                    data-tab="tab-mob-<?php the_ID(); ?>"
                                    data-title="<?php the_field('opening_method'); ?>"><?php the_title(); ?></li>
                                <?php
                            }
                        } else {
                            //no posts found
                        }

                        wp_reset_postdata();

                        ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php } ?>


    <?php echo '[section id="calculate_busnisse" class="calculate_busnisse"  padding="0px"]'; ?>
    <div class="calculate_busnisse_screen pd">
        <div class="row">
            <div class="col">
                <h2 class="title_calculate_busnisse">Рассчитайте, сколько стоит <br>открыть бизнес под ключ сегодня</h2>
                <h3 class="subtitle_calculate_busnisse">Выберите, чем будем заниматься:</h3>
            </div>
            <div class="dynamic_tariff" id="desctop_grid_tarif">
                <ul class="dynamic_tariff_menu">
                    <?php
                    $terms = get_terms(
                        array(
                            'taxonomy' => '_sfer_uslug',
                            'hide_empty' => true,
                            'hierarchical' => false,
                            // 'orderby' => 'name',
                            'order' => 'ASC',
                        )
                    );
                    $term = get_queried_object();
                    foreach ($terms as $term) {

                        ?>
                        <li class="<?php if (get_field('activr_sferf', $term)): ?>current-menu-parent<?php endif; ?>"><a
                                href="#"><?php echo $term->name; ?> <span>↓</span></a>
                            <ul class="dynamic_tariff_sub_menu">
                                <?php

                                $args = array(
                                    'post_type' => 'business_calculation',
                                    'post_status' => 'publish',
                                    'posts_per_page' => -1,
                                    'orderby' => 'date',
                                    'order' => 'ASC',
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => '_sfer_uslug',
                                            'field' => 'name',
                                            'terms' => $term->name
                                        )
                                    )
                                );

                                //the query
                                $partnersList = new WP_Query($args);

                                //loop through query
                                if ($partnersList->have_posts()) {

                                    while ($partnersList->have_posts()) {
                                        $partnersList->the_post();
                                        ?>
                                        <li class="<?php if (get_field('star_tabs')): ?>active<?php endif; ?>"
                                            data-tab="tab-<?php the_id(); ?>"
                                            data-title="<?php the_field('opening_method'); ?>"><?php the_title(); ?></li>
                                        <?php
                                    }
                                } else {
                                    //no posts found
                                }

                                wp_reset_postdata();

                                ?>
                            </ul><!-- /partners-list -->
                        </li>
                    <?php } ?>
                </ul>


                <div class="col ">
                    <div class="dynamic_tariff_content" id="desctop_tabs">
                        <?php
                        $posts = get_posts(array(
                            // 'orderby'     => 'date',
                            'order' => 'DESC',
                            'post_type' => 'business_calculation',
                            'suppress_filters' => true,
                            'posts_per_page' => -1,
                        ));
                        global $post;

                        foreach ($posts as $post) {
                            setup_postdata($post);
                            $category = get_the_category();
                            ?>

                            <div id="tab-<?php the_id(); ?>"
                                 class="tab-content <?php if (get_field('star_tabs')): ?>current<?php endif; ?>">
                                <div class="dynamic_tariff_table">
                                    <div class="dynamic_tariff_col tariff_col_naming">
                                        <div class="dynamic_tariff_header">
                                            <h3 class="tariff_col_title">Выберите способ:</h3>
                                        </div>
                                        <?php if (get_field('list_tariff')): ?>
                                            <div class="list_tariff">
                                                <?php while (has_sub_field('list_tariff')): ?>
                                                    <div
                                                        class="item_tarif"><?php the_sub_field('service_item_list_tarif'); ?></div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="dynamic_tariff_col tariff_col_one"
                                         id="center_col-tarif-<?php the_id(); ?>">
                                        <div class="dynamic_tariff_header">
                                            <h3 class="tariff_col_title">Поэтапно</h3>
                                            <p class="tariff_desc"><?php the_field('desc_item_list_tarif_1'); ?></p>
                                        </div>
                                        <?php if (get_field('list_tariff')): ?>
                                            <div class="list_tariff">
                                                <?php while (has_sub_field('list_tariff')): ?>
                                                    <div
                                                        class="item_tarif"><?php the_sub_field('price_service_item_tarif'); ?>
                                                        <?php if (get_sub_field('prompt_vis')): ?>
                                                            <div class="prompt_container">
                                                                <img
                                                                    src="https://taxlab.ru/wp-content/uploads/prompt.svg"
                                                                    class="prompt_icon">
                                                                <p><?php the_sub_field('text_prompt'); ?></p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="dynamic_tariff_footer">
                                            <p class="dynamic_tariff_amount tariff_amount_1 ">От <span
                                                    class="num_1"><?php the_field('price_item_tariff_1'); ?></span> руб.
                                            </p>
                                            <p class="dynamic_tariff_date">
                                                Открытие<br>за <?php the_field('date_open_1'); ?></p>
                                        </div>
                                    </div>
                                    <div class="dynamic_tariff_col tariff_col_two center_col">
                                        <div class="dynamic_tariff_header">
                                            <h3 class="tariff_col_title">Всё разом</h3>
                                            <p class="tariff_desc"><?php the_field('desc_item_list_tarif_2'); ?></p>
                                            <a href="#calc_form" class="btn_tariff">Получить предложение</a>
                                        </div>
                                        <?php if (get_field('list_tariff')): ?>
                                            <div class="list_tariff">
                                                <?php while (has_sub_field('list_tariff')): ?>
                                                    <div class="item_tarif"><img
                                                            src="/wp-content/uploads/check_calc.svg"></div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="dynamic_tariff_footer">
                                            <p class="dynamic_tariff_amount tariff_amount_2">От <span
                                                    class="num_2"><?php the_field('price_item_tariff_2'); ?></span> руб.
                                            </p>
                                            <p class="sale">Экономите <span class="sale_amount"></span> руб.</p>
                                            <p class="dynamic_tariff_date">
                                            <p class="dynamic_tariff_date">
                                                Открытие<br>от <span
                                                    style="color:rgba(35, 174, 100, 1);"><?php the_field('date_open_2'); ?></span>
                                                рабочих дней
                                            </p>
                                            <div class="box_suprise">+ Подарок <img
                                                    src="/wp-content/uploads/surprice.svg">
                                                <p class="desc_box_suprise"><img src="/wp-content/uploads/image-110.svg"
                                                                                 class="icon_box_suprise"><?php the_field('text_boxsur'); ?>
                                                </p>
                                            </div>
                                            </p>
                                        </div>
                                    </div>


                                    <div class="dynamic_tariff_col tariff_col_three">
                                        <div class="dynamic_tariff_header">
                                            <h3 class="tariff_col_title">Своими силами</h3>
                                            <p class="tariff_desc">
                                                <?php the_field('desc_item_list_tarif_3'); ?>
                                            </p>
                                        </div>
                                        <div class="list_tariff_last">
                                            <div class="item_tariff_last">
                                                <p style="transform: rotate(3deg);"><?php the_field('argument-1'); ?></p>
                                                <img src="/wp-content/uploads/calc_tariff1.svg">
                                            </div>
                                            <div class="item_tariff_last">
                                                <p style="transform: rotate(-4deg);"><?php the_field('argument-2'); ?></p>
                                                <img src="/wp-content/uploads/calc_tariff2.svg">
                                            </div>
                                            <div class="item_tariff_last">
                                                <p style="transform: rotate(7deg);"><?php the_field('argument-3'); ?></p>
                                                <img src="/wp-content/uploads/calc_tariff3.svg">
                                            </div>
                                        </div>
                                        <div class="dynamic_tariff_footer">
                                            <p class="dynamic_tariff_amount">
                                                От <?php the_field('price_item_tariff_3'); ?></p>
                                            <p class="dynamic_tariff_date">
                                                Открытие<br><?php the_field('date_open_3'); ?></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>


            <div class="tarif_mobile_grid">
                <?php
                $terms = get_terms(
                    array(
                        'taxonomy' => '_sfer_uslug',
                        'hide_empty' => true,
                        'hierarchical' => false,
                        // 'orderby' => 'name',
                        'order' => 'DESC',
                    )
                ); ?>
                <ul class="dynamic_tariff_menu">
                    <?php foreach ($terms as $term) { ?>
                        <li class="<?php if (get_field('activr_sferf', $term)): ?>current-menu-parent<?php endif; ?>"><a
                                href="#" class="modal_tarif "
                                data-modal="<?php echo $term->slug; ?>-tarif"><?php echo $term->name; ?> <span>↓</span></a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="change_bus_direction">
                    Посмотрите 3 способа открытия<br><span class="title_bus_dir">Медицинской клиники</span>:
                </div>

                <?php
                $posts = get_posts(array(
                    // 'orderby'     => 'date',
                    'order' => 'ASC',
                    'post_type' => 'business_calculation',
                    'suppress_filters' => true,
                    'posts_per_page' => -1,
                ));
                global $post;

                foreach ($posts as $post) {
                    setup_postdata($post);
                    $category = get_the_category();
                    ?>

                    <div id="tab-mob-<?php the_id(); ?>"
                         class="tab-content <?php if (get_field('star_tabs')): ?>current<?php endif; ?>">
                        <div class="tab">
                            <ul class="tabs-btn">
                                <li class="tab-btn">Поэтапно</li>
                                <li class="tab-btn">Всё разом</li>
                                <li class="tab-btn">Своими силами</li>
                            </ul>
                            <div class="tabs-content">
                                <div class="tab-content">
                                    <div class="dynamic_tariff_table">
                                        <div class="dynamic_tariff_col tariff_col_one">
                                            <div class="dynamic_tariff_header">
                                                <p class="tariff_desc"> <?php the_field('desc_item_list_tarif_1'); ?></p>
                                            </div>
                                            <?php if (get_field('list_tariff')): ?>
                                                <div class="list_tariff">
                                                    <?php while (has_sub_field('list_tariff')): ?>
                                                        <div class="item_tarif">
                                                            <p><?php the_sub_field('service_item_list_tarif'); ?></p>
                                                            <p><?php the_sub_field('price_service_item_tarif'); ?></p>
                                                            <?php if (get_sub_field('prompt_vis')): ?>
                                                                <div class="prompt_container">
                                                                    <img
                                                                        src="https://taxlab.ru/wp-content/uploads/prompt.svg"
                                                                        class="prompt_icon">
                                                                    <p><?php the_sub_field('text_prompt'); ?></p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="dynamic_tariff_footer">
                                                <p>Итого</p>
                                                <div class="tarif_amount">
                                                    <p class="dynamic_tariff_amount tariff_amount_1 ">От <span
                                                            class="num_1"><?php the_field('price_item_tariff_1'); ?></span>
                                                        руб.</p>
                                                    <p class="dynamic_tariff_date">
                                                        Открытие<br>за <?php the_field('date_open_1'); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="dynamic_tariff_col tariff_col_two center_col">
                                        <div class="dynamic_tariff_header">
                                            <p class="tariff_desc"><?php the_field('desc_item_list_tarif_2'); ?></p>
                                            <a href="#calc_form" class="btn_tariff">Получить предложение</a>
                                        </div>
                                        <?php if (get_field('list_tariff')): ?>
                                            <div class="list_tariff">
                                                <?php while (has_sub_field('list_tariff')): ?>
                                                    <div class="item_tarif">
                                                        <p><?php the_sub_field('service_item_list_tarif'); ?></p>
                                                        <p><img src="/wp-content/uploads/check_calc.svg"></p>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="dynamic_tariff_footer" style="display:block">
                                            <div class="dynamic_tariff_footer_wrap">
                                                <p>Итого</p>
                                                <div class="tarif_amount">
                                                    <p class="dynamic_tariff_amount tariff_amount_2">От <span
                                                            class="num_2"><?php the_field('price_item_tariff_2'); ?></span>
                                                        руб.</p>
                                                    <p class="sale">Экономите <span class="sale_amount"></span> руб.</p>
                                                    <p class="dynamic_tariff_date">
                                                        Открытие<br>от <span
                                                            style="color:rgba(35, 174, 100, 1);"><?php the_field('date_open_2'); ?></span>
                                                        рабочих дней
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="box_suprise">+ Подарок <img
                                                    src="/wp-content/uploads/surprice.svg">
                                                <p class="desc_box_suprise">
                                                    <span class="close"><img
                                                            src="https://taxlab.ru/wp-content/uploads/Vector-5.svg"></span>
                                                    <img src="/wp-content/uploads/image-110.svg"
                                                         class="icon_box_suprise"><?php the_field('text_boxsur'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div class="dynamic_tariff_col tariff_col_three">
                                        <div class="dynamic_tariff_header">
                                            <p class="tariff_desc">
                                                <?php the_field('desc_item_list_tarif_3'); ?>
                                            </p>
                                        </div>
                                        <div class="list_tariff_last">
                                            <div class="item_tariff_last">
                                                <p style="transform: rotate(3deg);">Учебная программа не прошла проверку
                                                    Минобра.</p>
                                                <img src="/wp-content/uploads/calc_tariff1.svg">
                                            </div>
                                            <div class="item_tariff_last">
                                                <p style="transform: rotate(-4deg);">Не смогли подобрать<br>
                                                    помещение<br> под все требования.</p>
                                                <img src="/wp-content/uploads/calc_tariff2.svg">
                                            </div>
                                            <div class="item_tariff_last">
                                                <p style="transform: rotate(7deg);">Сайт не соответствовал нормам и
                                                    требованиям.</p>
                                                <img src="/wp-content/uploads/calc_tariff3.svg">
                                            </div>
                                        </div>
                                        <div class="dynamic_tariff_footer">
                                            <p>Итого</p>
                                            <div class="tarif_amount">
                                                <p class="dynamic_tariff_amount">
                                                    От <?php the_field('price_item_tariff_3'); ?></p>
                                                <p class="dynamic_tariff_date">
                                                    Открытие<br><?php the_field('date_open_3'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                ?>
            </div>


        </div>
    </div>
    <?php echo '[/section]'; ?>
    <?php

    wp_reset_postdata();
    wp_reset_query();

    $output_string = ob_get_contents();
    ob_end_clean();
    umbrella_add_custom_css_files(['/assets/css/blocks/calculate_business.css']);
    umbrella_add_custom_js_files(['/assets/js/blocks/calculate_business.js']);
    return $output_string;
}

add_shortcode('calculate_business', 'calculate_busnisse_shortcode');
?>