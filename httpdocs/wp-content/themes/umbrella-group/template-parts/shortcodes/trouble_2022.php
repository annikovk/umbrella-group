<?php

class trouble_2022
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='trouble_2022'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="padding_trouble_2022">
                    <div class="title_trouble_2022">
                        <div class="title-h1-text-common">Выручаем от&nbsp;неприятностей 2020 и&nbsp;2022 годов</div>
                        <div class="main-text-common">Помогаем клиентам адаптироваться во&nbsp;время кризисов.</div>
                    </div>
                    <div class="content_trouble_2022">
                        <div class="padding-bottom_trouble_2022 mobile-content_trouble_2022">
                            <div id="spoiler-trouble-2022" style="display: none">
                                <div class="main-text-common">
                                    <span class="main-text-accent-common">Во&nbsp;время пандемии COVID-19&nbsp;</span>
                                    мы сделали скидки для пострадавших отраслей. 
                                    Например, &minus;50% на&nbsp;бухгалтерское обслуживание туристическим фирмам. 
                                    Аудиты проводили дистанционно. Оповещали клиентов о&nbsp;ковидных ограничениях и&nbsp;помогали соблюсти закон.
                                    <br>
                                    <br>
                                    <span class="main-text-accent-common">В&nbsp;2022</span> 
                                    помогаем нашим клиентам наладить цепочки поставок, заключать договоры с&nbsp;защитой 
                                    от&nbsp;скачков доллара и&nbsp;спекуляций. Регистрируем фирмы в&nbsp;Казахстане.
                                    <br>
                                    <br>
                                    В&nbsp;телеграм-канале информируем о&nbsp;новых положениях, постановлениях и&nbsp;льготах, 
                                    а&nbsp;также анонсируем все полезные события и&nbsp;обучающие мероприятия.
                                </div>
                            </div>
                            <div class="action-text-common" 
                                 title="Click to Show/Hide Content"
                                 onclick="spoilerContentTrouble2022()">
                                <span id="spoiler-btn-show-trouble-2022">Узнайте, как</span>
                                <span id="spoiler-btn-hide-trouble-2022" style="display:none">Свернуть</span>
                            </div>
                        </div>
                        <div class="card-shadow-common card_trouble_2022">
                            <div class="border-card_trouble_2022">
                                <div class="position-card-legend_trouble_2022">
                                    <div class="legend_trouble_2022 mute-text-legend">Посмотрите примеры</div>
                                </div>
                                <div>
                                    <ul>
                                    <li>
                                        <span class="main-text-common">
                                            За&nbsp;время пандемии помогли сократить налоги на&nbsp;140&nbsp;000&nbsp;000&nbsp;руб.
                                        </span>
                                    </li>
                                    <li>
                                        <span class="main-text-common">
                                            Зарегистрировали для клиента юрлицо в&nbsp;Казахстане для импорта немецких запчастей 
                                            после ухода поставщика с&nbsp;рынка&nbsp;РФ из-за санкций 2022.
                                        </span>
                                    </li>
                                </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card_trouble_2022 content-desktop_trouble-2022">
                            <div class="main-text-common">
                                <span class="main-text-accent-common">Во&nbsp;время пандемии COVID-19&nbsp;</span>
                                мы сделали скидки для пострадавших отраслей. 
                                Например, &minus;50% на&nbsp;бухгалтерское обслуживание туристическим фирмам. 
                                Аудиты проводили дистанционно. Оповещали клиентов о&nbsp;ковидных ограничениях и&nbsp;помогали соблюсти закон.
                                <br>
                                <br>
                                <span class="main-text-accent-common">В&nbsp;2022</span> 
                                помогаем нашим клиентам наладить цепочки поставок, заключать договоры с&nbsp;защитой 
                                от&nbsp;скачков доллара и&nbsp;спекуляций. Регистрируем фирмы в&nbsp;Казахстане.
                                <br>
                                <br>
                                В&nbsp;телеграм-канале информируем о&nbsp;новых положениях, постановлениях и&nbsp;льготах, 
                                а&nbsp;также анонсируем все полезные события и&nbsp;обучающие мероприятия.
                            </div>
                        </div>
                 </div>
                </div>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/trouble_2022.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/trouble_2022.js']);
        return $html;
    }

}

function trouble_2022_shortcode($atts)
{
    $shortcode = new trouble_2022();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('trouble_2022', 'trouble_2022_shortcode');

?>