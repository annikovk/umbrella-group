<?php

class garantiya_srokov
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='umbrella-feedback' bg_color="rgb(249, 249, 249)" padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <p class="red"> привет </p>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/garantiya_srokov.css']);
        return $html;
    }

}

function garantiya_srokov_shortcode($atts)
{
    $shortcode = new garantiya_srokov();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('garantiya_srokov', 'garantiya_srokov_shortcode');

?>