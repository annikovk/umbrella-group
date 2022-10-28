<?php
function business_first_screen_shortcode($atts){
        $html = <<<EOTHTML
         [section id='new_main_first_screen' class='bus_main_first_screen'  padding="0px"]
            <div class="first-screen-services-tabs show-for-small">
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
            <div class="bus-first-screen">
                    <div class="row">
                        <div class="col">
                            <div class="top_content">
                                <h1>Откроем бизнес под ключ в Новосибирске:</h1>
                                <h2>без хлопот, в сжатые сроки, с гарантиями в договоре</h2>
                            </div>
                            <div class="bus_capabilities">
                                <div class="bus_capabilities_list">
                                    <ul class="capabilities_list">
                                        <li>Зарегистрируем вашу компанию</li>
                                        <li>Откроем расчётный счёт и поможем с онлайн-кассой</li>
                                        <li>Подберём
                                         помещение</li>
                                        <li>Получим для вас лицензию</li>
                                        <li>Разработаем и зарегистрируем товарный знак</li>
                                        <li>Организуем бухгалтерский учёт</li>
                                        <li>Решим все вопросы с документами и договорами</li>
                                        <li>Поможем получить поддержку от государства</li>
                                        <li>Проконсультируем на всех этапах. 24/7 на связи по всем вопросам</li>
                                    </ul>
                                    <a href="#" class="btn_bus get_plans_price">Получить план работ и стоимость</a>
                                </div>
                                <div class="bus_first-screen_video" style="background:url('https://taxlab.ru/wp-content/uploads/ks.jpg');">
                                <a href="">
                                <img src="https://taxlab.ru/wp-content/uploads/Group-556.svg"> Посмотрите, как мы помогли открыть студию красоты
                                </a>
                                </div>
                            </div>
                        </div>
                </div>
                
            </div>
            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox"]
            [contact-form-7 id="13619" title="Форма на главной (узнать стоимость услуг)"]
            [/lightbox]
         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/business_first_screen.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/business_first_screen.js']);
        return $html;
    }

add_shortcode('business_first_screen', 'business_first_screen_shortcode');

