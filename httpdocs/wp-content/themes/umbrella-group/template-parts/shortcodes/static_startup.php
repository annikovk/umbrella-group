<?php

function statick_startup_shortcode($atts){
        $html = <<<EOTHTML
         [section id='statick_startup_shortcode' class='statick_startup_shortcode'  padding="0px"]
            <div class="business_direction_screen pd">
                    <div class="row">
                        <div class="col">
                            <h2 class="title_sections_startup">
                            Статистика<br> по стартапам в России
                            </h2>
                            <div class="static_desc">
                               <p>
                               По <a href="https://www.sberbank.com/common/img/uploaded/files/pdf/analytics/ust_bz.pdf" target="_blank">статистике Сбербанка:</a> вероятность разорения<br> предприятия особенно высока в течение первого года<br> существования и в возрасте старше 3-х лет.<br> 6,5% компаний закрываются за первые полгода от регистрации,<br> только 71% малых и средних предприятий живут более 3 лет. 
                               </p>
                               <img src="https://taxlab.ru/wp-content/uploads/Диаграмма.svg">
                            </div>
                            <div class="accrodion_top_problems">
                                <h2 class="title_problem_startup">Топ-5 проблем при открытии бизнеса:</h2>
                                <div class="b-problems">
                                    <div class="problems__item">
                                        <a href="#" class="problems__title js-problems-title ">
                                            <span class="problems__text"><span>1</span>Нехватка денег</span>
                                            <div class="problems__spoiler js-faq-rotate"><span class="problems__symbol ">↓</span></div> 
                                        </a>
                                        <div class="problems__content js-problems-content" >
                                             <div class="row_content">
                                                <p>При плохом планировании начинающий предприниматель столкнется с нехваткой денег. Неверный выбор системы налогообложения влечет за собой высокие налоги. Государственная поддержка, инвесторы и фонды увеличат ваш капитал — это возможность, о которой забывают многие новички. </p>
                                                <p>
                                                <b>Наш налоговый консультант подскажет</b>, как экономно организовать уплату налогов, а бухгалтер на старте бизнеса проанализирует риски и поможет спланировать расходы. Для увеличения бюджета заручимся поддержкой фондов, краудфандинга и государства. Юрист проверит договоры с инвесторами.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="b-problems">
                                    <div class="problems__item">
                                        <a href="#" class="problems__title js-problems-title problems__active">
                                            <span class="problems__text"><span>2</span>Сложности с лицензированием</span>
                                            <div class="problems__spoiler js-faq-rotate"><span class="problems__symbol ">↓</span></div> 
                                        </a>
                                        <div class="problems__content js-problems-content" style="display:block;">
                                            <div class="row_content">
                                                <p>В зависимости от вида деятельности, лицензии выдают МВД, Росздравнадзор, Россельхознадзор, Рособрнадзор, Ространснадзор, Роскомнадзор или Росгвардия. У каждого свои требования. Чтобы получить лицензию, нужно собрать пакет документов и соблюсти требования к помещению, оборудованию, сотрудникам и многие другие. Причём в регионах есть и дополнительные требования. В выдаче лицензии могут отказать, и госпошлина не возвращается. Ошибки предпринимателей дорого стоят: от повторных уплат госпошлины до переоборудования или смены помещения. 
                                                </p>
                                                <p>
                                                <b>Специалисты отдела лицензирования знают требования органов</b>, в том числе местные. Поэтому они помогут подготовиться к лицензированию, возьмут на себя общение с ведомствами, без помех получат лицензию.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="b-problems">
                                    <div class="problems__item">
                                        <a href="#" class="problems__title js-problems-title">
                                            <span class="problems__text"><span>3</span>Нет учёта финансов</span>
                                            <div class="problems__spoiler js-faq-rotate"><span class="problems__symbol ">↓</span></div> 
                                        </a>
                                        <div class="problems__content js-problems-content">
                                            <div class="row_content">
                                                <p>
                                                 Кассовые разрывы, кризис и стагнация бизнеса, завышенные ожидания — всё то, с чем столкнулись разорившиеся бизнесы. Ведение учета поступлений и расходов защитит вас от банкротства бизнеса. Оплата аренды, зарплаты и закупка продукции не будет задерживаться. 
                                                </p>
                                                <p>
                                                    <b>Наш бухгалтер организует финансовый учёт.</b> Вы сможете следить за движением денег, балансом и будете точно знать расходы и доходы.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="b-problems">
                                    <div class="problems__item">
                                        <a href="#" class="problems__title js-problems-title">
                                            <span class="problems__text"><span>4</span>Неправильное оформление договоров</span>
                                            <div class="problems__spoiler js-faq-rotate"><span class="problems__symbol ">↓</span></div> 
                                        </a>
                                        <div class="problems__content js-problems-content">
                                            <div class="row_content">
                                                <p>
                                                 В договорах должны быть прописаны достоверные условия и сроки выполнения работы, денежная неустойка за нарушение обязательств одной из сторон. Правильное оформление договоров защищает компанию от лишних расходов, недопониманий с клиентом и проблем в суде. 
                                                </p>
                                                <p>
                                                    <b>Помогут наши юрист и специалист по корпоративном праву.</b>Они знают, что предусмотреть в договорах, чтобы обезопасить ваш бизнес.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="b-problems">
                                    <div class="problems__item">
                                        <a href="#" class="problems__title js-problems-title">
                                            <span class="problems__text"><span>5</span>Новичок в юридических вопросах</span>
                                            <div class="problems__spoiler js-faq-rotate"><span class="problems__symbol ">↓</span></div> 
                                        </a>
                                        <div class="problems__content js-problems-content">
                                            <div class="row_content">
                                                <p>
                                                 Предприниматель должен владеть юридической базой. В каждой деятельности есть свои нормативные документы, правила и регламенты. МВД, налоговая служба, трудовая инспекция и другие органы имеют право вас оштрафовать или закрыть за несоблюдение законов.
                                                </p>
                                                <p>
                                                    <b>Наши юристы помогут</b> вам в этих вопросах и оповестят о новых законах.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/static_startup.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/static_startup.js']);
        return $html;
    }

add_shortcode('statick_startup', 'statick_startup_shortcode');

