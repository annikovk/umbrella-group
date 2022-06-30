<?php

class help_start_business
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='help_start_business'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
            <div class="top_padding_help_start_business">
                <div class="title_help_start_business">Помогаем открыть бизнес &laquo;под&nbsp;ключ&raquo;</div>
                <div class="text_help_start_business">
                    С&nbsp;1990 года мы&nbsp;накопили опыт, чтобы не&nbsp;только решать отдельные вопросы,
                    но&nbsp;и&nbsp;открывать бизнес &laquo;под ключ&raquo;.
                </div>
                <div class="help_start_business_content">
                    <div class="left_text_help_start_business_content">
                        <div class="text_help_start_business left_text_help_start_business_layout">
                            <div class="text_desktop_help_start_business">
                                Мы&nbsp;помогаем предпринимателям открыть своё дело под ключ и&nbsp;в&nbsp;сжатые сроки. Без лишних хлопот.
                            </div>
                            <div class="text_desktop_help_start_business">
                                <span class="accent_text">
                                    Например, вы&nbsp;решили открыть языковую школу.
                                </span> 
                                <br>
                                Зарегистрируем организацию и&nbsp;товарный знак, 
                                получим образовательную лицензию, подберём помещение, подходящее под все требования, проверим программу обучения 
                                и&nbsp;подготовим к&nbsp;проверкам Роспотребнадзора и&nbsp;Минобра. Поможем установить онлайн-кассу. 
                                Если вы&nbsp;хотите, чтобы в&nbsp;школе проигрывлась музыка, например, на&nbsp;ресепшене, 
                                то&nbsp;заключим договор с&nbsp;РАО или ВОИС&nbsp;&mdash; аккредитованными организациями по&nbsp;коллективному 
                                управлению интеллектуальными правами. 
                                Мы&nbsp;готовы взять на&nbsp;себя решение всех юридических и&nbsp;бухгалтерских вопросов. 
                                Экономим ваше время и&nbsp;деньги, защищаем от&nbsp;&laquo;метода проб и&nbsp;ошибок&raquo;.
                            </div>   
                            <div class="text_mobile_help_start_business">
                                <span class="accent_text">
                                    Например, вы&nbsp;решили открыть языковую школу.
                                </span> 
                                <br>
                                <div id="spoiler" style="display:none">
                                    Зарегистрируем организацию и&nbsp;товарный знак, 
                                    получим образовательную лицензию, подберём помещение, подходящее под все требования, проверим программу обучения 
                                    и&nbsp;подготовим к&nbsp;проверкам Роспотребнадзора и&nbsp;Минобра. Поможем установить онлайн-кассу. 
                                    Если вы&nbsp;хотите, чтобы в&nbsp;школе проигрывлась музыка, например, на&nbsp;ресепшене, 
                                    то&nbsp;заключим договор с&nbsp;РАО или ВОИС&nbsp;&mdash; аккредитованными организациями по&nbsp;коллективному 
                                    управлению интеллектуальными правами. 
                                    Мы&nbsp;готовы взять на&nbsp;себя решение всех юридических и&nbsp;бухгалтерских вопросов. 
                                    Экономим ваше время и&nbsp;деньги, защищаем от&nbsp;&laquo;метода проб и&nbsp;ошибок&raquo;.
                                </div>
                                <div class="links_download" 
                                     title="Click to Show/Hide Content" 
                                     type="button" 
                                     onclick="spoilerContent()">
                                     <span id="spoiler-btn-show">Посмотрите, что сделаем&nbsp;мы</span>
                                     <span id="spoiler-btn-hide" style="display:none">Свернуть</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rite_text_help_start_business">
                        <div class="border_card_help">
                              <div class="legend_position">
                                <span class="text_help_start_business text_help_start_business_legend">
                                    Клиенты о результатах сотрудничества
                                </span>
                              </div>
                              <div class="content_layout_help_start_business">
                                <div class="text_help_start_business text_help_start_business_italic">
                                    &laquo;Разгрузили голову владельца бизнеса&raquo;
                                </div>   
                                <div  class="text_help_start_business text_help_start_business_italic">
                                    &laquo;Вникли в&nbsp;проблему и&nbsp;нашли оптимальное решение&raquo;
                                </div>
                                <div  class="text_help_start_business text_help_start_business_italic">
                                    &laquo;Всё предельно ясно, сроки выдержаны. Оперативно и&nbsp;качественно&raquo;
                                </div>
                                <div>
                                    <a class="links_download" href="#">Посмотреть отзывы</a>
                                </div>
                              </div>
                         </div>
                    </div>
               </div>
            </div>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/help_start_business.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/help_start_business.js']);
        return $html;
    }

}

function help_start_business_shortcode($atts)
{
    $shortcode = new help_start_business();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('help_start_business', 'help_start_business_shortcode');

?>