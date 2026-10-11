<?php

class stock
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section class='stock' id='stock'  padding="0px"]
            [row]
                [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="padding-top-stock layout_stock">
                  [akciya_block id=9897 title='ООО в подарок за оформление лицензии' excerpt='Нужна лицензия для ведения бизнеса, но предприятие еще не зарегистрировано? Сэкономьте на регистрации предприятия!' type='half' new="true"]
                   [akciya_block id=9897 title='Семинар по изменениям в налогах 2027 г — за 14 900 руб.!' excerpt='Семинар для собственников, бухгалтеров, руководителей бизнеса. 24 сентября с 9:00 до 18:00 в Marins Parl Hotel (Вокзальная магистраль, 1)' type='half' new="true"]
                  
                </div>
                [/col]

           [/row]
         [/section]

        EOHTML;
        ug_enqueue_style('assets/css/blocks/stock.css');
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