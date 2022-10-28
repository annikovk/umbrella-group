<?php

function business_direction_shortcode($atts){
        $html = <<<EOTHTML
         [section id='business_direction_shortcode' class='business_direction_shortcode'  padding="0px"]
            <div class="first-screen-services-tabs show-for-small ">
                <ul class="tabs">
                    <li><a href="/services/audit/">Аудит</a></li>
                    <li><a href="/services/bukhgalterskie-uslugi/">Бухгалтерия</a></li>
                    <li><a href="/services/licensing/">Лицензирование</a></li>
                    <li><a href="/services/register-elimination/">Регистрация</a></li>
                    <li><a href="/services/register-elimination/likvidatsiya-ooo/">Ликвидация</a></li>
                    <li><a href="/services/licensing/registracija-tovarnogo-znaka/">Товарный знак</a></li>
                    <li><a href="/services/services-le/">Юридические услуги</a></li>
                    <li><a href="/services/sdelki-s-nedvizhimostyu/">Сделки с недвижимостью</a></li>
                </ul>
            </div>
            <div class="business_direction_screen pd">
                    <div class="row">
                        <div class="col">
                            <h2 class="title_sections_directions">
                            Чем вы хотите заниматься?
                            </h2>
                            <div class="list_directions">
                                <a href="#" class="item_directions">
                                    <img src="https://taxlab.ru/wp-content/uploads/dir4.jpg"/>
                                    <p class="title_directions">Красота и здоровье</p>
                                </a>
                                <a href="#" class="item_directions">
                                    <img src="https://taxlab.ru/wp-content/uploads/dir5.jpg"/>
                                    <p class="title_directions">Красота и здоровье</p>
                                </a>
                                <a href="#" class="item_directions">
                                    <img src="https://taxlab.ru/wp-content/uploads/dir3.jpg"/>
                                    <p class="title_directions">Красота и здоровье</p>
                                </a>
                                <a href="#" class="item_directions">
                                    <img src="https://taxlab.ru/wp-content/uploads/dir2.jpg"/>
                                    <p class="title_directions">Красота и здоровье</p>
                                </a>
                                <a href="#" class="item_directions">
                                    <img src="https://taxlab.ru/wp-content/uploads/dir1.jpg"/>
                                    <p class="title_directions">Красота и здоровье</p>
                                </a>
                            </div>  

                        </div>
                </div>
                
            </div>
         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/business_direction.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/business_direction.js']);
        return $html;
    }

add_shortcode('business-directions', 'business_direction_shortcode');

