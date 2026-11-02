<?php

class trouble_2022
{
    public $atts;

    public function generate_shortcode()
    {

        if (isMobile()) {

        $html = <<<EOHTML

        EOHTML;

         } else {
        // Кейсы-примеры: выездная проверка −63 млн и камеральная по УСН. Своих страниц
        // у кейсов нет (/case/… редиректит на главную) — ведём в список кейсов, на
        // вкладку «Юридические услуги» (хэш разбирает cases.js).
        $case_tax = $case_usn = '/about/cases/#Yuridicheskie_uslugi';
        $html = <<<EOHTML
         [section id='trouble_2022'  padding="0px"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                <div class="padding_trouble_2022">
                    <div class="title_trouble_2022">
                        <div class="title-h1-text-common">Помогаем бизнесу пройти через перемены</div>
                        <div class="main-text-common">Новые законы, проверки, изменения в&nbsp;компании&nbsp;&mdash; берём это на&nbsp;себя.</div>
                    </div>
                    <div class="content_trouble_2022">
                        <div class="padding-bottom_trouble_2022 mobile-content_trouble_2022">
                            <div id="spoiler-trouble-2022" style="display: none">
                                <div class="main-text-common">
                                    <span class="main-text-accent-common">Когда меняются правила.</span>
                                    Следим за&nbsp;изменениями в&nbsp;налоговом и&nbsp;корпоративном законодательстве и&nbsp;заранее подсказываем клиентам, что и&nbsp;к&nbsp;какому сроку нужно сделать.
                                    <br>
                                    <br>
                                    <span class="main-text-accent-common">Когда приходит проверка.</span>
                                    Сопровождаем налоговые проверки, готовим возражения и&nbsp;отстаиваем позицию клиента в&nbsp;налоговой и&nbsp;в&nbsp;суде.
                                    <br>
                                    <br>
                                    <span class="main-text-accent-common">Когда меняется бизнес.</span>
                                    Помогаем заключать договоры с&nbsp;защитой от&nbsp;рисков, реорганизовать компанию, получить лицензию или сменить систему налогообложения.
                                    <br>
                                    <br>
                                    В&nbsp;<a class="action-text-common" href="https://t.me/umbrella_club_nsk" target="_blank" rel="noopener">телеграм-канале</a> информируем о&nbsp;новых положениях, постановлениях и&nbsp;льготах, 
                                    а&nbsp;также анонсируем все полезные события и&nbsp;обучающие мероприятия.
                                </div>
                            </div>
                            <div class="action-text-common" 
                                 title="Click to Show/Hide Content"
                                 onclick="spoilerContentTrouble2022()">
                                <span id="spoiler-btn-show-trouble-2022">Узнайте, как</span>
                                <span id="spoiler-btn-hide-trouble-2022" style="display:none">Свернуть</span>
                            </div>
                        </div>
                        <div class="card-shadow-common card_trouble_2022">
                            <div class="border-card_trouble_2022">
                                <div class="position-card-legend_trouble_2022">
                                    <div class="legend_trouble_2022 mute-text-legend">Посмотрите примеры</div>
                                </div>
                                <div>
                                    <ul>
                                    <li>
                                        <span class="main-text-common">
                                            <a class="action-text-common" href="$case_tax">Уменьшили доначисления</a> по&nbsp;выездной налоговой проверке на&nbsp;63&nbsp;млн&nbsp;руб.
                                        </span>
                                    </li>
                                    <li>
                                        <span class="main-text-common">
                                            <a class="action-text-common" href="$case_usn">Добились отмены решения</a> налоговой по&nbsp;камеральной проверке декларации по&nbsp;УСН.
                                        </span>
                                    </li>
                                </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card_trouble_2022 content-desktop_trouble-2022">
                            <div class="main-text-common">
                                <span class="main-text-accent-common">Когда меняются правила.</span>
                                Следим за&nbsp;изменениями в&nbsp;налоговом и&nbsp;корпоративном законодательстве и&nbsp;заранее подсказываем клиентам, что и&nbsp;к&nbsp;какому сроку нужно сделать.
                                <br>
                                <br>
                                <span class="main-text-accent-common">Когда приходит проверка.</span>
                                Сопровождаем налоговые проверки, готовим возражения и&nbsp;отстаиваем позицию клиента в&nbsp;налоговой и&nbsp;в&nbsp;суде.
                                <br>
                                <br>
                                <span class="main-text-accent-common">Когда меняется бизнес.</span>
                                Помогаем <a class="action-text-common" href="/services/services-le/sostavlenie-dogovorov/">заключать договоры</a> с&nbsp;защитой от&nbsp;рисков, реорганизовать компанию, получить лицензию или сменить систему налогообложения.
                                <br>
                                <br>
                                В&nbsp;<a class="action-text-common" href="https://t.me/umbrella_club_nsk" target="_blank" rel="noopener">телеграм-канале</a> информируем о&nbsp;новых положениях, постановлениях и&nbsp;льготах, 
                                а&nbsp;также анонсируем все полезные события и&nbsp;обучающие мероприятия.
                            </div>
                        </div>
                 </div>
                </div>
            [/col]
           [/row]
         [/section]
        EOHTML; }
        ug_enqueue_style('assets/css/blocks/trouble_2022.css');
        ug_enqueue_script('assets/js/blocks/trouble_2022.js', ['jquery'], ['in_footer' => true, 'strategy' => 'defer']);
        return $html;
    }

}

function trouble_2022_shortcode($atts)
{
    $shortcode = new trouble_2022();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('trouble_2022', 'trouble_2022_shortcode');

?>