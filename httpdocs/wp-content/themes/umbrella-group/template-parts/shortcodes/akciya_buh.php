<?php
/**
 * Блоки страницы акции «−50% на первый месяц бухгалтерского обслуживания»
 * (/akcii/buhgalterskoe-obsluzhivanie-skidka-50/), до 31.12.2026.
 *
 * Устроено как akciya_tz и akciya_pb: каждый блок — отдельный шорткод,
 * тексты живут здесь. Между блоками в контенте записи стоят готовые блоки
 * темы: [cases branch="Бухгалтерия" ids="…"] — «Кейсы Umbrella Group».
 *
 * Факты в текстах — только с сайта: 35+ лет и 55+ специалистов —
 * [clients_hero]/[about_hero]; «команда от 3-х специалистов», «ведущий
 * бухгалтер с опытом более 10 лет», бесплатная консультация — первый экран
 * /services/bukhgalterskie-uslugi/obsluzhivanie/ (page 50), там же «Гарантия
 * защиты от ошибок» и снижение налоговых рисков; 1-е место среди
 * бухгалтерских компаний Новосибирска — лавры «Почему работать с нами»
 * (рейтинг «Планка», 2023).
 *
 * Кнопки «Получить скидку» открывают ту же всплывающую форму, что кнопки
 * первого экрана главной (#main-banner-contact-form-lightbox, CF7 13619);
 * лайтбокс выводит [akciya_buh_hero]. Заявка внизу — форма акции из боковой
 * колонки (CF7 9878, umbrella_akcii_contact_form).
 */
class akciya_buh
{
    private const CSS = 'assets/css/blocks/akciya-buh.css';
    private const IMAGE = '/wp-content/themes/umbrella-group/assets/img/promo-50-banner';

    /** Кнопка-ссылка на всплывающую форму. */
    private static function form_link(string $class, string $text): string
    {
        $arrow = '<svg width="22" height="10" viewBox="0 0 22 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h19M16 1l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        return '<a class="' . $class . '" href="#main-banner-contact-form-lightbox">' . $text . ' ' . $arrow . '</a>';
    }

    /** Иконки карточек — инлайном, как у других акций. */
    private static function icons(): array
    {
        $svg = static fn(string $path): string => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $path . '</svg>';
        return [
            'percent' => $svg('<path d="M18.5 5.5 5.5 18.5"/><circle cx="7" cy="7" r="2.5"/><circle cx="17" cy="17" r="2.5"/>'),
            'calendar' => $svg('<rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/><path d="m9.2 15 1.9 1.9 3.8-3.8"/>'),
            'user' => $svg('<circle cx="10" cy="8" r="3.8"/><path d="M3.5 20c.6-3.6 3.2-5.8 6.5-5.8 1.4 0 2.7.4 3.7 1.1"/><path d="M18 14.5v6M15 17.5h6"/>'),
            'book' => $svg('<path d="M4.5 5.5A2 2 0 0 1 6.5 3.5H19v14H6.5a2 2 0 0 0-2 2V5.5Z"/><path d="M4.5 19.5a2 2 0 0 0 2 2H19v-4"/><path d="M8.5 8h7M8.5 11.5h5"/>'),
            'report' => $svg('<path d="M14 3.5H7A1.5 1.5 0 0 0 5.5 5v14A1.5 1.5 0 0 0 7 20.5h10a1.5 1.5 0 0 0 1.5-1.5V8L14 3.5Z"/><path d="M13.8 3.8V8.2h4.4"/><path d="m9 14.5 2 2 4-4"/>'),
            'calc' => $svg('<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 7h8"/><path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01M8.5 15h.01M12 15h.01M15.5 15h.01M8.5 18h.01M12 18h.01M15.5 18h.01" stroke-width="2.2"/>'),
            'shield' => $svg('<path d="M12 3.2 4.8 6v5.6c0 4.2 2.9 7.6 7.2 9.1 4.3-1.5 7.2-4.9 7.2-9.1V6L12 3.2Z"/><path d="M12 8v4.5"/><path d="M12 15.6h.01" stroke-width="2.2"/>'),
            'people' => $svg('<circle cx="9" cy="8" r="3.2"/><path d="M3 19.5c.5-3.2 2.9-5.2 6-5.2s5.5 2 6 5.2"/><path d="M15.5 4.9a3.2 3.2 0 0 1 0 6.2M17.5 14.6c1.9.6 3.2 2.3 3.5 4.9"/>'),
            'chat' => $svg('<path d="M20.5 12a8 8 0 0 1-11.7 7.1L3.5 20.5l1.4-5.1A8 8 0 1 1 20.5 12Z"/><path d="M8.5 10.5h7M8.5 13.5h4.5"/>'),
            'team' => $svg('<path d="M12 3.2 4.8 6v5.6c0 4.2 2.9 7.6 7.2 9.1 4.3-1.5 7.2-4.9 7.2-9.1V6L12 3.2Z"/><path d="m8.8 12 2.2 2.2 4.3-4.4"/>'),
        ];
    }

    /**
     * Первый экран: бейдж со сроком, h1, лид, кнопки, иллюстрация акции
     * (та же, что на слайде главной) и полоса фактов о компании. Заголовок —
     * h1 страницы: шаблон акции его не печатает (umbrella_akciya_has_own_hero).
     */
    public function render_hero(): string
    {
        ug_enqueue_style(self::CSS);

        $image = ug_eager_img(
            '<picture class="akciya-buh-hero__picture">'
            . '<source type="image/webp" srcset="' . esc_url(home_url(self::IMAGE . '.webp')) . '">'
            . '<img src="' . esc_url(home_url(self::IMAGE . '.png')) . '" width="925" height="630" fetchpriority="high" decoding="async"'
            . ' alt="Бухгалтерская отчётность, папка «Бухгалтерия» и калькулятор — скидка 50% на первый месяц бухгалтерского обслуживания">'
            . '</picture>'
        );
        $button = self::form_link('akciya-buh-hero__button', 'Получить скидку 50%');

        $facts = '';
        foreach ([
            ['35+', 'лет работаем<br>с&nbsp;бизнесом'],
            ['55+', 'специалистов<br>в&nbsp;штате'],
            ['от&nbsp;3-х', 'специалистов —<br>ваша команда'],
            ['1-е', 'место среди бухгалтерских<br>компаний Новосибирска<sup>*</sup>'],
        ] as [$num, $label]) {
            $facts .= '<li class="akciya-buh-hero__fact"><b>' . $num . '</b><span>' . $label . '</span></li>';
        }

        // Лайтбокс рендерим сразу: возвращённые шорткоды второй раз не разбираются.
        $lightbox = do_shortcode('[lightbox id="main-banner-contact-form-lightbox" width="400px" padding="0px"][contact-form-7 id="13619" title="Форма на главной (узнать стоимость услуг)"][/lightbox]');

        return <<<EOT
            <section class="akciya-buh-hero">
                <div class="akciya-buh-hero__main">
                    <div class="akciya-buh-hero__text">
                        <div class="akciya-buh-hero__badge">Акция до&nbsp;31.12.2026</div>
                        <h1 class="akciya-buh-hero__title">Бухгалтерское обслуживание<span class="akciya-buh-hero__title-accent">&minus;50% на&nbsp;первый месяц</span></h1>
                        <p class="akciya-buh-hero__lead">Передайте учёт экспертам Umbrella&nbsp;Group и&nbsp;начните сотрудничество на&nbsp;выгодных условиях: первый месяц бухгалтерского обслуживания&nbsp;— за&nbsp;полцены.</p>
                        <div class="akciya-buh-hero__actions">
                            $button
                            <a class="akciya-buh-hero__more" href="#usloviya-akcii">Условия акции</a>
                        </div>
                    </div>
                    <div class="akciya-buh-hero__image">$image</div>
                </div>
                <ul class="akciya-buh-hero__facts">$facts</ul>
                <p class="akciya-buh-hero__footnote">* По&nbsp;итогам рейтинга «Планка», 2023.</p>
                $lightbox
            </section>
            EOT;
    }

    /** Условия акции — три карточки и сноска о стоимости. */
    public function render_conditions(): string
    {
        ug_enqueue_style(self::CSS);

        // Якорь для «Условия акции» в первом экране: id секции шаблон не принимает
        return '<span id="usloviya-akcii" class="akciya-buh-anchor"></span>' . ug_render('akciya-section', [
            'block'  => 'akciya-buh-conditions',
            'kicker' => 'Условия акции',
            'title'  => 'Как получить скидку 50%',
            'lead'   => 'Три простых условия&nbsp;— и&nbsp;первый месяц бухгалтерского обслуживания обойдётся вам вдвое дешевле.',
            'layout' => 'cards',
            'icons'  => self::icons(),
            'items'  => [
                [
                    'icon' => 'percent',
                    'title' => '&minus;50% на&nbsp;первый месяц',
                    'text' => 'Скидка на&nbsp;стоимость первого месяца бухгалтерского обслуживания по&nbsp;договору.',
                ],
                [
                    'icon' => 'calendar',
                    'title' => 'Договор до&nbsp;31.12.2026',
                    'text' => 'Скидка действует при заключении договора до&nbsp;31&nbsp;декабря 2026&nbsp;года.',
                ],
                [
                    'icon' => 'user',
                    'title' => 'Для новых клиентов',
                    'text' => 'Для компаний и&nbsp;ИП, которые ранее не&nbsp;стояли на&nbsp;бухгалтерском обслуживании в&nbsp;ГК&nbsp;Umbrella&nbsp;Group.',
                ],
            ],
            'after'  => '<p class="akciya-buh-conditions__note">Стоимость обслуживания зависит от&nbsp;системы налогообложения, количества операций и&nbsp;сотрудников&nbsp;— бухгалтер рассчитает её бесплатно на&nbsp;консультации.</p>',
        ]);
    }

    /** Что входит в бухгалтерское обслуживание. */
    public function render_included(): string
    {
        ug_enqueue_style(self::CSS);

        return ug_render('akciya-section', [
            'block'  => 'akciya-buh-included',
            'kicker' => 'Что вы получаете',
            'title'  => 'Бухгалтерия под ключ',
            'lead'   => 'Берём на&nbsp;себя учёт целиком или отдельные участки&nbsp;— в&nbsp;зависимости от&nbsp;задач вашего бизнеса.',
            'layout' => 'cards',
            'icons'  => self::icons(),
            'items'  => [
                [
                    'icon' => 'book',
                    'title' => 'Бухгалтерский и&nbsp;налоговый учёт',
                    'text' => 'Ведём учёт от&nbsp;первичных документов до&nbsp;отчётности в&nbsp;1С.',
                ],
                [
                    'icon' => 'report',
                    'title' => 'Отчётность в&nbsp;срок',
                    'text' => 'Готовим и&nbsp;сдаём бухгалтерскую и&nbsp;налоговую отчётность, следим за&nbsp;сроками.',
                ],
                [
                    'icon' => 'calc',
                    'title' => 'Расчёт налогов',
                    'text' => 'Считаем налоги и&nbsp;взносы с&nbsp;учётом актуальных требований и&nbsp;проверяем корректность расчётов.',
                ],
                [
                    'icon' => 'shield',
                    'title' => 'Без ошибок и&nbsp;штрафов',
                    'text' => 'Гарантия защиты от&nbsp;ошибок: ответственность за&nbsp;учёт несёт аутсорсер, а&nbsp;наши эксперты помогают снизить налоговые риски.',
                ],
                [
                    'icon' => 'chat',
                    'title' => 'Консультации бухгалтера',
                    'text' => 'На&nbsp;вопросы по&nbsp;учёту и&nbsp;налогам отвечает ведущий бухгалтер с&nbsp;опытом работы более 10&nbsp;лет.',
                ],
                [
                    'icon' => 'team',
                    'title' => 'Персональная команда',
                    'text' => 'Вашим учётом занимаются от&nbsp;3-х специалистов&nbsp;— работа не&nbsp;встанет из-за отпуска или болезни одного бухгалтера.',
                ],
            ],
        ]);
    }

    /** Кому выгодна акция: сегменты, у каждого — своё предложение и ссылка. */
    public function render_audience(): string
    {
        ug_enqueue_style(self::CSS);

        $arrow = '<svg width="18" height="10" viewBox="0 0 18 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h15M12 1l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $segments = [
            [
                'title' => 'ИП на&nbsp;УСН и&nbsp;патенте',
                'text' => 'Возьмём на&nbsp;себя учёт, расчёт налогов и&nbsp;взносов, отчётность и&nbsp;сроки. Вы&nbsp;занимаетесь делом, а&nbsp;не&nbsp;декларациями.',
                'link' => ['/services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-ip/', 'Учёт для ИП'],
            ],
            [
                'title' => 'Малый бизнес&nbsp;— ООО',
                'text' => 'Без штатного бухгалтера: считаем зарплату, сдаём отчётность. Первый месяц&nbsp;— за&nbsp;полцены.',
                'link' => ['/services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-ooo/', 'Учёт для ООО'],
            ],
            [
                'title' => 'Компании на&nbsp;ОСНО и&nbsp;с&nbsp;НДС',
                'text' => 'Больше операций&nbsp;— выше цена ошибки. Ведём налоговый учёт и&nbsp;следим, чтобы расчёты были корректными, а&nbsp;отчётность уходила в&nbsp;срок.',
            ],
            [
                'title' => 'Новые компании после регистрации',
                'text' => 'Поможем выбрать систему налогообложения и&nbsp;поставим учёт с&nbsp;нуля, чтобы с&nbsp;первого месяца не&nbsp;копились ошибки.',
            ],
            [
                'title' => 'Уходите от&nbsp;штатного бухгалтера или другой фирмы',
                'text' => 'Примем дела и&nbsp;оценим состояние учёта, а&nbsp;если его нужно восстановить&nbsp;— подскажем как. Без расходов на&nbsp;содержание и&nbsp;обучение штатной бухгалтерии и&nbsp;без трудовых споров.',
                'link' => ['/services/bukhgalterskie-uslugi/vosstanovleniya-ucheta/', 'Восстановление учёта'],
            ],
        ];

        $items = '';
        foreach ($segments as $i => $s) {
            $num = sprintf('%02d', $i + 1);
            $link = isset($s['link'])
                ? '<a class="akciya-buh-audience__link" href="' . esc_url($s['link'][0]) . '">' . $s['link'][1] . ' ' . $arrow . '</a>'
                : '';
            $items .= <<<EOT
                <div class="akciya-buh-audience__item">
                    <div class="akciya-buh-audience__number">$num</div>
                    <p class="akciya-buh-audience__item-title">{$s['title']}</p>
                    <div class="akciya-buh-audience__body">
                        <p class="akciya-buh-audience__item-text">{$s['text']}</p>
                        $link
                    </div>
                </div>
                EOT;
        }

        return <<<EOT
            <section class="akciya-buh-audience">
                <p class="akciya-buh-audience__kicker">Для кого</p>
                <h2 class="akciya-buh-audience__title">Кому особенно выгодна акция</h2>
                <div class="akciya-buh-audience__items">$items</div>
            </section>
            EOT;
    }

    /** Как подключиться: четыре шага и красная полоса со скидкой. */
    public function render_steps(): string
    {
        ug_enqueue_style(self::CSS);

        $arrow = '<svg width="18" height="10" viewBox="0 0 18 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h15M12 1l4 4-4 4" stroke="#ec1c23" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $percent = '<svg width="36" height="36" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 8 8 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="9.5" cy="9.5" r="2.8" stroke="currentColor" stroke-width="1.8"/><circle cx="20.5" cy="20.5" r="2.8" stroke="currentColor" stroke-width="1.8"/></svg>';

        $steps = ug_render('akciya-section', [
            'block'  => 'akciya-buh-steps',
            'kicker' => 'Как это работает',
            'title'  => 'Как подключиться по&nbsp;акции',
            'lead'   => 'От&nbsp;заявки до&nbsp;первого месяца обслуживания со&nbsp;скидкой&nbsp;— четыре шага.',
            'head'   => 'head-row',
            'layout' => 'steps',
            'arrow'  => $arrow,
            'items'  => [
                [
                    'title' => 'Оставьте заявку',
                    'text' => 'Позвоните или оставьте номер на&nbsp;сайте&nbsp;— перезвоним в&nbsp;течение 20&nbsp;минут в&nbsp;рабочее время.',
                ],
                [
                    'title' => 'Бесплатная консультация',
                    'text' => 'Бухгалтер уточнит задачи, систему налогообложения и&nbsp;объём операций и&nbsp;рассчитает стоимость.',
                ],
                [
                    'title' => 'Договор со&nbsp;скидкой',
                    'text' => 'Заключаете договор до&nbsp;31.12.2026&nbsp;— первый месяц обслуживания со&nbsp;скидкой 50%.',
                ],
                [
                    'title' => 'Ведём ваш учёт',
                    'text' => 'Принимаем документы и&nbsp;берём бухгалтерию на&nbsp;себя&nbsp;— вы&nbsp;занимаетесь бизнесом.',
                ],
            ],
        ]);
        $button = self::form_link('akciya-buh-saving__button', 'Оставить заявку');

        return $steps . <<<EOT
            <div class="akciya-buh-saving">
                <div class="akciya-buh-saving__icon">$percent</div>
                <div class="akciya-buh-saving__text">
                    <p class="akciya-buh-saving__title"><span class="akciya-buh-saving__sum">&minus;50%</span> на&nbsp;первый месяц</p>
                    <p class="akciya-buh-saving__caption">при заключении договора до&nbsp;<b>31&nbsp;декабря</b> 2026&nbsp;года</p>
                </div>
                $button
            </div>
            EOT;
    }

    /**
     * Отзывы клиентов — карточки и окна отзыва со страницы «Клиенты»
     * ([ug_client_tiles], сниппет 1031): ids="…" — клиенты по порядку.
     */
    public function render_reviews($atts): string
    {
        $atts = shortcode_atts(['ids' => ''], $atts, 'akciya_buh_reviews');
        $ids = array_values(array_filter(array_map('intval', explode(',', (string) $atts['ids']))));
        if (!$ids || !function_exists('ug_client_tiles_card')) {
            return '';
        }

        $cards = '';
        $dialogs = '';
        foreach ($ids as $id) {
            $post = get_post($id);
            if (!$post || $post->post_type !== 'client' || $post->post_status !== 'publish') {
                continue;
            }
            [$card, $dialog] = ug_client_tiles_card($post, [], ug_client_tiles_services($post));
            $cards .= $card;
            $dialogs .= $dialog;
        }
        if ($cards === '') {
            return '';
        }

        ug_enqueue_style(self::CSS);
        ug_enqueue_style('assets/css/blocks/client_tiles.css');
        ug_enqueue_script('assets/js/blocks/cases_tabs.js');
        ug_enqueue_script('assets/js/blocks/client_tiles.js', [ug_style_handle('assets/js/blocks/cases_tabs.js')]);

        $url = esc_url(home_url('/about/clients/'));
        $arrow = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $zoom = $dialogs !== '' ? ug_client_review_zoom() : '';

        return <<<EOT
            <section class="akciya-buh-reviews">
                <div class="akciya-buh-reviews__head">
                    <div>
                        <h2 class="akciya-buh-reviews__title">Отзывы клиентов</h2>
                        <p class="akciya-buh-reviews__lead">Компании, которые доверили нам бухгалтерию. Нажмите на&nbsp;карточку, чтобы прочитать отзыв и&nbsp;благодарственное письмо.</p>
                    </div>
                    <a class="akciya-buh-reviews__all" href="$url">Все клиенты$arrow</a>
                </div>
                <div class="client-tiles__grid akciya-buh-reviews__grid">$cards</div>
                $dialogs
                $zoom
            </section>
            EOT;
    }

    /** Частые вопросы об акции — <details>, без скриптов. */
    public function render_faq(): string
    {
        ug_enqueue_style(self::CSS);

        $faq = [
            ['Кто может воспользоваться акцией?', 'Компании и&nbsp;ИП, которые ранее не&nbsp;стояли на&nbsp;бухгалтерском обслуживании в&nbsp;ГК&nbsp;Umbrella&nbsp;Group.'],
            ['До какого срока действует предложение?', 'Скидка действует при заключении договора до&nbsp;31&nbsp;декабря 2026&nbsp;года.'],
            ['На что распространяется скидка 50%?', 'На&nbsp;стоимость первого месяца бухгалтерского обслуживания по&nbsp;договору.'],
            ['Сколько стоит обслуживание?', 'Стоимость зависит от&nbsp;системы налогообложения, количества операций и&nbsp;сотрудников. Бухгалтер бесплатно проконсультирует и&nbsp;рассчитает её под ваш бизнес.'],
            ['Учёт запущен&nbsp;— возьмёте?', 'Да. Сначала оценим состояние учёта, а&nbsp;если его нужно восстановить, расскажем, как это сделать: <a href="/services/bukhgalterskie-uslugi/vosstanovleniya-ucheta/">восстановление учёта</a>&nbsp;— отдельная услуга.'],
        ];

        $items = '';
        foreach ($faq as $i => [$q, $a]) {
            $open = $i === 0 ? ' open' : '';
            $items .= '<details class="akciya-buh-faq__item"' . $open . '><summary class="akciya-buh-faq__question">' . $q . '</summary>'
                . '<p class="akciya-buh-faq__answer">' . $a . '</p></details>';
        }

        return <<<EOT
            <section class="akciya-buh-faq">
                <p class="akciya-buh-faq__kicker">Вопросы и&nbsp;ответы</p>
                <h2 class="akciya-buh-faq__title">Частые вопросы об&nbsp;акции</h2>
                <div class="akciya-buh-faq__items">$items</div>
            </section>
            EOT;
    }

    /**
     * Финальный баннер — как «Нужен сильный результат…» на /about/cases/
     * (тёмный, картинка справа растворяется в фоне), но с формой акции
     * прямо в баннере: CF7 9878, тексты — из полей записи.
     */
    public function render_cta(): string
    {
        ug_enqueue_style(self::CSS);

        $form = umbrella_akcii_contact_form();
        $image = esc_url(home_url('/wp-content/themes/umbrella-group/assets/img/cases-cta-office.jpg'));

        return <<<EOT
            <section class="akciya-buh-cta">
                <div class="akciya-buh-cta__image"><img src="$image" alt="" loading="lazy" decoding="async"></div>
                <div class="akciya-buh-cta__text">
                    <p class="akciya-buh-cta__eyebrow">Разберём ваш учёт</p>
                    <h2 class="akciya-buh-cta__title">Нужен порядок в&nbsp;бухгалтерии? Начните со&nbsp;скидкой 50%</h2>
                    <p class="akciya-buh-cta__desc">Оставьте номер&nbsp;— бухгалтер свяжется с&nbsp;вами, ответит на&nbsp;вопросы и&nbsp;рассчитает стоимость обслуживания. Акция действует до&nbsp;31.12.2026.</p>
                </div>
                <div class="akciya-buh-cta__form">$form</div>
            </section>
            EOT;
    }
}

// [akciya_buh_hero], [akciya_buh_conditions], [akciya_buh_included], [akciya_buh_audience],
// [akciya_buh_steps], [akciya_buh_reviews ids="…"], [akciya_buh_faq], [akciya_buh_cta]
foreach (['hero', 'conditions', 'included', 'audience', 'steps', 'reviews', 'faq', 'cta'] as $akciya_buh_block) {
    add_shortcode('akciya_buh_' . $akciya_buh_block, static fn($atts) => (new akciya_buh())->{'render_' . $akciya_buh_block}($atts));
}
unset($akciya_buh_block);
