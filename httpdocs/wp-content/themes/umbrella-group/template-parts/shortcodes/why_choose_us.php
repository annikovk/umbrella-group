<?php

class why_choose_us
{
    public $atts;

    public function generate_shortcode()
    {
        $view = $this->atts['view'] ?? '';
        // view="full" поставлен руками в контент страницы — показываем везде,
        // в том числе на /about/. Без атрибута (блок 9156) и view="strip"
        // (футер) — прежние исключения: промо, главная и /about/ со своими лаврами.
        if ($view !== 'full' && (get_the_ID() == 13514 || is_front_page() || (preg_match('/about\/$/', ug_request_path())))) {
            return "";
        }
        if ($view === 'strip') {
            return $this->strip();
        }
        if ($view === 'c2') {
            return $this->cards_c2();
        }
        if ($view === 'b' || $view === 'c') {
            return $this->cards($view);
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
                                    [col_inner_1 span="3" span__sm="6"]
                                        [featured_box img="30657" inline_svg="0" img_width="108" pos="center" icon_color="rgb(166, 220, 18)"]
                                            <p class="laurel-icon-header">5-ое место в номинации «Интеллектуальное право»<sup style="font-size: 6pt;"> 4</sup></p>
                                            <p class="laurels-icons-year">по итогам 2025 г.</p>
                                        [/featured_box]
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
        return $html;
    }

    /**
     * Узкая полоса рейтингов для общего футера: только «Деловой квартал» по
     * итогам 2025, источник в подписи, без сносок и вкладки сертификатов.
     * Значки — те же лавры из медиатеки, перекрашенные маской в золото
     * (сами файлы белые под тёмный фон моста).
     */
    private function strip()
    {
        $icon = function ($id) {
            $url = esc_url(wp_get_attachment_url($id));
            return '<span class="ug-rs__icon" aria-hidden="true" style="--ug-rs-icon:url(' . $url . ')"></span>';
        };
        $l1 = $icon(1862);
        $l2 = $icon(1863);
        $l4 = $icon(28244);
        $l5 = $icon(30657);
        return <<<EOHTML
        <noindex>
        <section class="ug-ratings-strip" aria-label="Рейтинги">
          <div class="ug-rs__in">
            <div class="ug-rs__lead">
              {$l1}
              <p class="ug-rs__text"><b class="ug-rs__place">1&nbsp;место</b>&nbsp;— <b>Налоговое право</b> и&nbsp;<b>Корпоративное право</b>
                <span class="ug-rs__src">рейтинг журнала «Деловой квартал», итоги 2025&nbsp;г.</span></p>
            </div>
            <ul class="ug-rs__list">
              <li>{$l2}<p class="ug-rs__text"><b>2&nbsp;место</b><span>Коммерческое и&nbsp;хозяйственное право</span></p></li>
              <li>{$l4}<p class="ug-rs__text"><b>4&nbsp;место</b><span>Представительство в&nbsp;суде</span></p></li>
              <li>{$l5}<p class="ug-rs__text"><b>5&nbsp;место</b><span>Интеллектуальное право</span></p></li>
              <li class="ug-rs__more-li"><a class="ug-rs__more" href="/about/#awards">Все награды&nbsp;→</a></li>
            </ul>
            <a class="ug-rs__more ug-rs__more--end" href="/about/#awards">Все награды&nbsp;→</a>
          </div>
        </section>
        </noindex>
        EOHTML;
    }

    /**
     * Пять мест «Делового квартала» по итогам 2025 одним рядом.
     * view="b" — тёмная лента на затемнённом фото моста, белые лавры как есть,
     * первые места — золотом и крупнее; view="c" — светлые карточки.
     */
    private function cards($view)
    {
        $items = [
            [1862, 1, 'Налоговое право'],
            [1862, 1, 'Корпоративное право'],
            [1863, 2, 'Коммерческое и&nbsp;хозяйственное право'],
            [28244, 4, 'Представительство в&nbsp;суде'],
            [30657, 5, 'Интеллектуальное право'],
        ];
        $li = '';
        foreach ($items as [$id, $place, $title]) {
            $url = esc_url(wp_get_attachment_url($id));
            $top = $place === 1 ? ' is-top' : '';
            $li .= '<li class="ug-rc__item' . $top . '">'
                . '<span class="ug-rc__icon" aria-hidden="true" style="--ug-rs-icon:url(' . $url . ')"></span>'
                . '<p class="ug-rc__place">' . $place . '&nbsp;место</p>'
                . '<p class="ug-rc__title">«' . $title . '»</p>'
                . '</li>';
        }
        $bg = $view === 'b' ? ' style="--ug-rc-bg:url(' . esc_url(wp_get_attachment_image_url(9108, 'large')) . ')"' : '';
        return <<<EOHTML
        <noindex>
        <section class="ug-rc ug-rc--{$view}" aria-label="Рейтинги"{$bg}>
          <div class="ug-rc__in">
            <div class="ug-rc__head">
              <p class="ug-rc__h">Почему работать с&nbsp;нами надёжно и&nbsp;удобно</p>
              <a class="ug-rc__more" href="/about/#awards">Все награды&nbsp;→</a>
            </div>
            <ul class="ug-rc__list">{$li}</ul>
            <p class="ug-rc__src">По версии рейтинга журнала «Деловой квартал», итоги 2025&nbsp;г.</p>
          </div>
        </section>
        </noindex>
        EOHTML;
    }

    /**
     * view="c2" — доработанный вариант C: первые места крупными тёмными
     * карточками с золотой цифрой, источник бейджем у заголовка.
     */
    private function cards_c2()
    {
        $items = [
            [1862, 1, 'Налоговое право'],
            [1862, 1, 'Корпоративное право'],
            [1863, 2, 'Коммерческое и&nbsp;хозяйственное право'],
            [28244, 4, 'Представительство в&nbsp;суде'],
            [30657, 5, 'Интеллектуальное право'],
        ];
        $blank = esc_url(get_theme_file_uri('assets/img/laurel-blank.svg'));
        $li = '';
        foreach ($items as [$id, $place, $title]) {
            $url = esc_url(wp_get_attachment_url($id));
            if ($place === 1) {
                $li .= '<li class="ug-c2__item is-top">'
                    . '<span class="ug-c2__laurel" aria-hidden="true" style="--ug-rs-icon:url(' . $blank . ')"></span>'
                    . '<p class="ug-c2__num">1</p><p class="ug-c2__place">место</p>'
                    . '<p class="ug-c2__title">«' . $title . '»</p></li>';
            } else {
                $li .= '<li class="ug-c2__item">'
                    . '<span class="ug-c2__icon" aria-hidden="true" style="--ug-rs-icon:url(' . $url . ')"></span>'
                    . '<p class="ug-c2__place"><b>' . $place . '</b> место</p>'
                    . '<p class="ug-c2__title">«' . $title . '»</p></li>';
            }
        }
        return <<<EOHTML
        <noindex>
        <section class="ug-c2" aria-label="Рейтинги">
          <div class="ug-c2__in">
            <div class="ug-c2__head">
              <div>
                <p class="ug-c2__h">Почему работать с&nbsp;нами <em>надёжно</em> и&nbsp;удобно</p>
                <p class="ug-c2__badge"><span class="ug-c2__star" aria-hidden="true">★</span>Рейтинг журнала «Деловой квартал» · итоги 2025</p>
              </div>
              <a class="ug-c2__more" href="/about/#awards">Все награды&nbsp;→</a>
            </div>
            <ul class="ug-c2__list">{$li}</ul>
          </div>
        </section>
        </noindex>
        EOHTML;
    }

}

function why_choose_us_shortcode($atts)
{
    $shortcode = new why_choose_us();
    $shortcode->atts = is_array($atts) ? $atts : [];
    return $shortcode->generate_shortcode();
}

add_shortcode('why_choose_us', 'why_choose_us_shortcode');

?>