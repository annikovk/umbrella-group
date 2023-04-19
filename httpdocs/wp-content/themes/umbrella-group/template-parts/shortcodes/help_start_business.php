<?php
 function isMobile() {
            $useragent=$_SERVER['HTTP_USER_AGENT'];
            return preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        }

class help_start_business
{
    public $atts;

    public function generate_shortcode()
    {
         if (isMobile()) {

        $html = <<<EOHTML

        EOHTML;

         } else {
        $html = <<<EOHTML
         [section id='help_start_business'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="top_padding_help_start_business">
                    <div class="title-h1-text-common">Помогаем открыть бизнес &laquo;под&nbsp;ключ&raquo;</div>
                    <div class="main-text-common">
                        С&nbsp;1990 года мы&nbsp;накопили опыт, чтобы не&nbsp;только решать отдельные вопросы,
                        но&nbsp;и&nbsp;открывать бизнес &laquo;под ключ&raquo;.
                    </div>
                    <div class="help_start_business_content">
                        <div class="left_text_help_start_business_content">
                            <div class="left_text_help_start_business_layout">
                                <div class="main-text-common text_desktop_help_start_business">
                                    Мы&nbsp;помогаем предпринимателям открыть своё дело под ключ и&nbsp;в&nbsp;сжатые сроки. 
                                    Без лишних хлопот.
                                </div>
                                <div class="main-text-common text_desktop_help_start_business">
                                    <span class="main-text-accent-common">
                                        Например, вы&nbsp;решили открыть языковую школу.
                                    </span> 
                                    <br>
                                    Зарегистрируем организацию и&nbsp;товарный знак, 
                                    получим образовательную лицензию, подберём помещение, подходящее под все требования, проверим программу обучения 
                                    и&nbsp;подготовим к&nbsp;проверкам Роспотребнадзора и&nbsp;Минобра. Поможем установить онлайн-кассу. 
                                    Если вы&nbsp;хотите, чтобы в&nbsp;школе проигрывлась музыка, например, на&nbsp;ресепшене, 
                                    то&nbsp;заключим договор с&nbsp;РАО или ВОИС&nbsp;&mdash; аккредитованными организациями по&nbsp;коллективному 
                                    управлению интеллектуальными правами. <br><br>
                                    Мы&nbsp;готовы взять на&nbsp;себя решение всех юридических и&nbsp;бухгалтерских вопросов. <br><br>
                                    Экономим ваше время и&nbsp;деньги, защищаем от&nbsp;&laquo;метода проб и&nbsp;ошибок&raquo;.
                                </div>   
                                <div class="main-text-common text_mobile_help_start_business">
                                    <span class="main-text-accent-common">
                                        Например, вы&nbsp;решили открыть языковую школу.
                                    </span> 
                                    <br>
                                    <div class="main-text-common" id="spoiler" style="display:none">
                                        Зарегистрируем организацию и&nbsp;товарный знак, 
                                        получим образовательную лицензию, подберём помещение, подходящее под все требования, проверим программу обучения 
                                        и&nbsp;подготовим к&nbsp;проверкам Роспотребнадзора и&nbsp;Минобра. Поможем установить онлайн-кассу. 
                                        Если вы&nbsp;хотите, чтобы в&nbsp;школе проигрывлась музыка, например, на&nbsp;ресепшене, 
                                        то&nbsp;заключим договор с&nbsp;РАО или ВОИС&nbsp;&mdash; аккредитованными организациями по&nbsp;коллективному 
                                        управлению интеллектуальными правами. <br><br>
                                        Мы&nbsp;готовы взять на&nbsp;себя решение всех юридических и&nbsp;бухгалтерских вопросов. <br><br>
                                        Экономим ваше время и&nbsp;деньги, защищаем от&nbsp;&laquo;метода проб и&nbsp;ошибок&raquo;.
                                    </div>
                                    <div class="action-text-common" 
                                         title="Click to Show/Hide Content" 
                                         onclick="spoilerContent()">
                                         <span id="spoiler-btn-show">Посмотрите, что сделаем&nbsp;мы</span>
                                         <span id="spoiler-btn-hide" style="display:none">Свернуть</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="rite_text_help_start_business card-shadow-common">
                            <div class="border_card_help">
                                  <div class="legend_position">
                                    <span class="mute-text-legend text_help_start_business_legend">
                                        Клиенты о результатах сотрудничества
                                    </span>
                                  </div>
                                  <div class="content_layout_help_start_business">
                                    <div class="text_help_start_business main-text-italic-common">
                                        &laquo;Разгрузили голову владельца бизнеса&raquo;
                                    </div>   
                                    <div class="text_help_start_business main-text-italic-common">
                                        &laquo;Вникли в&nbsp;проблему и&nbsp;нашли оптимальное решение&raquo;
                                    </div>
                                    <div class="text_help_start_business main-text-italic-common">
                                        &laquo;Всё предельно ясно, сроки выдержаны. Оперативно и&nbsp;качественно&raquo;
                                    </div>
                                    <div>
                                        <a class="action-text-common" href="/about/feedback/">Посмотреть отзывы</a>
                                    </div>
                                  </div>
                             </div>
                        </div>
                   </div>
                </div>
            [/col]
           [/row]
         [/section]
        EOHTML; }
        umbrella_add_custom_css_files(['/assets/css/common-style.css']);
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