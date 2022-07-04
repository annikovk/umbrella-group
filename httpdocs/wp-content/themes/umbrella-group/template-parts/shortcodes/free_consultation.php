<?php

class free_consultation
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='free_consultation'  padding="0px"]
         <div class="free_consultation_background">
            [row]
                [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <img class="mobile_mode_free_consultation" src="https://taxlab.ru/wp-content/uploads/IMG_9591-1.png" alt="IMG_9591-1.png">
                [contact-form-7 id="13771" title="Форма на главной (бесплатная консультация)"]
                [/col]
           [/row]
         </div>
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/free_consultation.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/free_consultation.js']);
        return $html;
    }

}

function free_consultation_shortcode($atts)
{
    $shortcode = new free_consultation();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('free_consultation', 'free_consultation_shortcode');

?>