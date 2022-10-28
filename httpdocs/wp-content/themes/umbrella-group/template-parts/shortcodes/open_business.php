<?php

function open_business_shortcode($atts){
        $html = <<<EOTHTML
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
         [section id='open_business' class='open_business'  padding="0px"]
             
            <div class="open_business_screen pd">
                <div class="row">
                    <div class="col">
                        <h2 class="title_open_business">Откроем бизнес под ключ</h2>
                        <div class="tabs">
                          <ul id="tabs-nav">
                            <li><a href="#beauty">Красота и здоровье</a></li>
                            <li><a href="#trade">Торговля</a></li>
                            <li><a href="#food">Общественное питание</a></li>
                            <li><a href="#educational">Образовательные услуги</a></li>
                            <li><a href="#other">Другое</a></li>
                          </ul>
                          <div id="tabs-content">
                            <div id="beauty" class="tab-content">
                              <div class="wrap_open_business_tab">
                                <img src="https://taxlab.ru/wp-content/uploads/krasota.jpg" class="content_tab_img" style="margin-left: -62px;">
                                <ul class="list_type_busnisse">
                                    <li>Стоматологический кабинет</li>
                                    <li>Косметологический кабинет</li>
                                    <li>Медицинская клиника</li>
                                    <li>Массажный салон</li>
                                    <li>Салон красоты</li>
                                    <li>Cпа-салон</li>
                                    <li>Аптека</li>
                                </ul>
                              </div>
                                <div class="swiper step_slider_busnisse">
                                  <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">1</span>
                                        </div>
                                        <div class="content_step">
                                            Бесплатно проконсультируем по открытию бизнеса. Можно в офисе или по телефону.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">2</span>
                                        </div>
                                        <div class="content_step">
                                           Зарегистрируем ИП / ООО / НКО. Выберем оптимальный вариант и подготовим документы.
                                           <a href="#" class="btn_modal_step" data-modal="how-do">Как мы это сделаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">3</span>
                                        </div>
                                        <div class="content_step">
                                            Откроем расчётный счёт в банке.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">4</span>
                                        </div>
                                        <div class="content_step">
                                            Подберём, зарегистрируем и установим онлайн-кассы.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">5</span>
                                        </div>
                                        <div class="content_step">
                                            Подберём помещение. Силами Жилфонда найдем прибыльное место.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">6</span>
                                        </div>
                                        <div class="content_step">
                                           Оформим лицензию на ваш вид детельности за максимально сжатые сроки.
                                            <a href="#" class="btn_modal_step" data-modal="exactly-do">Что конкретно делаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">7</span>
                                        </div>
                                        <div class="content_step">
                                           Отрисуем и зарегистрируем товарный знак. Защитим интеллектуальную собственность.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text chek_icon">
                                            
                                            </div>
                                        </div>
                                        <div class="content_step">
                                            Вы получаете действующий бизнес.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Помощь от государства
                                            </div>
                                        </div>
                                        <div class="content_step">
                                           Расскажем про меры поддержки малого и среднего бизнеса. Например, вы сможете бесплатно получить от государства…
                                           <a href="#" class="btn_modal_step" data-modal="help-state">Читать подробнее</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Для иностранных граждан
                                            </div>
                                        </div>
                                        <div class="content_step">
                                         Помогаем открыть бизнес в России. Оформим разрешение на временное пребывание в России. Мы станем вашим представителем в налоговой инспекции и передадим…
                                         <a href="#" class="btn_modal_step" data-modal="foreign-citizens">Читать подробнее</a>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="swiper-button-next">→</div>
                                  <div class="swiper-button-prev">←</div>
                                </div>
                            </div>

                            <div id="trade" class="tab-content">
                              <div class="wrap_open_business_tab">
                                <img src="https://taxlab.ru/wp-content/uploads/trade.jpg">
                                <ul class="list_type_busnisse">
                                    <li>Розничный магазин продуктов</li>
                                    <li>Интернет-магазин</li>
                                    <li>Магазин разливного пива</li>
                                </ul>
                              </div>
                                <div class="swiper step_slider_busnisse">
                                  <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">1</span>
                                        </div>
                                        <div class="content_step">
                                            Бесплатно проконсультируем по открытию бизнеса. Можно в офисе или по телефону.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">2</span>
                                        </div>
                                        <div class="content_step">
                                         Готовим коммерческое предложение и договор. Прописываем все действия с этапами, сроками и гарантиями, итоговую стоимость и госпошлины.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">3</span>
                                        </div>
                                        <div class="content_step">
                                           Зарегистрируем ИП / ООО / НКО. Выберем оптимальный вариант и подготовим документы.
                                           <a href="#" class="btn_modal_step" data-modal="how-do">Как мы это сделаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">4</span>
                                        </div>
                                        <div class="content_step">
                                            Откроем расчётный счёт в банке.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">5</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем, зарегистрируем и установим онлайн-кассы.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">6</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем помещение. Силами Жилфонда найдем прибыльное место.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">7</span>
                                        </div>
                                        <div class="content_step">
                                          Зарегистрируем в ЕГАИС, если потребуется (необходимо при продаже алкоголя).
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">8</span>
                                        </div>
                                        <div class="content_step">
                                          Оформим лицензию на ваш вид деятельности за максимально сжатые сроки.
                                           <a href="#" class="btn_modal_step" data-modal="exactly-do">Что конкретно делаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">9</span>
                                        </div>
                                        <div class="content_step">
                                           Отрисуем и зарегистрируем товарный знак. Защитим интеллектуальную собственность. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">10</span>
                                        </div>
                                        <div class="content_step">
                                           Поставим на учёт в Роспотребнадзор. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">11</span>
                                        </div>
                                        <div class="content_step">
                                            Заключим договор с РАО и ВОИС. Включайте любую музыку в помещении и не получайте штрафов.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text chek_icon">
                                            
                                            </div>
                                        </div>
                                        <div class="content_step">
                                            Вы получаете действующий бизнес.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Помощь от государства
                                            </div>
                                        </div>
                                        <div class="content_step">
                                           Расскажем про меры поддержки малого и среднего бизнеса. Например, вы сможете бесплатно получить от государства…
                                           <a href="#" class="btn_modal_step" data-modal="help-state">Читать подробнее</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Для иностранных граждан
                                            </div>
                                        </div>
                                        <div class="content_step">
                                         Помогаем открыть бизнес в России. Оформим разрешение на временное пребывание в России. Мы станем вашим представителем в налоговой инспекции и передадим…
                                         <a href="#" class="btn_modal_step" data-modal="foreign-citizens">Читать подробнее</a>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="swiper-button-next">→</div>
                                  <div class="swiper-button-prev">←</div>
                                </div>
                            </div>

                            <div id="food" class="tab-content">
                              <div class="wrap_open_business_tab">
                                <img src="https://taxlab.ru/wp-content/uploads/food.jpg" style="margin-left: -60px;">
                                <ul class="list_type_busnisse">
                                    <li>Ресторан</li>
                                    <li>Кофейня</li>
                                </ul>
                              </div>
                                <div class="swiper step_slider_busnisse">
                                  <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">1</span>
                                        </div>
                                        <div class="content_step">
                                            Бесплатно проконсультируем по открытию бизнеса. Можно в офисе или по телефону.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">2</span>
                                        </div>
                                        <div class="content_step">
                                         Готовим коммерческое предложение и договор. Прописываем все действия с этапами, сроками и гарантиями, итоговую стоимость и госпошлины.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">3</span>
                                        </div>
                                        <div class="content_step">
                                           Зарегистрируем ИП / ООО / НКО. Выберем оптимальный вариант и подготовим документы.
                                           <a href="#" class="btn_modal_step" data-modal="how-do">Как мы это сделаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">4</span>
                                        </div>
                                        <div class="content_step">
                                            Откроем расчётный счёт в банке.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">5</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем, зарегистрируем и установим онлайн-кассы.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">6</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем помещение. Силами Жилфонда найдем прибыльное место.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">7</span>
                                        </div>
                                        <div class="content_step">
                                          Зарегистрируем в ЕГАИС, если потребуется (необходимо при продаже алкоголя).
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">8</span>
                                        </div>
                                        <div class="content_step">
                                          Оформим лицензию на ваш вид деятельности за максимально сжатые сроки.
                                           <a href="#" class="btn_modal_step" data-modal="exactly-do">Что конкретно делаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">9</span>
                                        </div>
                                        <div class="content_step">
                                           Отрисуем и зарегистрируем товарный знак. Защитим интеллектуальную собственность. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">10</span>
                                        </div>
                                        <div class="content_step">
                                           Поставим на учёт в Роспотребнадзор. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">11</span>
                                        </div>
                                        <div class="content_step">
                                            Заключим договор с РАО и ВОИС. Включайте любую музыку в помещении и не получайте штрафов.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text chek_icon">
                                            
                                            </div>
                                        </div>
                                        <div class="content_step">
                                            Вы получаете действующий бизнес.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Помощь от государства
                                            </div>
                                        </div>
                                        <div class="content_step">
                                           Расскажем про меры поддержки малого и среднего бизнеса. Например, вы сможете бесплатно получить от государства…
                                           <a href="#" class="btn_modal_step" data-modal="help-state">Читать подробнее</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Для иностранных граждан
                                            </div>
                                        </div>
                                        <div class="content_step">
                                         Помогаем открыть бизнес в России. Оформим разрешение на временное пребывание в России. Мы станем вашим представителем в налоговой инспекции и передадим…
                                         <a href="#" class="btn_modal_step" data-modal="foreign-citizens">Читать подробнее</a>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="swiper-button-next">→</div>
                                  <div class="swiper-button-prev">←</div>
                                </div>
                            </div>

                            <div id="educational" class="tab-content">
                              <div class="wrap_open_business_tab">
                                <img src="https://taxlab.ru/wp-content/uploads/bt.jpg">
                                <ul class="list_type_busnisse">
                                    <li>Учебный центр доп. образования, профессионального обучения</li>
                                    <li>Онлайн-школа</li>
                                    <li>Детская школа</li>
                                    <li>Детский центр</li>
                                    <li>Детская секция</li>
                                </ul>
                              </div>
                                <div class="swiper step_slider_busnisse">
                                  <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">1</span>
                                        </div>
                                        <div class="content_step">
                                            Бесплатно проконсультируем по открытию бизнеса. Можно в офисе или по телефону.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">2</span>
                                        </div>
                                        <div class="content_step">
                                         Готовим коммерческое предложение и договор. Прописываем все действия с этапами, сроками и гарантиями, итоговую стоимость и госпошлины.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">3</span>
                                        </div>
                                        <div class="content_step">
                                           Зарегистрируем ИП / ООО / НКО. Выберем оптимальный вариант и подготовим документы.
                                           <a href="#" class="btn_modal_step" data-modal="how-do">Как мы это сделаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">4</span>
                                        </div>
                                        <div class="content_step">
                                            Откроем расчётный счёт в банке.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">5</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем, зарегистрируем и установим онлайн-кассы.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">6</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем помещение. Силами Жилфонда найдем прибыльное место.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">7</span>
                                        </div>
                                        <div class="content_step">
                                          Оформим лицензию на ваш вид деятельности за максимально сжатые сроки.
                                           <a href="#" class="btn_modal_step" data-modal="exactly-do">Что конкретно делаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">8</span>
                                        </div>
                                        <div class="content_step">
                                           Отрисуем и зарегистрируем товарный знак. Защитим интеллектуальную собственность. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">9</span>
                                        </div>
                                        <div class="content_step">
                                           Поставим на учёт в Роспотребнадзор. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text chek_icon">
                                            
                                            </div>
                                        </div>
                                        <div class="content_step">
                                            Вы получаете действующий бизнес.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Помощь от государства
                                            </div>
                                        </div>
                                        <div class="content_step">
                                           Расскажем про меры поддержки малого и среднего бизнеса. Например, вы сможете бесплатно получить от государства…
                                           <a href="#" class="btn_modal_step" data-modal="help-state">Читать подробнее</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Для иностранных граждан
                                            </div>
                                        </div>
                                        <div class="content_step">
                                         Помогаем открыть бизнес в России. Оформим разрешение на временное пребывание в России. Мы станем вашим представителем в налоговой инспекции и передадим…
                                         <a href="#" class="btn_modal_step" data-modal="foreign-citizens">Читать подробнее</a>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="swiper-button-next">→</div>
                                  <div class="swiper-button-prev">←</div>
                                </div>
                            </div>

                            <div id="other" class="tab-content">
                              <div class="wrap_open_business_tab">
                                <img src="https://taxlab.ru/wp-content/uploads/other.jpg">
                                <ul class="list_type_busnisse">
                                    <li>Строительная фирма</li>
                                    <li>Услуги дизайнера</li>
                                    <li>Агентство недвижимости</li>
                                    <li>Турагентство</li>
                                    <li>Рекламное агентство</li>
                                    <li>И другие виды деятельности</li>
                                </ul>
                              </div>
                                <div class="swiper step_slider_busnisse">
                                  <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">1</span>
                                        </div>
                                        <div class="content_step">
                                            Бесплатно проконсультируем по открытию бизнеса. Можно в офисе или по телефону.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">2</span>
                                        </div>
                                        <div class="content_step">
                                         Готовим коммерческое предложение и договор. Прописываем все действия с этапами, сроками и гарантиями, итоговую стоимость и госпошлины.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">3</span>
                                        </div>
                                        <div class="content_step">
                                           Зарегистрируем ИП / ООО / НКО. Выберем оптимальный вариант и подготовим документы.
                                           <a href="#" class="btn_modal_step" data-modal="how-do">Как мы это сделаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">4</span>
                                        </div>
                                        <div class="content_step">
                                            Откроем расчётный счёт в банке.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">5</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем, зарегистрируем и установим онлайн-кассы.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">6</span>
                                        </div>
                                        <div class="content_step">
                                           Подберем помещение. Силами Жилфонда найдем прибыльное место.
                                            <a href="#" class="btn_modal_step" data-modal="how-select">Как подбираем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">7</span>
                                        </div>
                                        <div class="content_step">
                                          Оформим лицензию на ваш вид деятельности за максимально сжатые сроки.
                                           <a href="#" class="btn_modal_step" data-modal="exactly-do">Что конкретно делаем?</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">8</span>
                                        </div>
                                        <div class="content_step">
                                           Отрисуем и зарегистрируем товарный знак. Защитим интеллектуальную собственность. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="title_step">
                                            <span class="num_step">9</span>
                                        </div>
                                        <div class="content_step">
                                           Поставим на учёт в Роспотребнадзор. 
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text chek_icon">
                                            
                                            </div>
                                        </div>
                                        <div class="content_step">
                                            Вы получаете действующий бизнес.
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Помощь от государства
                                            </div>
                                        </div>
                                        <div class="content_step">
                                           Расскажем про меры поддержки малого и среднего бизнеса. Например, вы сможете бесплатно получить от государства…
                                           <a href="#" class="btn_modal_step" data-modal="help-state">Читать подробнее</a>
                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="no_title_step">
                                            <div class="no_title_text krest_icon">
                                                Для иностранных граждан
                                            </div>
                                        </div>
                                        <div class="content_step">
                                         Помогаем открыть бизнес в России. Оформим разрешение на временное пребывание в России. Мы станем вашим представителем в налоговой инспекции и передадим…
                                         <a href="#" class="btn_modal_step" data-modal="foreign-citizens">Читать подробнее</a>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="swiper-button-next">→</div>
                                  <div class="swiper-button-prev">←</div>
                                </div>
                            </div>

                          </div>
                        </div>
                    </div>
                </div>
            </div>
        <div id="how-do" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Как мы это сделаем?</h2>
                    </div>
                    <div class="content_modal">
                        <ul>
                            <li>Проконсультируем, какая организационно-правовая форма подойдёт для вашей деятельности.</li>
                            <li>Проконсультируем по системе налогообложения.</li>
                            <li>Подготовим пакет документов.</li>
                            <li>Разработаем устав.</li>
                            <li>Изготовим печать.</li>
                            <li>Получим уведомление о постановке на учет в ФСС и ПФР.</li>
                            <li>Подготовим документы для открытия расчётного счета в любом выбранном банке.</li>
                            <li>Уплатим пошлины.</li>
                            <li>Передаём документы в налоговую (или Минюст для НКО).</li>
                            <li>Получаем свидетельства о регистрации в налоговой (или Минюсте для НКО)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="how-select" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Как подбираем?</h2>
                    </div>
                    <div class="content_modal">
                    <h3>Учитываем:</h3>
                        <ul>
                            <li>Соответствие помещения требованиям лицензирующего органа, чтобы вы без проблем получили лицензию.</li>
                            <li>Ваши пожелания по расположению и стоимости.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="exactly-do" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Что конкретно делаем?</h2>
                    </div>
                    <div class="content_modal">
                        <ul>
                            <li>Минимизируем риски отказа: проверим помещение, оборудование и штат на соответствие требованиям, подскажем, как устранить ошибки.</li>
                            <li>Оформим документы для подачи заявления.</li>
                            <li>Сопроводим экспертизы и проверки по заявке. Всё общение с органами берём на себя.</li>
                            <li>Консультируем и помогаем на всех стадиях лицензирования.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="help-state" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Помощь от государства</h2>
                    </div>
                    <div class="content_modal">
                    <h3>Вы сможете бесплатно получить от государства:</h3>
                        <ul>
                            <li>Льготные займы под 6,33 % годовых;</li>
                            <li>Поручительства по кредитам;</li>
                            <li>Повышение квалификации по охране труда;</li>
                            <li>Подготовку и сопровождение экспортных контрактов;</li>
                            <li>Обучающие программы и мастер-классы;</li>
                            <li>Содействие в продвижении продукции и услуг;</li>
                            <li>Регистрацию бизнеса для физических лиц;</li>
                        </ul>
                        <p>и многое другое.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="foreign-citizens" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Для иностранных граждан</h2>
                    </div>
                    <div class="content_modal">
                        <p>Мы передадим в налоговую все необходимые документы для регистрации бизнеса. При этом вам не нужно присутствовать лично. Отправьте нам документы и мы всё сделаем сами. 
                        </p>
                    </div>
                </div>
            </div>
        </div>



         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/open_business.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/open_bussnise.js']);
        return $html;
    }

add_shortcode('open_business', 'open_business_shortcode');