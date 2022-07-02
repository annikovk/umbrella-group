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
                <div class="bac_form card-shadow-common">
                    <div class="border-form_free_consultation">
                        <div class="title-h2-text-common margin_title_free_consultation main-text-free_consultation">Бесплатная консультация</div>
                        <div class="main-text-common main-text-free_consultation margin_text_free_consultation">
                            Специалист тезисно разберёт ваш вопрос на&nbsp;3-минутной бесплатной консультации.
                            Закажите обратный звонок и&nbsp;мы&nbsp;свяжемся с&nbsp;вами в&nbsp;течение 20&nbsp;минут
                            в&nbsp;наше рабочее время:пн. &mdash;&nbsp;пт.&nbsp;с&nbsp;09:00 до&nbsp;18:00.
                         </div>
                        <form action="">
                          <div class="radio-input_free_consultation">
                          <div>                            
                            <input type="radio" id="callMe" checked>
                             <label class="main-text-common" for="callMe">Позвоните мне</label> 
                             </div>

                             <div class="contact_whatsapp">
                             <div> 
                                <input type="radio" id="WhatsApp">
                                <label class="main-text-common" for="WhatsApp">Напишите на WhatsApp</label>
                                </div>
                               
                                 <div class="icon_whatsapp">
                                    <img src="https://taxlab.ru/wp-content/uploads/whatsapp.png" alt="whatsapp.png">
                                </div>
                             </div> 
                        </div>
                        <div>
                            <input type="text" placeholder="Имя">
                            <input type="text" placeholder="+ 7 (ХХХ) ХХХ-ХХ-ХХ *">
                        </div>
                        <div class="approve_free_consultation">
                            <input type="checkbox" id="approve" name="approve" checked>
                            <div class="mute-text-normal-common small-mute-text">
                                Нажимая на&nbsp;кнопку, вы&nbsp;соглашаетесь с&nbsp;условиями обработки персональных данных
                            </div>
                        </div>
                        <div type="submit" class="btn-form_free_consultation">Получить консультацию</div>
                         </form>
                    </div>
                </div>
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