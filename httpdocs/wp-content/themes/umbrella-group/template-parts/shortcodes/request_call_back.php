<?php

class request_call_back
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
        
         [section id='request_call_back'  padding="0px"]
            [row]
                [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="wrapper_request_call_back">
                    <div class="layout_request_call_back">
                        <div class="title-h1-text-common padding-top-request_call_back" style="text-align: center;">Закажите обратный звонок</div>
                        <div class="main-text-common" style="text-align: center">
                            Специалист тезисно разберёт ваши вопросы на&nbsp;3-минутной <span class="main-text-accent-common-red">бесплатной консультации</span>.
                            Мы&nbsp;свяжемся с&nbsp;вами в&nbsp;течение 20&nbsp;минут
                            в&nbsp;рабочее время:<br>пн. &mdash;&nbsp;пт.&nbsp;с&nbsp;09:00 до&nbsp;18:00.
                        </div>
                        [contact-form-7 id="13790" title="Главная форма в footer (Обратный звонок)"]
                    </div>
                </div>
                [/col]
           [/row]
        [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/request_call_back.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/request_call_back.js']);
        return $html;
    }

}

function request_call_back_shortcode($atts)
{
    $shortcode = new request_call_back();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('request_call_back', 'request_call_back_shortcode');

?>