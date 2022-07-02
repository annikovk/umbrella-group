<?php

class stock
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='stock'  padding="0px"]
            [row]
                [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="padding-top-stock layout_stock">
                  <div class="red_border_stock card-shadow-common">
                  <div class="position-card-legend_stock">
                    <div class="main-text-common red_color_stock">АКЦИЯ</div>
                  </div>
                 
                      <div class="title-h3-text-common text-pad">Бесплатная регистрация ИП</div>
                      <div class="main-text-common text-pad desktop_mode_stock">Начните свое дело с&nbsp;экономии! Обратитесь к&nbsp;нам для открытия&nbsp;ИП и&nbsp;при открытии расчетного счета в&nbsp;банке-партнере мы&nbsp;зарегистрируем&nbsp;ИП совершенно бесплатно!</div>
                      <div class="stock-parametric">
                        <div class="main-text-common main-text-accent-common desktop_mode_stock">До 31.02.2022</div>
                        <div class="btn-about-stock">Узнать подробнее</div>
                      </div>
                  </div>
                  <div class="red_border_stock card-shadow-common">
                  <div class="position-card-legend_stock">
                    <div class="main-text-common red_color_stock">АКЦИЯ</div>
                  </div>
                      <div class="title-h3-text-common text-pad">ООО в&nbsp;подарок за&nbsp;оформление лицензии</div>
                      <div class="main-text-common text-pad desktop_mode_stock">Нужна лицензия для ведения бизнеса, но&nbsp;предприятие еще не&nbsp;зарегистрировано? Сэкономьте на&nbsp;регистрации предприятия!</div>
                      <div class="stock-parametric">
                        <div class="main-text-common main-text-accent-common desktop_mode_stock">До 31.02.2022</div>
                        <div class="btn-about-stock">Узнать подробнее</div>
                      </div>
                  </div>
                </div>
                [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/stock.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/stock.js']);
        return $html;
    }

}

function stock_shortcode($atts)
{
    $shortcode = new stock();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('stock', 'stock_shortcode');

?>