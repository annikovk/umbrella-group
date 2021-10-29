<?php
add_action('init', 'remove_parent_theme_umbrella');
function remove_parent_theme_umbrella()
{
    remove_shortcode('accordion-item');
    remove_shortcode('accordion');
    add_shortcode('accordion-item', 'accordion_item_umbrella');
    add_shortcode('accordion', 'accordion_umbrella');
}


function accordion_umbrella($atts, $content = null)
{
    extract(shortcode_atts(array(
        'auto_open' => '',
        'open' => '',
        'title' => '',
        'class' => '',
    ), $atts));

    if ($auto_open) $open = 1;

    $classes = array('accordion');
    if ($class) $classes[] = $class;

    if ($title) $title = '<h3 class="accordion_title">' . $title . '</h3>';
    return $title . '<div class="' . implode(' ', $classes) . '" rel="' . $open . '">' . flatsome_contentfix($content) . '</div>';
}

function accordion_item_umbrella($atts, $content = '', $code)
{

    extract(shortcode_atts(array(
        'subtitle' => '',
        'title' => 'Accordion Panel',
        'class' => ''
    ), $atts));
    if (strlen($subtitle) > 1) {
        if (str_contains($subtitle, "<Рассрочка>")) {
            $subtitle = str_replace("<Рассрочка>", get_partial_payment_note_for_prices_page(), $subtitle);
        }
        $subtitle = '<div class="accordion-subtitle">' . $subtitle . '</div>';
    }

    if (strlen($content) > 1) {
        $button = '<button class="toggle">↓</button>';
    } else {
        $button = '';
    }
    return '<div class="accordion-item box-shadow-3-hover" >'
        . $button .
        '<a href="#" class="accordion-title  plain" style=" padding-top: 24px; padding-bottom: 24px; ">' . '' .
        '<div class="accordion-title-title">'
        . $title .
        '</div>'
        . $subtitle . '</a>' .

        '<div class="accordion-inner">' . flatsome_contentfix($content) . '</div>' .
        '</div>';

}

function get_partial_payment_note_for_prices_page()
{
    $html = <<<EOHTML
                <span class="partial_payment_note_for_prices_page">
                    Возможна оплата частями 
                    <span class="partial_payment_note_for_prices_page_hint"> <img src="/wp-content/uploads/Help_Outline_Icon_19.png" alt="help icon"> 
                        <div class="partial_payment_note_for_prices_page_tooltip">
                            Предоплата 70 %, остальные 30 % оплачиваются после подписания акта о проделанных работах. Свяжитесь с нами и получите предложение!
                        </div> 
                    </span>
                </span>
            EOHTML;

    return $html;
}
