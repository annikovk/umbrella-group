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
        <noindex>
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
                                            <p class="laurel-icon-header">Участник конкурса<br />
                                            «Лучшее малое предприятие года города Новосибирска»<sup style="font-size: 6pt;"> 1</sup></p>
                                            <p class="laurels-icons-year">май, 2021</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1862" inline_svg="0" img_width="108" pos="center"]
                                            <p class="laurel-icon-header">1-ая группа рейтинга по налоговому консультированию и спорам среди региональных компаний<sup style="font-size: 6pt;"> 2</sup></p>
                                            <p class="laurels-icons-year">декабрь, 2022</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1862" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]
                                            <p class="laurel-icon-header">1-ое место среди бухгалтерских компаний Новосибирска<sup style="font-size: 6pt;"> 3</sup></p>
                                            <p class="laurels-icons-year">май, 2023</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1862" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]          
                                            <p class="laurel-icon-header">1-ое место в номинации «Корпоративное право»<sup style="font-size: 6pt;"> 4</sup></p>
                                            <p class="laurels-icons-year">по итогам 2025 г.</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="12"]
                                        
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6" ]
                                        [featured_box img="1863" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]          
                                            <p class="laurel-icon-header">2-ое место в номинации «Коммерческое и хозяйственное право»<sup style="font-size: 6pt;"> 4</sup></p>
                                            <p class="laurels-icons-year">по итогам 2025 г.</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="1862" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]            
                                            <p class="laurel-icon-header">1-ое место в номинации «Налоговое право»<sup style="font-size: 6pt;"> 4</sup></p>
                                            <p class="laurels-icons-year">по итогам 2025 г.</p>
                                        [/featured_box]
                                    [/col_inner_1]
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="28244" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]            
                                            <p class="laurel-icon-header">4-ое место в номинации «Представительство в суде»<sup style="font-size: 6pt;"> 4</sup></p>
                                            <p class="laurels-icons-year">по итогам 2025 г.</p>
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
                                [ux_gallery ids="27451" style="default" lightbox_image_size="original" columns="1" image_height="141%"]
                                [/col_inner_1]
                                [col_inner_1 span="3" span__sm="6"]
                                    <div class="row large-columns-1 medium-columns- small-columns-">
                                    <div class="gallery-col col">
                                    <div class="col-inner">
                                    <a class="" href="/wp-content/uploads/Выписка-95222-ЮC_23.pdf" target="blank"> 
                                    <div class="box has-hover gallery-box box-default">
                                    <div class="box-image image-cover" style="padding-top:141%;">
                                    <img width="283" height="400" src="https://taxlab.ru/wp-content/uploads/cert-2-min-283x400.jpg" class="attachment-medium size-medium" alt="" decoding="async" loading="lazy" ids="9340" style="default" lightbox_image_size="original" columns="1" image_height="141%"> </div>
                                    <div class="box-text text-left">
                                     <div class="year">2020</div><p>Член СРО аудиторов Ассоциации «Содружество»</p><p></p>
                                    </div>
                                    </div>
                                    </a> 
                                    </div>
                                    </div>
                                    </div>

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
                                [ux_gallery ids="15686" style="default" lightbox_image_size="original" columns="1"]
                                [/col_inner_1]
                                [/row_inner_1]
                                [row_inner_1 v_align="middle"]
                                [col_inner_1 span__sm="12"]

                           
                            <div class="flickity-viewport" style="height: 558.906px; touch-action: pan-y;"><div class="flickity-slider" style="left: 0px; transform: translateX(-296.95%);"><div class="row is-selected" id="row-1836219916" style="position: absolute; left: 303.56%;">
                    <div id="col-2071496506" class="col small-12 large-12">
                    <div class="col-inner">
                    <div class="row large-columns-1 medium-columns- small-columns-">
                    <div class="gallery-col col">
                    <div class="col-inner">
                    <a href="/wp-content/uploads/Выписка-95222-ЮC_23.pdf" target="blank" > 
                    <div class="box has-hover gallery-box box-default">
                    <div class="box-image">
                    <img width="276" height="400" src="https://taxlab.ru/wp-content/uploads/cert-2-min-283x400.jpg" class="attachment-medium size-medium" alt="" decoding="async" loading="lazy" ids="9342" style="default" lightbox_image_size="original" columns="1"> </div>
                    <div class="box-text text-left">
                      <div class="year">2020</div><p>Член СРО аудиторов Ассоциации «Содружество»</p><p></p>
                    </div>
                    </div>
                    </a> </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div><div class="row" id="row-1510627227" aria-hidden="true" style="position: absolute; left: 379.45%;">
                    <div id="col-525896529" class="col small-12 large-12">
                    <div class="col-inner">
                    <div class="row large-columns-1 medium-columns- small-columns-">
                    <div class="gallery-col col">
                    <div class="col-inner">
                    <a  href="/wp-content/uploads/Выписка-95222-ЮC_23.pdf" > <div class="box has-hover gallery-box box-default">
                    <div class="box-image">
                    <img width="283" height="400" src="https://taxlab.ru/wp-content/uploads/cert-2-min-283x400.jpg" class="attachment-medium size-medium" alt="" decoding="async" loading="lazy" ids="15686" style="default" lightbox_image_size="original" columns="1" srcset="https://taxlab.ru/wp-content/uploads/полис-2022-ВСК-283x400.jpg 283w, https://taxlab.ru/wp-content/uploads/полис-2022-ВСК-566x800.jpg 566w, https://taxlab.ru/wp-content/uploads/полис-2022-ВСК-768x1086.jpg 768w, https://taxlab.ru/wp-content/uploads/полис-2022-ВСК-1087x1536.jpg 1087w, https://taxlab.ru/wp-content/uploads/полис-2022-ВСК.jpg 1240w" sizes="(max-width: 283px) 100vw, 283px"> </div>
                    <div class="box-text text-left">
                    <div class="year">2020</div><p>Член СРО аудиторов Ассоциации «Содружество»</p>
                    </div>
                    </div>
                    </a> </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div><div class="row align-middle" id="row-106520825" style="position: absolute; left: 151.78%;" aria-hidden="true">
                    <div id="col-714136003" class="col small-12 large-12">
                    <div class="col-inner">
                    </div>
                    </div>
                    </div><div class="row align-middle" id="row-465116681" aria-hidden="true" style="position: absolute; left: 227.67%;">
                    <div id="col-1215745469" class="col small-12 large-12">
                    <div class="col-inner">
                    <div class="row large-columns-1 medium-columns- small-columns-">
                    <div class="gallery-col col">
                    <div class="col-inner">
                    <a class="image-lightbox lightbox-gallery" href="https://taxlab.ru/wp-content/uploads/cert-2-min.jpg" title="<p>Свидетельство на товарный знак UMBRELLA GROUP</p>"> <div class="box has-hover gallery-box box-default">
                    <div class="box-image">
                    <img width="283" height="400" src="https://taxlab.ru/wp-content/uploads/cert-1-min-283x400.jpg" class="attachment-medium size-medium" alt="" decoding="async" loading="lazy" ids="9334" style="default" lightbox_image_size="original" columns="1" srcset="https://taxlab.ru/wp-content/uploads/cert-1-min-283x400.jpg 283w, https://taxlab.ru/wp-content/uploads/cert-1-min-566x800.jpg 566w, https://taxlab.ru/wp-content/uploads/cert-2-min.jpg 637w" sizes="(max-width: 283px) 100vw, 283px"> </div>
                    <div class="box-text text-left">
                    <p></p><p>Свидетельство на товарный знак UMBRELLA GROUP</p><p></p>
                    </div>
                    </div>
                    </a> </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div></div></div>
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
                <div class="laurel-footnote">1 — Департамент инвестиций потребительского рынка, инноваций и предпринимательства мэрии города Новосибирска      2 — по версии журнала 300.pravo.ru      3 — по итогам рейтинга «Планка»      4 — по версии рейтинга журнала «Деловой Квартал»</div>
            [/col]
           [/row]
           </noindex>
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