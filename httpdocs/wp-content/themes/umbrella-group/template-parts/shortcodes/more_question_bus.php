<?php
function more_question_busnisse_shortcode($atts)
{
    $html = <<<EOTHTML
         [section id='more_question_bus' class='more_question_bus'  padding="0px"]
            <div class="more_question_busnisse_screen pd">
                <div class="row">
                    <div class="col">
                        <div class="more_question_wrap">
                            <h2 class="more_question_title_bus">Много вопросов по запуску своего дела?</h2>
                            <p class="question_bold">Задайте их экспертам</p>
                            <p>Приходите в уютный офис, познакомьтесь со специалистами по лицензированию, бухгалтерии и налогам, корпоративному праву. Обсудите с ними план действий за чашкой ароматного чая. Расспросите обо всём, что волнует на старте дела.
                            </p>
                            <a href="#form_calc" class="btn_more_footer btn_scrolls">Бесплатная консультация</a>
                        </div>
                    </div>
                </div>         
            </div>
         [/section]
        EOTHTML;
    umbrella_add_custom_css_files(['/assets/css/blocks/more_question_bus.css']);
    umbrella_add_custom_js_files(['/assets/js/blocks/more_question_bus.js']);
    return $html;
}

add_shortcode('more_question_busnisse', 'more_question_busnisse_shortcode');