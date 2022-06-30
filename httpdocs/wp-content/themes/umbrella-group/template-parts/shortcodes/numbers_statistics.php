<?php

class numbers_statistics
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='numbers_statistics'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div>
                    <div class="title-h1-text-common title_numbers-statistics">
                        Закрываем несколько задач за&nbsp;1&nbsp;визит
                    </div>
                    <div class="numbers_statistics_header main-text-common">
                        Перед встречей мы&nbsp;тщательно изучаем ваш вопрос и&nbsp;собираем команду
                        из&nbsp;специалистов так, чтобы комплексно решить вашу задачу. Например, за&nbsp;1&nbsp;визит
                        вы&nbsp;можете навестить вашего бухгалтера или специалиста по&nbsp;кадрам, задать вопросы
                        юристу, получить консультацию по&nbsp;регистрации товарного знака.
                        <br>
                        <br>
                        ГК&nbsp;Umbrella Group&nbsp;&mdash;&nbsp;это:
                        </div>
                    <div class="container_numbers_statistics">
                        <div class="numbers_statistics_block_array">
                            <div class="numbers_statistics_block text-align_numbers-statistics">
                                <div class="number_statistics_item">19</div>   
                                <div class="main-text-common">юристов</div>
                            </div> 
                            <div class="numbers_statistics_block text-align_numbers-statistics">
                                <div class="number_statistics_item">15</div>   
                                <div class="main-text-common">бухгалтеров</div>
                            </div>
                            <div class="numbers_statistics_block text-align_numbers-statistics">
                                <div class="number_statistics_item">10</div>   
                                <div class="main-text-common">аудиторов</div>
                            </div>             
                            <div class="numbers_statistics_block text-align_numbers-statistics">
                                <div class="number_statistics_item">4</div>   
                                <div class="main-text-common center-text_numbers-statistics">налоговых<br>консультанта</div>
                            </div>
                        </div>
                        <div class="number_statistics_text numbers_statistics_footer">
                            <div class=" text-align_numbers-statistics main-text-common container_text_numbers_statistics_footer">
                                <span class="main-text-accent-common">
                                    2 офиса &mdash; на правом и левом берегах&nbsp;Оби
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/numbers_statistics.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/numbers_statistics.js']);
        return $html;
    }

}

function numbers_statistics_shortcode($atts)
{
    $shortcode = new numbers_statistics();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('numbers_statistics', 'numbers_statistics_shortcode');

?>