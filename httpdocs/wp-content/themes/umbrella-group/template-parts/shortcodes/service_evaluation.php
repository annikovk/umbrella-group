<?php

class service_evaluation
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section class='service_evaluation' id='service_evaluation'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="layout-service_evaluation">
                    <div>
                        <div class="title-h1-text-common">Не меняем цену</div>
                        <div class="content_service_evaluation">
                            <div class="main-text-common">
                                У&nbsp;наших клиентов не&nbsp;бывает &laquo;непредвиденных&raquo; расходов.
                                <br>    <br>
                                <div class="main-text-common mobile_service_evaluation">
                                Точно обозначаем стоимость решения ваших задач до&nbsp;старта работ 
                                и&nbsp;заранее предупреждаем о&nbsp;возможных дополнительных расходах.
                            </div>
                            </div>
                            <div class="main-text-common desktop_service_evaluation">
                                Каждый день специалисты мониторят изменения в&nbsp;законодательстве, чтобы быть в&nbsp;курсе нововведений. 
                                Мы&nbsp;знаем обо всех пошлинах на&nbsp;2022&nbsp;год. Юристы прогнозируют этапы ведения 
                                дел в&nbsp;суде и&nbsp;анализируют всевозможные риски. Поэтому мы&nbsp;точно обозначаем стоимость 
                                решения ваших задач до&nbsp;старта работ и&nbsp;заранее предупреждаем о&nbsp;возможных дополнительных расходах.
                            </div>
                            <div class="main-text-italic-common mobile_service_evaluation">
                                Не&nbsp;обещаем невозможного, не&nbsp;&laquo;тянем деньги&raquo; с&nbsp;клиентов 
                                и&nbsp;оцениваем риски &laquo;на&nbsp;берегу&raquo;.
                            </div>
                            <div class="main-text-italic-common desktop_service_evaluation">
                                Не&nbsp;обещаем невозможного, не&nbsp;&laquo;тянем деньги&raquo; с&nbsp;клиентов 
                                и&nbsp;оцениваем риски &laquo;на&nbsp;берегу&raquo;. Дорожим репутацией, 
                                и&nbsp;именно поэтому в&nbsp;подходе к&nbsp;решению ваших задач лежит структурная 
                                и&nbsp;последовательная работа.
                            </div>
                            <div class="slider_mobile_service_evaluation mobile_service_evaluation_flex">
                                <div class="card-mobile__service_evaluation">
                                    <img class="item-img-mobile" 
                                            src="https://taxlab.ru/wp-content/uploads/service_evaluation_1.png" 
                                            alt="Пример КП на лицензирование с оценкой сроков и стоимости.">
                                    <div class="mute-text-common comment_card_service_evaluation">
                                        Пример КП на лицензирование с оценкой сроков и стоимости.
                                    </div>
                                </div>
                                <div class="card-mobile__service_evaluation">
                                    <img class="item-img-mobile" 
                                                src="https://taxlab.ru/wp-content/uploads/service_evaluation_2.png" 
                                                alt="Пример КП на аудит с оценкой сроков и стоимости.">
                                     <div class="mute-text-common comment_card_service_evaluation">
                                        Пример КП на аудит с оценкой сроков и стоимости.
                                     </div>
                                </div>
                                <div class="card-mobile__service_evaluation">
                                    <img class="item-img-mobile" 
                                                src="https://taxlab.ru/wp-content/uploads/service_evaluation_3.png" 
                                                alt="Пример КП на бухгалтерское обслуживание с оценкой.">
                                    <div class="mute-text-common comment_card_service_evaluation">
                                        Пример КП на бухгалтерское обслуживание с оценкой.
                                    </div>
                                </div>
                            </div>
                            <div class="button-block_service_evaluation">
                                <a class="button-service_evaluation width-content-service_evaluation" href="#service_evaluation_zapros_kp">
                                    Получить предложение
                                </a>
                                <a class="action-text-common width-content-service_evaluation" href="/price/">Посмотреть прайс</a>
                            </div>

                        </div>
                    </div>
                    <div class="layout-stack-and-text desktop_service_evaluation">
                        <div class="layout-stack">
                            <div class="container-stack">
                                <div class="card">
                                    <img class="item-img" 
                                        src="https://taxlab.ru/wp-content/uploads/service_evaluation_3.png" 
                                        alt="Пример КП на бухгалтерское обслуживание с оценкой.">
                                </div>
                                <div class="card">
                                    <img class="item-img" 
                                        src="https://taxlab.ru/wp-content/uploads/service_evaluation_2.png" 
                                        alt="Пример КП на аудит с оценкой сроков и стоимости.">
                                </div>
                                <div class="card">
                                    <img class="item-img" 
                                    src="https://taxlab.ru/wp-content/uploads/service_evaluation_1.png" 
                                    alt="Пример КП на лицензирование с оценкой сроков и стоимости.">
                                </div>
                            </div>
                        </div>
                        <div class="text-under-stack">
                            <div id="current-slide" class="mute-text-normal-common"></div>
                            <div class="navigation_panel_stack">
                                <div class="btn-prev btn-arrow">&larr;</div>
                                    <div class="layout-dots">
                                        <div id="btn-dot-1" class="dot"></div>
                                        <div id="btn-dot-2" class="dot"></div>
                                        <div id="btn-dot-3" class="dot"></div>
                                    </div>
                                <div class="btn-next btn-arrow">&rarr;</div>
                            </div>
                         </div>
                    </div>
                </div>
            [/col]
           [/row]
           [lightbox id="service_evaluation_zapros_kp"   width="400px"  padding="0px"][contact-form-7 id="13770" title="Форма на главной (Получить предложение)"][/lightbox]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/service_evaluation.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/service_evaluation.js']);
        return $html;
    }

}

function service_evaluation_shortcode($atts)
{
    $shortcode = new service_evaluation();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('service_evaluation', 'service_evaluation_shortcode');

?>