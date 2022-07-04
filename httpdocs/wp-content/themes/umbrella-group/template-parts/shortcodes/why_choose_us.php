<?php

class why_choose_us
{
    public $atts;

    public function generate_shortcode()
    {
        if (get_the_ID() == 13514 || is_front_page() || (preg_match('/about\/$/', $_SERVER['REQUEST_URI']))) {
            return "";
        }
        $html = <<<EOHTML
         [row style="collapse" width="full-width"]
            [col span__sm="12"]
                [section bg="9108" bg_size="original" bg_overlay="rgba(0, 0, 0, 0.7)" bg_pos="0% 0%" dark="true" padding="100px" class="flex-why-you-should-work-with-us"]
                        [row_inner style="collapse"]
                            [col_inner span__sm="12"]
                            <p class="h2 for-h2 for-h2-white">Почему работать с нами надёжно и удобно</p>
                            [/col_inner]
                        [/row_inner]
                    [row_inner style="collapse" v_align="equal" h_align="center"]
                        [col_inner span__sm="12" padding="0px 0px 0px 0px" margin="0px 0px 0px 0px"]
                            [tabgroup style="line-bottom" nav_style="normal"]
                                [tab title="Рейтинги"]
                                [row_inner_1 style="collapse" v_align="middle"]
                                    [col_inner_1 span="3" span__sm="6" padding="0px 0px 0px 0px" margin="0px 0px 0px 0px"]
                                        [featured_box img="1861" inline_svg="0" img_width="108" pos="center"]
                                            <p class="laurel-icon-header">Финалист конкурса<br />
                                            «Комплексное предоставление услуг в сфере консалтинга»<sup style="font-size: 6pt;"> 4</sup></p>
                                            <p class="laurels-icons-year">2020</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1862" inline_svg="0" img_width="108" pos="center"]
                                            <p class="laurel-icon-header">1-я группа рейтинга юридических компаний в Новосибирске<sup style="font-size: 6pt;"> 2</sup></p>
                                            <p class="laurels-icons-year">2021</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1862" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]
                                            <p class="laurel-icon-header">Лидер в отрасли «Корпоративное право»</p>
                                            <p class="laurel-place">города Новосибирск<sup style="font-size: 6pt;"> 1</sup></p>
                                            <p class="laurels-icons-year">2021</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1863" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]            
                                            <p class="laurel-icon-header">Лидер в отрасли «Налоговое право»</p>
                                            <p class="laurel-place">города Новосибирск<sup style="font-size: 6pt;"> 1</sup></p>
                                            <p class="laurels-icons-year">2021</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                [/row_inner_1]
                                [/tab]
                                [tab title="Контроль качества и сертификация"]
                                [row_inner_1 style="collapse" padding="0px 16px 0px 0px" visibility="hide-for-small"]
                                [col_inner_1 span="3" span__sm="6"]
                                [ux_gallery ids="9342" style="default" lightbox_image_size="original" columns="1" image_height="141%"]
                                [/col_inner_1]
                                [col_inner_1 span="3" span__sm="6"]
                                [ux_gallery ids="13293" style="default" lightbox_image_size="original" columns="1" image_height="141%"]
                                [/col_inner_1]
                                [col_inner_1 span="3" span__sm="6"]
                                [ux_gallery ids="9340" style="default" lightbox_image_size="original" columns="1" image_height="141%"]
                                [/col_inner_1]
                                [col_inner_1 span="3" span__sm="6"]
                                [ux_gallery ids="13294" style="default" lightbox_image_size="original" columns="1" image_height="141%"]
                                [/col_inner_1]
                                [/row_inner_1]
                                [ux_slider style="focus" slide_width="277px" slide_align="left" freescroll="true" visibility="show-for-small"]
                                [row_inner_1]
                                [col_inner_1 span__sm="12"]
                                [ux_gallery ids="9342" style="default" lightbox_image_size="original" columns="1"]
                                [/col_inner_1]
                                [/row_inner_1]
                                [row_inner_1]
                                [col_inner_1 span__sm="12"]
                                [ux_gallery ids="9341" style="default" lightbox_image_size="original" columns="1"]
                                [/col_inner_1]
                                [/row_inner_1]
                                [row_inner_1 v_align="middle"]
                                [col_inner_1 span__sm="12"]
                                [ux_gallery ids="9340" style="default" lightbox_image_size="original" columns="1"]
                                [/col_inner_1]
                                [/row_inner_1]
                                [row_inner_1 v_align="middle"]
                                [col_inner_1 span__sm="12"]
                                [ux_gallery ids="9334" style="default" lightbox_image_size="original" columns="1"]
                                [/col_inner_1]
                                [/row_inner_1]
                                [/ux_slider]
                                [/tab]
                            [/tabgroup]
                        [/col_inner]
                    [/row_inner]
                [/section]
                <div class="laurel-footnote">1 — по версии журнала «Деловой квартал»      2 — по версии журнала pravo.ru      3 — по версии журнала «Эксперт Сибирь»      4 — Новосибирская городская торгово-промышленная палата</div>
            [/col]
           [/row]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/block-laurels.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/block-laurels.js']);
        return $html;
    }

}

function why_choose_us_shortcode($atts)
{
    $shortcode = new why_choose_us();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('why_choose_us', 'why_choose_us_shortcode');

?>