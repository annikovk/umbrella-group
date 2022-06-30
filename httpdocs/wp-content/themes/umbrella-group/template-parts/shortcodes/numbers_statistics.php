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
            <h1 class="number_statistics_title">Закрываем несколько задач за 1&nbsp;визит</h1>
            <div class="numbers_statistics_header">
                <p class="number_statistics_main_text">
                   Перед встречей мы тщательно изучаем ваш вопрос и собираем команду
                   из специалистов так, чтобы комплексно решить вашу задачу. Например, за 1 визит
                   вы можете навестить вашего бухгалтера или специалиста по кадрам, задать вопросы
                   юристу, получить консультацию по регистрации товарного знака.
                </p>
                <p class="number_statistics_main_text">ГК&nbsp;Umbrella Group&nbsp;&mdash;&nbsp;это:</p>
            </div>
            <div class="container_numbers_statistics">
                <div class="numbers_statistics_block_array">
                <div class="numbers_statistics_block">
                    <div class="number_statistics_item">19</div>   
                    <div class="number_statistics_text">юристов</div>
                </div> 
                <div class="numbers_statistics_block">
                    <div class="number_statistics_item">15</div>   
                    <div class="number_statistics_text">бухгалтеров</div>
                </div>
                <div class="numbers_statistics_block">
                    <div class="number_statistics_item">10</div>   
                    <div class="number_statistics_text">аудиторов</div>
                </div>             
                <div class="numbers_statistics_block">
                    <div class="number_statistics_item">4</div>   
                    <div class="number_statistics_text">налоговых<br>консультанта</div>
                </div>
            </div>
                <div class="number_statistics_text numbers_statistics_footer">
                    <div class="container_text_numbers_statistics_footer">
                        <span>2 офиса &mdash; на правом и левом берегах&nbsp;Оби</span>
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