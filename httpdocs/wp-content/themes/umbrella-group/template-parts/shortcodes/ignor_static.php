<?php

function statick_ignor_shortcode($atts){
        $html = <<<EOTHTML
         [section id='statick_ignor' class='statick_ignor'  padding="0px"]
            <div class="statick_ignor_screen pd">
                    <div class="row">
                        <div class="col">
                            <h2 class="title_sections_startup">
                            Игнорируем статистику по России<br> и увеличиваем срок жизни бизнеса
                            </h2>
                            <h3>Помогаем открыть бизнес с минимумом рисков.</h3>
                            <div class="static_desc">
                               <p>
                               <span style="font-weight:700">Статистика ГК Umbrella Group:</span><br> На рынке 32 года. 5 лет назад, в 2017 году, комплексно открыли<br> 46 компаний. Зарегистрировали, получили для них лицензию, ведём<br> бухгалтерию и правовой консалтинг. В 2022 году 89 % из них<br> продолжают функционировать и празднуют 5-летний юбилей.<br> Некоторые клиенты ведут бизнес уже по 15, 16, 17 лет.
                               <a href="#customer_reviews" class="link_reviews btn_scrolls">Посмотрите их отзывы</a>
                               </p>
                               <img src="https://taxlab.ru/wp-content/uploads/diagrqam-ignor-bus.svg" class="statick_ignor_desc">
                               <img src="https://taxlab.ru/wp-content/uploads/Диаграмма.jpg" class="statick_ignor_mob">
                            </div>
                        </div>
                    </div>

            </div>
         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/static-ignor.css']);
        return $html;
    }

add_shortcode('statick_ignor', 'statick_ignor_shortcode');