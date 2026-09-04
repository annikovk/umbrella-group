<?php
class new_main_first_screen
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOT
         [section id='new_main_first_screen' class='new_main_first_screen'  padding="0px"]
            <div class="first-screen-services-tabs show-for-small">
                <ul class="tabs">
                    <li><a href="/services/audit/">Аудит</a></li>
                    <li><a href="/services/bukhgalterskie-uslugi/">Бухгалтерия</a></li>
                    <li><a href="/services/licensing/">Лицензирование</a></li>
                    <li><a href="/services/register-elimination/">Регистрация</a></li>
                    <li><a href="/services/register-elimination/likvidatsiya-ooo/">Ликвидация</a></li>
                    <li><a href="/services/licensing/registracija-tovarnogo-znaka/">Товарный знак</a></li>
                    <li><a href="/services/services-le/">Юридические услуги</a></li>
                </ul>
            </div>
            <div class="first-screen">
                <div class="main-banner">
                    <div class="row">
                     
                        <div class="col">
                            <div class="main-banner-content">
                                <div class="left">
                            
                                    <h1 class="sub-header">
                                        Экспертное сопровождение  бизнеса в Новосибирске
                                    </h1>   
                                    <p style="padding-bottom: 60px;" class="text-like-h1">
                                        Сибирский налоговый семинар 2027
                                    </p>
                           
                                    <p class="description">
                                        2026 год был только началом. И уже кардинально изменил налоговую реальность российского бизнеса. Но на этом изменения не заканчиваются. На семинаре эксперты разберут, с чем бизнес входит в 2027 год, какие изменения уже известны и как они повлияют на работу компаний.
                                    </p>
                                    <p>Спикеры:</p>
                                    <ul class="description">
                                        <li>Софья Гладкова - председатель Общественного совета России при УФНС по Новосибирской области</li>
                                        <li>Никита Демиденко - ведущий налоговый юрист по Новосибирской области, директор ООО "Юрсервис" (Входит в ГК Umbrella Group)</li>
                                    </ul>

                                    <a href="/blog/nalogovi-seminar-2027/" class="hide-for-small button primary lowercase banner-button"> Подробнее о семинаре
                                    </a>
                                    
                             

                                     <!--p style="font-family: PT Serif, sans-serif; text-align: left; margin-top: unset; font-weight: 600; font-size: 36px; line-height: 36px; "> Семинар по налоговой <br> реформе 2026</!--p> 
                                       
                                    <p class="description">
                                    Думаете, что бизнесу в 2026 году остается только сокращать расходы и людей?
                                    </p>
                                    <p class="description">
                                    Или вовсе закрываться… Не понимаете, как работать дальше с новыми налогами и усилением контроля?
                                    </p>
                                    <p class="description">
                                    В 2026 году правила действительно меняются.
                                    Но стратегия «закрыться или затаиться» — не решение.
                                    </p>
                                    <p class="description">
                                    На семинаре разберем, как перестроить бизнес-модель под новую налоговую реальность, чтобы сохранить прибыль и снизить личные риски собственников и руководителей.
                                    </p>
                                    <p class="description">
                                    Без воды. Только практические инструменты.
                                    </p>
                                    
                                    <div-- style="padding-top: 50px;"><a href="/blog/nalogovaya-perestroyka-seminar-2026/" class="hide-for-small button primary lowercase banner-button">Регистрация на семинар
                                    </a></div-->


                                 </div>
                                <div class="right">
                                    <img src="https://taxlab.ru/wp-content/uploads/spikery-banner-4.png" loading="lazy">
                                </div>
                                    
                                    <a href="/blog/nalogovi-seminar-2027/" class="show-for-small button primary lowercase banner-button">Подробнее о семинаре
                                    </a>
                                 
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ratings">
                    [row style="collapse" width="full-width"]
                    [col span__sm="12"]
                    [row_inner_1 style="collapse" v_align="middle"]
                    [col_inner_1 span="4" span__sm="12" padding="0px 0px 0px 0px" margin="0px 0px 0px 0px"]
                    [featured_box img="13631" inline_svg="0" img_width="95" pos="left"]
                    <p class="laurel-place">ТОП-5</p>
                    <p class="laurel-icon-header">юридических компаний Новосибирска<sup style="font-size: 6pt;"> 3</sup></p>
                    <p class="laurels-icons-year">июнь, 2025</p>
                    [/featured_box]
                    [/col_inner_1]
                    [col_inner_1 span="4" span__sm="12"]
                    [featured_box img="13809" inline_svg="0" img_width="95" pos="left" ]
                    <p class="laurel-place">1-ое место</p>
                    <p class="laurel-icon-header">среди бухгалтерских компаний Новосибирска<sup style="font-size: 6pt;"> 2</sup></p>
                    <p class="laurels-icons-year">2023</p>
                    [/featured_box]
                    [/col_inner_1]
                    [col_inner_1 span="4" span__sm="12"]
                    [featured_box img="13809" inline_svg="0" img_width="95" pos="left"]
                    <p class="laurel-place">1-ое место</p>
                     <p class="laurel-icon-header">в номинации <br>«Корпоративное право»<sup style="font-size: 6pt;"> 1</sup></p>
                    <p class="laurels-icons-year">июнь, 2025</p>
                    [/featured_box]
                    [/col_inner_1]
            <div class="laurel-footnote">1 — по версии рейтинга журнала «Деловой квартал» 2 — по итогам рейтинга «Планка» (planka.ru) 3 — по версии журнала «Деловой квартал»;</div>
                    [/row_inner_1]
            
                    [/col]
                    [/row]
                </div>
            </div>
            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox"]
            [contact-form-7 id="13619" title="Форма на главной (узнать стоимость услуг)"]
            [/lightbox]

            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox-seminar"]
            [contact-form-7 id="080e72d" title="Форма для семинара"]
            [/lightbox]

            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightboxx"]
            [contact-form-7 id="4af4748" title="Форма на главной (узнать стоимость услуг)_copy"]
            [/lightbox]




         [/section]
        EOT;
        umbrella_add_custom_css_files(['/assets/css/blocks/new_main_first_screen.css']);
        umbrella_add_custom_css_files(['/assets/css/blocks/block-laurels.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/block-laurels.js']);
        return $html;
    }

}

function new_main_first_screen_shortcode($atts)
{
    $shortcode = new new_main_first_screen();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('new_main_first_screen', 'new_main_first_screen_shortcode');

