<?php

class on_call
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='on_call'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="layout_on_call">
                    <div class="descriptions_on_call mobile_mode_on_call_flex">
                        <div class="title-h1-text-common">На&nbsp;связи 24&nbsp;/ 7&nbsp;/ 365</div>                     
                        <div class="main-text-common">
                            За&nbsp;вами закрепляется специалист, который на&nbsp;связи даже в&nbsp;новогоднюю ночь и&nbsp;утро. 
                        </div>
                        <div>
                        <div class="main-text-italic-common">&laquo;Как будто в&nbsp;соседнем кабинете&raquo;</div>
                        <div class="mute-text-legend-normal-common">из отзыва клиента</div>
                    </div>
                    </div>
                    <div class="slider-phone_on_call">
                        <div class="paginator_phone desktop_mode_on_call_flex">
                            <div class="up-phone btn-arrow-phone" id="up-phone">&uarr;</div>
                                <div class="dotPhone-list">
                                    <div id="btn-dot-phone-1" class="dotPhone select_phone"></div>
                                    <div id="btn-dot-phone-2" class="dotPhone"></div>
                                    <div id="btn-dot-phone-3" class="dotPhone"></div>
                                </div>
                            <div class="down-phone btn-arrow-phone" id="down-phone">&darr;</div>
                    </div>
                        <div class="phone_slider_on_call" id="phone_slider_on_call">
                            <div class="phone_on_call">
                                <img class="mac-up-phone" src="https://taxlab.ru/wp-content/uploads/iPhone_8_1-1.png" alt="iPhone_8_1-1.png">
                                <div class="container_mac-up-phone">
                                    <div class="slider_mac-up-phone">
                                        <div class="slider-line_mac-up-phone" id="slider-line_mac-up-phone">
                                            <img class="img_mac-up-phone active_mac-up-phone" 
                                                src="https://taxlab.ru/wp-content/uploads/эран-1.png" alt="экран-1.png">
                                            <img class="img_mac-up-phone" 
                                                src="https://taxlab.ru/wp-content/uploads/экран-2.png" alt="экран-2.png">
                                            <img class="img_mac-up-phone" 
                                                src="https://taxlab.ru/wp-content/uploads/экран-3.png" alt="экран-3.png">
                                         </div>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                    <div class="descriptions_on_call desktop_mode_on_call_flex">
                        <div class="title-h1-text-common">На&nbsp;связи 24&nbsp;/ 7&nbsp;/ 365</div>
                        <div class="main-text-common desktop_mode_on_call">
                            За&nbsp;вами закрепляется специалист, который на&nbsp;связи даже в&nbsp;новогоднюю ночь и&nbsp;утро. 
                            Не&nbsp;оставляем клиентов без поддержки и&nbsp;реагируем на&nbsp;срочные вопросы. 
                            Позвоните или напишите вашему специалисту на&nbsp;What&rsquo;s App и&nbsp;получите ответ незамедлительно.
                        </div>                        
                        <div class="main-text-common mobile_mode_on_call">
                            За&nbsp;вами закрепляется специалист, который на&nbsp;связи даже в&nbsp;новогоднюю ночь и&nbsp;утро. 
                        </div>
                        <div>
                        <div class="main-text-italic-common">&laquo;Как будто в&nbsp;соседнем кабинете&raquo;</div>
                        <div class="mute-text-legend-normal-common">из отзыва клиента</div>
                    </div>
                    </div>
                </div>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/on_call.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/on_call.js']);
        return $html;
    }

}

function on_call_shortcode($atts)
{
    $shortcode = new on_call();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('on_call', 'on_call_shortcode');

?>