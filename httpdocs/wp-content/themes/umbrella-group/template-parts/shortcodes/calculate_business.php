<?php

function calculate_busnisse_shortcode($atts){
        $html = <<<EOTHTML
         [section id='calculate_busnisse' class='calculate_busnisse'  padding="0px"]
             
            <div class="calculate_busnisse_screen pd">
                <div class="row">
                    <div class="col">
                        <h2 class="title_calculate_busnisse">Рассчитайте, сколько стоит <br>открыть бизнес под ключ сегодня</h2>
                        <h3 class="subtitle_calculate_busnisse">Выберите, чем будем заниматься?</h3>
                            <div class="dynamic_tariff">
                                <ul class="dynamic_tariff_menu">
                                    <li><a href="#">Красота и здоровье <span>↓</span></a>
                                        <ul class="dynamic_tariff_sub_menu">
                                            <li data-tab="tab-apteka-1" class="active" >Аптека1</li>
                                            <li data-tab="tab-apteka-2">Аптека2</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Торговля <span>↓</span></a>
                                        <ul class="dynamic_tariff_sub_menu">
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Общественное питание <span>↓</span></a>
                                        <ul class="dynamic_tariff_sub_menu">
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Образовательные услуги <span>↓</span></a>
                                        <ul class="dynamic_tariff_sub_menu">
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                        </ul>
                                    </li>
                                    <li><a href="#">Другое <span>↓</span></a>
                                        <ul class="dynamic_tariff_sub_menu">
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                            <li>Аптека</li>
                                        </ul>
                                    </li>
                                </ul>
                                <div class="dynamic_tariff_content">
                                    <div id="tab-apteka-1" class="tab-content current">
                                        <div class="dynamic_tariff_table">
                                            <div class="dynamic_tariff_col tariff_col_naming">
                                                <h3 class="tariff_col_title">Выберите способ:</h3>
                                                <div class="list_tariff">
                                                    <div class="item_tarif">Регистрация ООО и открытие расчётного счёта</div>
                                                    <div class="item_tarif">Регистрация НКО</div>
                                                    <div class="item_tarif">Онлайн-касса</div>
                                                    <div class="item_tarif">Лицензирование</div>
                                                    <div class="item_tarif">Подбор помещения</div>
                                                    <div class="item_tarif">Регистрация товарного знака</div>
                                                    <div class="item_tarif">Помощь с получением поддержки и финансирования от государства</div>
                                                </div>
                                            </div>
                                            <div class="dynamic_tariff_col tariff_col_one">
                                                <h3 class="tariff_col_title">Поэтапно</h3>
                                                <p class="tarif_desc">Самый выгодный способ: все этапы включены в один договор, поэтому общая стоимость ниже. Часть работ ведём параллельно, поэтому экономим время.
                                                </p>

                                                <a href="#" class="btn_tariff">Получить предложение</a>

                                                <div class="list_tariff">
                                                    <div class="item_tarif">Регистрация ООО и открытие расчётного счёта</div>
                                                    <div class="item_tarif">Регистрация НКО</div>
                                                    <div class="item_tarif">Онлайн-касса</div>
                                                    <div class="item_tarif">Лицензирование</div>
                                                    <div class="item_tarif">Подбор помещения</div>
                                                    <div class="item_tarif">Регистрация товарного знака</div>
                                                    <div class="item_tarif">Помощь с получением поддержки и финансирования от государства</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-apteka-2" class="tab-content">
                                    TAB 2 
                                    </div>
                                    <div id="tab-3" class="tab-content">
                                    TAB 3 
                                    </div>                               
                                </div>
                            </div>
                    </div>
                </div>
            </div>
         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/calculate_business.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/calculate_business.js']);
        return $html;
    }

add_shortcode('calculate_business', 'calculate_busnisse_shortcode');