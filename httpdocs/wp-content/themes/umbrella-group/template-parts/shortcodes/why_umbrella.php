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
            <div class="block-padding_why-umbrella main-container_why-umbrella">
                <img alt="why_umbrella_background.png" 
                     src="https://taxlab.ru/wp-content/uploads/why_umbrella_background.png"/>
                <div class="card_why-umbrella card-shadow-common card-shadow_none_why-umbrella">
                    <div class="title-h1-text-common title_why_umbrella">Почему ГК&nbsp;Umbrella Group?</div>
                    <div class="citation_why-umbrella">
                        <div class="layout-quote_why-umbrella">
                            <div class="quote_why-umbrella">&laquo;</div>
                            <div class="main-text-italic-common">
                                Помогая российскому бизнесу родиться, жить, расти и&nbsp;развиваться,
                                мы&nbsp;помогаем России в&nbsp;целом, улучшая качество жизни каждого в&nbsp;отдельности.
                            </div>
                            <div class="quote_why-umbrella position-down_why-umbrella">&raquo;</div>
                        </div>
                        <div class="layout-author-quote_why-umbrella">
                            <img src="https://taxlab.ru/wp-content/uploads/Author_quote.png" 
                                 alt="Author_quote.png">
                            <div class="main-text-common">
                                <span class="main-text-accent-common">Александр Климов,</span>
                                <br>генеральный директор Umbrella&nbsp;Group
                            </div>
                        </div>
                   </div>
                   <div class="layout-thesis_why-umbrella">
                       <div class="number-thesis_why-umbrella">1</div>
                       <div class="main-text-common">Оправдываем доверие наших клиентов, ведь в&nbsp;нём&nbsp;&mdash; ключ к&nbsp;успеху.</div>
                       <div class="number-thesis_why-umbrella">2</div>
                       <div class="main-text-common">Мы&nbsp;знаем свое дело и&nbsp;любим свою работу.</div>
                       <div class="number-thesis_why-umbrella">3</div>
                       <div class="main-text-common">Более 30&nbsp;лет помогаем предпринимателям развивать бизнес и&nbsp;делать его более эффективным.</div>
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