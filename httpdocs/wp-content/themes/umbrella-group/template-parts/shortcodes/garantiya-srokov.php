<?php

class garantiya_srokov
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='umbrella-feedback'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
            <div class="layout main_block box-shadow_block">
                <div>
                    <h1 class="title-h1">Гарантия сроков</h1>
                    <p class="text_content desktop-text">
                        В каждом договоре вы увидите срок оказания услуг
                        и ответственность за нарушение сроков. Время &mdash; самый 
                        ценный ресурс, и мы знаем цену времени наших клиентов. 
                        Посмотрите гарантии в договорах:
                    </p>
                    <p class="text_content mobile-text">
                        В каждом договоре вы увидите 
                        срок оказания услуг 
                        и ответственность за нарушение сроков. Посмотрите гарантии 
                        в договорах:
                    </p>
                    <ul class="list_download">
                        <li><a class="links_download" href="#">Договор аудита</a></li>     
                        <li><a class="links_download href="#">Договор лицензирования</a></li>     
                        <li><a class="links_download href="#">Договор бухгалтерского обслуживания</a></li>
                    </ul>
                </div>
                <div>
                    <h1 class="title-h1">Гарантия результата</h1>
                    <p class="text_content desktop-text">
                        Исключаем человеческий фактор <span class="accent_text">двойным контролем:</span>
                        директора подразделений лично проверяют ведение 
                        бухгалтерского учёта, аудиторские отчёты и заключения,
                        участвуют во всех этапах ведения дел в арбитражных 
                        судах и судах общей юрисдикции. Для <span class="accent_text">комплексного 
                        решения вопросов</span> подключается команда из аудиторов, 
                        бухгалтеров и юристов.
                        <br>
                        <br>
                        Аудиторские и бухгалтерские услуги <span class="accent_text">застрахованы &mdash; </span>
                        при допущении ошибки штрафы и ответственность
                        ложатся на аудитора.
                        <br>
                        <br>
                        Мы за удобство и комфорт. Поэтому при оформлении 
                        лицензий вы можете воспользоваться <span class="accent_text">оплатой частями.</span>
                        100&nbsp;% предоплата не требуется.
                    </p>
                    <p class="text_content mobile-text">
                        Исключаем человеческий фактор 
                        двойным контролем.
                        <br>
                        <br>
                        Аудиторские и бухгалтерские 
                        услуги застрахованы  &mdash; 
                        при допущении ошибки штрафы 
                        и ответственность ложатся 
                        на аудитора.
                        <br>
                        <br>
                        При оформлении лицензий 
                        вы можете воспользоваться оплатой частями. 100&nbsp;% 
                        предоплата не требуется.
                    </p>
                </div>
            </div>
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