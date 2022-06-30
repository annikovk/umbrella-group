<?php

class why_umbrella
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
         [section id='why_umbrella'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
            <div class="block_padding main_container_whe_umbrella">
                <img alt="why_umbrella_background.png" 
                     src="https://taxlab.ru/wp-content/uploads/why_umbrella_background.png"/>
                <div class="card_why_umbrella">
                    <h1 class="title_why_umbrella">Почему ГК Umbrella Group?</h1>
                    <div class="layout_quote">
                        <div class="text_quote_why_umbrella">
                            <div class="quote">&laquo;</div>
                            <div class="text_quote">
                            Помогая российскому бизнесу родиться, жить, расти и развиваться,
                            мы помогаем России в целом, улучшая качество жизни каждого в отдельности.
                            </div>
                            <div class="quote position_down">&raquo;</div>
                        </div>
                        <div class="author_quote_why_umbrella">
                            <img src="https://taxlab.ru/wp-content/uploads/Author_quote.png" 
                                 alt="Author_quote.png">
                            <div class="text_why_umbrella">
                                <span class="author_quote_accent">Александр Климов,</span>
                                <br>генеральный директор Umbrella Group
                            </div>
                        </div>
                   </div>
                   <div class="layout_thesis">
                       <div class="number_thesis">1</div>
                       <div class="text_why_umbrella">Оправдываем доверие наших клиентов, ведь в нём &mdash; ключ к успеху.</div>
                       <div class="number_thesis">2</div>
                       <div class="text_why_umbrella">Мы знаем свое дело и любим свою работу.</div>
                       <div class="number_thesis">3</div>
                       <div class="text_why_umbrella">Более 30 лет помогаем предпринимателям развивать бизнес и делать его более эффективным.</div>
                   </div>
                </div>
            </div>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/why_umbrella.css']);
        umbrella_add_custom_js_files(["/assets/js/blocks/why_umbrella.js"]);
        return $html;
    }

}

function why_umbrella_shortcode($atts)
{
    $shortcode = new why_umbrella();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('why_umbrella', 'why_umbrella_shortcode');

?>