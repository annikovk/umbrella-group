<?php

class garantiya_srokov
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='garantiya_srokov'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="layout main_block_garantiya_srokov card-shadow-common">
                    <div>
                        <div class="title-h2-text-common margin-title_garantiya_srokov" style="text-align: center;">Гарантия сроков</div>
                        <div class="main-text-common desktop-text">
                            В&nbsp;каждом договоре вы&nbsp;увидите срок оказания услуг
                            и&nbsp;ответственность за&nbsp;нарушение сроков. Время&nbsp;&mdash; самый 
                            ценный ресурс, и&nbsp;мы&nbsp;знаем цену времени наших клиентов. 
                            Посмотрите гарантии в&nbsp;договорах:
                        </div>
                        <div class="main-text-common mobile-text">
                            В&nbsp;каждом договоре вы&nbsp;увидите 
                            срок оказания услуг 
                            и&nbsp;ответственность за&nbsp;нарушение сроков. Посмотрите гарантии 
                            в&nbsp;договорах:
                        </div>
                        <ul class="list-download_garantiya_srokov margin-link_garantiya_srokov">
                            <li><a class="action-text-common" href="#garantiya-srokov-dogovor-buhobsluzhivaniya">Договор аудита</a></li>     
                            <li><a class="action-text-common" href="#garantiya-srokov-dogovor-licenzirovaniya">Договор лицензирования</a></li>     
                            <li><a class="action-text-common" href="#garantiya-srokov-dogovor-audita">Договор бухгалтерского обслуживания</a></li>
                        </ul>
                    </div>
                    <div>
                        <div class="title-h2-text-common margin-title_garantiya_srokov"  style="text-align: center;">Гарантия результата</div>
                        <div class="main-text-common desktop-text">
                            Исключаем человеческий фактор <span class="main-text-accent-common">двойным контролем:</span>
                            директора подразделений лично проверяют ведение 
                            бухгалтерского учёта, аудиторские отчёты и&nbsp;заключения,
                            участвуют во&nbsp;всех этапах ведения дел в&nbsp;арбитражных 
                            судах и&nbsp;судах общей юрисдикции. Для <span class="main-text-accent-common">комплексного 
                            решения вопросов</span> подключается команда из&nbsp;аудиторов, 
                            бухгалтеров и&nbsp;юристов.
                            <br>
                            <br>
                            Аудиторские и&nbsp;бухгалтерские услуги <span class="main-text-accent-common">застрахованы&nbsp;&mdash; </span>
                            при допущении ошибки штрафы и&nbsp;ответственность
                            ложатся на&nbsp;аудитора.
                            <br>
                            <br>
                            Мы&nbsp;за&nbsp;удобство и&nbsp;комфорт. Поэтому при оформлении 
                            лицензий вы&nbsp;можете воспользоваться <span class="main-text-accent-common">оплатой частями.</span>
                            100% предоплата не&nbsp;требуется.
                        </div>
                        <div class="main-text-common mobile-text">
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
                        </div>
                    </div>
                </div>
            [/col]
           [/row]
           [lightbox id="garantiya-srokov-dogovor-buhobsluzhivaniya"  padding="0px"]<img src="/wp-content/uploads/garantiya-srokov-dogovor-buhobsluzhivaniya.webp" alt="garantiya-srokov-dogovor-buhobsluzhivaniya"> [/lightbox]
           [lightbox id="garantiya-srokov-dogovor-licenzirovaniya"  padding="0px"]<img src="/wp-content/uploads/garantiya-srokov-dogovor-licenzirovaniya.webp" alt="garantiya-srokov-dogovor-licenzirovaniya"> [/lightbox]
           [lightbox id="garantiya-srokov-dogovor-audita"  padding="0px"]<img src="/wp-content/uploads/garantiya-srokov-dogovor-audita.webp" alt="garantiya-srokov-dogovor-audita"> [/lightbox]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/garantiya_srokov.css']);
        umbrella_add_custom_js_files(["/assets/js/blocks/garantiya_srokov.js"]);
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