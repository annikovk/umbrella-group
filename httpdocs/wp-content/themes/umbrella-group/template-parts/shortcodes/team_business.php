<?php

function team_business_shortcode($atts)
{
    $html = <<<EOTHTML
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css"/>
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
         [section id='team_business' class='team_business'  padding="0px"]
            <div class="open_business_screen pd">
                <div class="row">
                    <div class="col">
                        <h2 class="team_title_business">Откроем финансово устойчивый бизнес</h2>
                        <div class="wrap_team">
                            <div class="about_team">
                                <img src="https://taxlab.ru/wp-content/uploads/image-100.jpg">
                                <h2 class="team_title_business_mob">Откроем финансово устойчивый бизнес</h2>
                                <div class="content_about_team">
                                    <p class="title_about_team">Комплексный подход</p>

                                   <p class="line-up"> С вами будет работать <a href="#" class="btn_modal_teams" data-modal="teams">команда из 5 человек</a>: юрист, бухгалтер, специалисты по корпоративному праву и лицензиям. 

                                    <p class="text_about_team">
                                    Юрист поможет с оформлением договоров, необходимых для вашего бизнеса, а также будет участвовать в спорных вопросах с контрагентами или госорганами. Бухгалтер и налоговый консультант подскажут, как законно экономить на налогах и не переплачивать их. Специалист по лицензиям поможет с оформлением лицензии в сжатые сроки, а вишенкой на торте станет регистрация товарного знака.
                                    Ваш бизнес будет под контролем разнопрофильных специалистов. Они обезопасят вас от непредвиденных расходов и ошибок.
                                    </p>
                                </div>
                            </div>
                            <div class="b-teams b-teams-first">
                            <a href="#" class="teams__title">
                                <span class="teams__text">Рассчитаем стоимость без доплат</span>
                            </a>
                            <div class="teams__contents" >
                                 <div class="teams_accrodion_content no_tabs">
                                    <p>
                                    Заранее обговариваем стоимость открытия бизнеса и фиксируем в договоре. Не допустим непредвиденных трат со стороны клиента. Посмотрите пример коммерческого предложения:
                                    </p>
                                    <div class="img_teams_content">
                                        <a href="https://taxlab.ru/wp-content/uploads/image-96-1.jpg" data-fancybox><img src="https://taxlab.ru/wp-content/uploads/image-96-1.jpg"></a>
                                        <img src="https://taxlab.ru/wp-content/uploads/Амбрелла.svg" class="shtamp">
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="b-teams b-teams-second">
                        <a href="#" class="teams__title">
                            <span class="teams__text">Фиксируем сроки и неустойки в договоре</span>
                            <div class="teams__spoiler js-faq-rotate"><span class="teams__symbol ">↓</span></div> 
                        </a>
                        <div class="teams__contents" >
                             <div class="teams_accrodion_content">
                                <p>
                                На первой консультации мы рассчитаем сроки каждого этапа. Учитываем время на проверку документов государственными органами (ФНС, Минздравом, Роспотребнадзором, МЧС и другими). Посмотрите гарантию срока в фрагменте договора:
                                </p>
                                <div class="img_teams_content">
                                    <a href="https://taxlab.ru/wp-content/uploads/image-89.jpg" data-fancybox><img src="https://taxlab.ru/wp-content/uploads/image-89.jpg"></a>
                                </div>
                            </div>
                        </div>
                        </div>
                            <div class="accrodion_top_teams">
                                <div class="b-teams">
                                    <div class="teams__item">
                                        <a href="#" class="teams__title js-teams-title opn">
                                            <span class="teams__text">Берем на себя общение с банками и органами <div class="teams__spoiler js-faq-rotate"><span class="teams__symbol ">↓</span></div> </span>
                                            
                                        </a>
                                        <div class="teams__content js-teams-content" >
                                             <div class="teams_accrodion_content">
                                                <p>
                                                Вам не придётся ходить к нотариусам, в налоговую службу и банки. С вас нужны только документы и согласие, все остальное за нами.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="b-teams">
                                    <div class="teams__item">
                                        <a href="#" class="teams__title js-teams-title opn">
                                            <span class="teams__text">Закрепим специалиста с опытом в вашей сфере  <div class="teams__spoiler js-faq-rotate"><span class="teams__symbol ">↓</span></div> </span>
                                           
                                        </a>
                                        <div class="teams__content js-teams-content" >
                                             <div class="teams_accrodion_content">
                                                <p>
                                                Работаем более 30 лет с ИП, ООО и НКО. Мы запускали самые разные предприятия: от языковых школ до мусороперерабатывающих заводов. Поэтому для работы с вашим бизнесом будет закреплён специалист с опытом именно в вашей сфере. В услуге все включено: от регистрации до выездных консультаций.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="b-teams">
                                    <div class="teams__item">
                                        <a href="#" class="teams__title js-teams-title opn">
                                            <span class="teams__text">Работаем в офисе или дистанционно  <div class="teams__spoiler js-faq-rotate"><span class="teams__symbol ">↓</span></div> </span>
                                          
                                        </a>
                                        <div class="teams__content js-teams-content" >
                                             <div class="teams_accrodion_content">
                                                <p>
                                                Вы можете встретиться с нами лично или, если вам удобно, консультироваться по телефону. При дистанционной работе вам нужно будет отправить документы и свою подпись в электронном виде. Если у вас нет электронной подписи, мы поможем её сделать.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
         [/section]
                <div id="teams" class="modal">
            <div class="modal-content">
            <span class="close">×</span>
                <div class="modal-body">
                    <div class="modal-header">
                        <h2>Наша команда</h2>
                        <p class="modal_sub_title">Ваш бизнес будет под контролем 5 разнопрофильных специалистов. Чтобы обезопасить вас от непредвиденных расходов и ошибок, работу команды проверяют директора департаментов. Каждый из них работает в своей сфере минимум 15 лет.</p>
                        <p>Познакомьтесь с ними:</p>
                    </div>
                    <div class="content_modal">
                        <div class="teams_modal_wrap">
                            <div class="item_teams_modal">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102.jpg">
                                <div class="info_member">
                                    <div class="member_profile">
                                    <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102.jpg">
                                   <p class="member_name">Климов Александр Владимирович</p>
                                   </div>
                                   <p class="member_desc">Генеральный директор Umbrella Group, опыт работы более 15 лет. Контролирует работу команды по открытию бизнеса, подключает профильных и отраслевых специалистов.</p>
                                </div>
                            </div>
                            <div class="item_teams_modal">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102-1.jpg">
                                <div class="info_member">
                                <div class="member_profile">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102-1.jpg">
                                   <p class="member_name">Батурина Ольга Викторовна</p>
                                </div>
                                   <p class="member_desc">Директор департамента корпоративного права и лицензий, опыт работы более 20 лет. Помогала открываться компаниям разных отраслей: от салонов красоты и интернет-магазинов до мусороперерабатывающих заводов.</p>
                                </div>
                            </div>
                            <div class="item_teams_modal">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102-2.jpg">
                                <div class="info_member">
                                <div class="member_profile">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102-2.jpg">
                                   <p class="member_name">Хохлова Наталья Александровна</p>
                                </div>
                                   <p class="member_desc">Директор департамента бухгалтерского учета и аудита, опыт работы более 20 лет. Помогает сократить налоговые отчисления.</p>
                                </div>
                            </div>
                            <div class="item_teams_modal">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102-3.jpg">
                                <div class="info_member">
                                <div class="member_profile">
                                <img src="https://taxlab.ru/wp-content/uploads/Rectangle-102-3.jpg">
                                   <p class="member_name">Демиденко Никита Алексеевич</p>
                                </div>
                                   <p class="member_desc">Директор департамента юридических услуг, опыт работы 10 лет. Консультирует по избежанию налоговых и других рисков при ведении бизнеса. Даёт рекомендации по построению группы компаний. Помогает составлять договоры и представляет интересы клиентов в суде.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        EOTHTML;
    umbrella_add_custom_css_files(['/assets/css/blocks/team_business.css']);
    umbrella_add_custom_js_files(['/assets/js/blocks/team_business.js']);
    return $html;
}

add_shortcode('team_business', 'team_business_shortcode');