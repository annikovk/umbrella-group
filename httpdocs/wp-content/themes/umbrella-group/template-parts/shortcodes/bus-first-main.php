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
                                    <div class="gradient-block"></div>
                                    <a href="#form_calc" class="btn_bus get_plans_price btn_scrolls btn_desc_price">Получить план работ и стоимость</a>
                                    <a href="#form_calc" class="btn_bus get_plans_price btn_scrolls btn_mobile_price">План работ и стоимость</a>
                                </div>
                                <div class="bus_first-screen_video" style="background:url('https://taxlab.ru/wp-content/uploads/ks.jpg');">
                                <a href="#" class="btn_modal_screen_one" data-modal="salon">
                                <img src="https://taxlab.ru/wp-content/uploads/Group-556.svg" class="play_desc">
                                <img src="https://taxlab.ru/wp-content/uploads/Group-556-1.svg" class="play_mob"> Посмотрите, как мы помогли открыть студию красоты
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
                <div id="salon" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Студия красоты <br>SofArt</h2>
                        <h3 class="modal_sub_title">Открытие и расцвет бизнеса за 2 года</h3>
                    </div>
                    <div class="content_modal">
                        <div class="ceo_salon salon_pd">
                            <img src="https://taxlab.ru/wp-content/uploads/image-113.jpg">
                            <div class="about_ceo_salon">
                                <p><span style="font-weight:700">Евгения Перова,</span> хозяйка салона, в бьюти-сфере с 2015 года.</p>
                                <p>В 2020 году она решила открыть салон мечты и назвала его SofArt, в честь своих детей — Софии и Артёма.</p>
                            </div>
                        </div>
                        <div class="salon_desc salon_pd">
                            Мы зарегистрировали ИП, оформили лицензии. Подобрали помещение в 15-ти минутах от метро Студенческая. Большое пространство и нетипичная планировка сделали место особенным, отличающимся от других салонов красоты.
                        </div>
                        <div class="mokup_salon salon_pd">
                            <img src="https://taxlab.ru/wp-content/uploads/image-112.jpg">
                            <img src="https://taxlab.ru/wp-content/uploads/image-115.jpg">
                            <img src="https://taxlab.ru/wp-content/uploads/image-114.jpg">
                        </div>
                        <div class="salon_desc salon_pd">
                           Студия расширяет базу клиентов и получает множество положительных отзывов.
                        </div>
                        <div class="video_salon salon_pd">
                            <iframe src="https://vk.com/video_ext.php?oid=620031605&id=456239042&hash=5f4f72d8e64e017c" width="100%" height="100%" frameborder="0" allowfullscreen="1" allow="autoplay; encrypted-media; fullscreen; picture-in-picture"></iframe>
                            <div class="timeline_video">
                            <img src="https://taxlab.ru/wp-content/uploads/Vector-4.svg"><span class="time_video">0:29</span><span class="title_video_md">«Каждый день мы дарим Вам красоту»</span>
                            </div>
                        </div>
                        <div class="salon_desc salon_pd">
                           С этого года Евгения открыла учебный класс и запустила курсы маникюра, чтобы передавать опыт и помогать другим осваиваться в профессии.
                        </div>
                        <img src="https://taxlab.ru/wp-content/uploads/image-111.jpg">
                    </div>
                </div>
            </div>
        </div>

        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/business_first_screen.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/business_first_screen.js']);

    return $html;
    }

add_shortcode('business_first_screen', 'business_first_screen_shortcode');

