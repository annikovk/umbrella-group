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
                  [akciya_block id=9897 title='ООО в подарок за оформление лицензии' excerpt='Нужна лицензия для ведения бизнеса, но предприятие еще не зарегистрировано? Сэкономьте на регистрации предприятия!' type='half' new="true"]
                  [akciya_block id=9876 title='Бесплатная регистрация ИП' type='half' new="true"]
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