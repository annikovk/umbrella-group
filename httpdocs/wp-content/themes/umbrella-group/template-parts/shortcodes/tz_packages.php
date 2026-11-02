<?php
/**
 * Блоки страницы «Регистрация товарного знака» (/services/licensing/registracija-tovarnogo-znaka/):
 *
 *   [tz_packages] — бесплатная проверка как вход + выбор пакета: переключатель
 *                   «с отчётом о проверке по платным базам / без отчёта», две карточки и сводка 2 × 2;
 *   [tz_check]    — что даёт отчёт о проверке по платным базам;
 *   [tz_steps]    — как проходит регистрация и какие шаги в каком пакете.
 *
 * Цены и состав пакетов — из документа отдела продаж (октябрь 2026), правятся
 * в UG_TZ_PACKAGES. Кнопки открывают попапы, которые уже стоят на странице:
 * #calculate-price-contact-form-lightbox (CF7 9965, его выводит [su_welcome_screen
 * button='price_calc']) и #ask-question-contact-form-lightbox (CF7 9967, лайтбокс
 * в конце контента). Название пакета подписывается в попапе «Пакет: …» — как
 * в buhuslugi-packs-block.js; в письмо оно не уходит (в форме только телефон).
 *
 * Без JS видны все четыре карточки; с JS — две, по переключателю.
 */

defined('ABSPATH') || exit;

const UG_TZ_FORM = '#calculate-price-contact-form-lightbox';
const UG_TZ_ASK  = '#ask-question-contact-form-lightbox';

/** Пункты состава пакетов. */
const UG_TZ_ITEMS = [
    'search'   => 'Комплексный поиск по&nbsp;закрытым базам Роспатента среди зарегистрированных знаков и&nbsp;поданных заявок',
    'report'   => 'Отчёт по&nbsp;итогам проверки с&nbsp;тактиками и&nbsp;стратегиями ведения делопроизводства',
    'filing'   => 'Формирование и&nbsp;подача заявки',
    'classes'  => 'Подбор классов МКТУ',
    'priority' => 'Получение приоритета',
    'feesinfo' => 'Направление информации по&nbsp;оплате пошлин',
    'feesctl'  => 'Контроль оплаты пошлин',
    'answers'  => 'Ответы на&nbsp;запросы и&nbsp;уведомления Роспатента',
    'control'  => 'Контроль делопроизводства',
    'cert'     => 'Получение решения о&nbsp;регистрации и&nbsp;свидетельства на&nbsp;товарный знак',
];

/**
 * Четыре варианта: check — с предоставлением отчёта о проверке по платным базам
 * (проверка проводится во всех вариантах), full — сопровождение до свидетельства.
 * base — «всё из пакета …» (подпись), items — ключи UG_TZ_ITEMS, extra — строки «отдельно».
 */
const UG_TZ_PACKAGES = [
    [
        'check' => true, 'full' => false,
        'kicker' => 'Заявка в&nbsp;Роспатент',
        'name'   => 'Подача заявки',
        'sub'    => 'с&nbsp;предоставлением отчёта о&nbsp;проверке по&nbsp;платным базам',
        'plain'  => 'Подача заявки с предоставлением отчёта о проверке по платным базам',
        'price'  => '17&nbsp;000',
        'lead'   => 'Проверим обозначение по&nbsp;закрытым базам Роспатента, передадим вам отчёт, подадим заявку и&nbsp;получим приоритет на&nbsp;ваш товарный знак.',
        'items'  => ['search', 'report', 'filing', 'classes', 'priority', 'feesinfo'],
        'extra'  => [
            ['Подготовка ответов на&nbsp;уведомления, сопровождение получения писем-согласий', 'от&nbsp;20&nbsp;000&nbsp;₽'],
            ['Доплата за&nbsp;ведение делопроизводства до&nbsp;свидетельства', '35&nbsp;000&nbsp;₽'],
        ],
    ],
    [
        'check' => true, 'full' => true, 'best' => true,
        'kicker' => 'До&nbsp;получения свидетельства',
        'name'   => 'Регистрация под&nbsp;ключ',
        'sub'    => 'с&nbsp;предоставлением отчёта о&nbsp;проверке по&nbsp;платным базам',
        'plain'  => 'Регистрация под ключ с предоставлением отчёта о проверке по платным базам',
        'price'  => '52&nbsp;000',
        'lead'   => 'Берём на&nbsp;себя весь путь: проверку обозначения и&nbsp;отчёт, заявку, ответы Роспатенту и&nbsp;контроль делопроизводства&nbsp;— до&nbsp;свидетельства на&nbsp;ваш товарный знак.',
        'base'   => 'Всё, что входит в&nbsp;подачу заявки с&nbsp;отчётом о&nbsp;проверке',
        'items'  => ['search', 'report', 'filing', 'classes', 'priority', 'feesinfo', 'feesctl', 'answers', 'control', 'cert'],
        'plus'   => ['feesctl', 'answers', 'control', 'cert'],
    ],
    [
        'check' => false, 'full' => false,
        'kicker' => 'Заявка в&nbsp;Роспатент',
        'name'   => 'Подача заявки',
        'sub'    => 'без предоставления отчёта о&nbsp;проверке по&nbsp;платным базам',
        'plain'  => 'Подача заявки без предоставления отчёта о проверке по платным базам',
        'price'  => '10&nbsp;000',
        'lead'   => 'Сформируем и&nbsp;подадим заявку на&nbsp;ваше обозначение, подберём классы МКТУ и&nbsp;получим приоритет.',
        'items'  => ['filing', 'classes', 'priority', 'feesinfo'],
    ],
    [
        'check' => false, 'full' => true,
        'kicker' => 'До&nbsp;получения свидетельства',
        'name'   => 'Регистрация под&nbsp;ключ',
        'sub'    => 'без предоставления отчёта о&nbsp;проверке по&nbsp;платным базам',
        'plain'  => 'Регистрация под ключ без предоставления отчёта о проверке по платным базам',
        'price'  => '45&nbsp;000',
        'lead'   => 'Подадим заявку и&nbsp;проведём её&nbsp;через экспертизу Роспатента до&nbsp;свидетельства на&nbsp;ваш товарный знак.',
        'base'   => 'Всё, что входит в&nbsp;подачу заявки без&nbsp;отчёта о&nbsp;проверке',
        'items'  => ['filing', 'classes', 'priority', 'feesinfo', 'feesctl', 'answers', 'control', 'cert'],
        'plus'   => ['feesctl', 'answers', 'control', 'cert'],
    ],
];

/** Иконки — содержимое svg 24 × 24, обводка (как в about_hero). */
const UG_TZ_ICONS = [
    'check'  => '<path d="m5 12.5 4.5 4.5L19 7.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'plus'   => '<path d="M12 5v14M5 12h14" stroke-linecap="round"/>',
    'search' => '<circle cx="10.5" cy="10.5" r="6"/><path d="m15 15 5 5" stroke-linecap="round"/>',
    'doc'    => '<path d="M7 3.75h7l4 4v12.5H7z" stroke-linejoin="round"/><path d="M14 3.75v4h4M9.75 12h5.5M9.75 15.5h5.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'send'   => '<path d="M4 11.5 20 4l-6.5 16-2.5-6.5z" stroke-linejoin="round"/><path d="m11 13.5 3.5-3.5" stroke-linecap="round"/>',
    'coin'   => '<circle cx="12" cy="12" r="8.25"/><path d="M9.5 9.25h3.75a1.9 1.9 0 0 1 0 3.75H9.5V8M9.5 13v3.5M8.25 15h4" stroke-linecap="round" stroke-linejoin="round"/>',
    'scale'  => '<path d="M12 4v16M7.5 20h9M5 7.5h14M5 7.5 2.75 13a2.25 2.25 0 0 0 4.5 0zM19 7.5 16.75 13a2.25 2.25 0 0 0 4.5 0z" stroke-linecap="round" stroke-linejoin="round"/>',
    'award'  => '<circle cx="12" cy="9.5" r="5.25"/><path d="m8.75 13.75-1.5 6.5L12 18l4.75 2.25-1.5-6.5" stroke-linejoin="round"/>',
    'shield' => '<path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke-linejoin="round"/><path d="m9 12 2.2 2.2L15.5 10" stroke-linecap="round" stroke-linejoin="round"/>',
    'alert'  => '<circle cx="12" cy="12" r="8.25"/><path d="M12 7.75v5M12 16.1v.15" stroke-linecap="round"/>',
    'chat'   => '<path d="M4.75 6.25c0-.83.67-1.5 1.5-1.5h11.5c.83 0 1.5.67 1.5 1.5v8.5c0 .83-.67 1.5-1.5 1.5H10l-4.25 3.5v-3.5h-.5c-.83 0-1.5-.67-1.5-1.5z" stroke-linejoin="round"/><path d="M8.5 9.5h7M8.5 12.5h4.5" stroke-linecap="round"/>',
];

function ug_tz_icon(string $name, int $size = 24): string
{
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' . UG_TZ_ICONS[$name] . '</svg>';
}

function ug_tz_assets(): void
{
    ug_enqueue_style('assets/css/blocks/tz_packages.css');
    ug_enqueue_script('assets/js/blocks/tz_packages.js');
}

function ug_tz_package_card(array $p, int $i): string
{
    $mode  = $p['check'] ? 'check' : 'plain';
    $best  = !empty($p['best']);
    $items = '';
    if (!empty($p['base'])) {
        $items .= '<li class="tzp-card__item tzp-card__item--base">' . ug_tz_icon('check', 18) . '<span>' . $p['base'] . '</span></li>';
        $list   = $p['plus'];
    } else {
        $list = $p['items'];
    }
    foreach ($list as $key) {
        $is_check = in_array($key, ['search', 'report'], true);
        $is_plus  = !empty($p['base']);
        $items .= '<li class="tzp-card__item' . ($is_check ? ' tzp-card__item--accent' : '') . '">'
            . ug_tz_icon($is_plus ? 'plus' : 'check', 18) . '<span>' . UG_TZ_ITEMS[$key] . '</span></li>';
    }
    $extra = '';
    if (!empty($p['extra'])) {
        $rows = '';
        foreach ($p['extra'] as [$text, $price]) {
            $rows .= '<li><span>' . $text . '</span><b>' . $price . '</b></li>';
        }
        $extra = '<div class="tzp-card__extra"><p class="tzp-card__extra-title">Отдельно при необходимости</p><ul>' . $rows . '</ul></div>';
    }
    $badge = $best ? '<span class="tzp-card__badge">Рекомендуем</span>' : '';
    $mode_label = $p['check'] ? 'С&nbsp;отчётом о&nbsp;проверке' : 'Без отчёта о&nbsp;проверке';
    $plain = esc_attr($p['plain'] . ' — ' . html_entity_decode(str_replace('&nbsp;', ' ', $p['price'])) . ' ₽');

    return '<article class="tzp-card' . ($best ? ' tzp-card--best' : '') . ($p['full'] ? ' tzp-card--full' : '') . '" data-tz-mode="' . $mode . '" data-tz-pkg="' . $i . '">'
        . '<div class="tzp-card__top">'
        . '<p class="tzp-card__kicker">' . $p['kicker'] . $badge . '</p>'
        . '<p class="tzp-card__mode">' . $mode_label . '</p>'
        . '<h3 class="tzp-card__name">' . $p['name'] . (!empty($p['sub']) ? ' <span class="tzp-card__name-sub">' . $p['sub'] . '</span>' : '') . '</h3>'
        . '<p class="tzp-card__price"><b>' . $p['price'] . '&nbsp;₽</b></p>'
        . '<p class="tzp-card__lead">' . $p['lead'] . '</p>'
        . '</div>'
        . '<ul class="tzp-card__items">' . $items . '</ul>'
        . $extra
        . ($p['full'] ? '<div class="tzp-card__result">' . ug_tz_icon('award', 22) . '<p><b>Результат&nbsp;— свидетельство на&nbsp;товарный знак.</b> Исключительное право действует 10&nbsp;лет.</p></div>' : '')
        . '<div class="tzp-card__foot"><a href="' . UG_TZ_FORM . '" class="tzp-btn' . ($best ? '' : ' tzp-btn--ghost') . '" data-tz-package="' . $plain . '">Заказать</a></div>'
        . '</article>';
}

function ug_tz_packages_shortcode(): string
{
    ug_tz_assets();

    $cards = '';
    foreach (UG_TZ_PACKAGES as $i => $p) {
        $cards .= ug_tz_package_card($p, $i);
    }

    // Сводка 2 × 2: строки — проверка, столбцы — объём работ.
    $cell = static function (int $i): string {
        $p = UG_TZ_PACKAGES[$i];
        $mode = $p['check'] ? 'check' : 'plain';
        return '<button type="button" class="tzp-matrix__cell' . (!empty($p['best']) ? ' tzp-matrix__cell--best' : '') . '" data-tz-go="' . $mode . '" data-tz-pkg="' . $i . '">'
            . '<b>' . $p['price'] . '&nbsp;₽</b>' . (!empty($p['best']) ? '<span>рекомендуем</span>' : '') . '</button>';
    };
    $matrix = '<div class="tzp-matrix" role="table" aria-label="Стоимость услуг">'
        . '<div class="tzp-matrix__row tzp-matrix__row--head" role="row"><span role="columnheader"></span><span role="columnheader">Подача заявки</span><span role="columnheader">Регистрация под&nbsp;ключ</span></div>'
        . '<div class="tzp-matrix__row" role="row"><span class="tzp-matrix__label" role="rowheader">С&nbsp;отчётом о&nbsp;проверке</span>' . $cell(0) . $cell(1) . '</div>'
        . '<div class="tzp-matrix__row" role="row"><span class="tzp-matrix__label" role="rowheader">Без отчёта о&nbsp;проверке</span>' . $cell(2) . $cell(3) . '</div>'
        . '</div>';

    $ask = UG_TZ_ASK;
    $chat = ug_tz_icon('chat', 30);
    $info = ug_tz_icon('doc', 20);

    return <<<EOHTML
    <section class="tzp" id="tz-packages">
        <div class="tzp__inner">
            <div class="tzp__head">
                <h2 class="tzp__title">Стоимость регистрации товарного знака</h2>
                <p class="tzp__lead">Закрепите исключительное право на&nbsp;свой бренд. Подготовим и&nbsp;подадим заявку в&nbsp;Роспатент или проведём регистрацию под&nbsp;ключ&nbsp;— до&nbsp;свидетельства на&nbsp;товарный знак. Перед подачей проверим обозначение по&nbsp;закрытым базам Роспатента, а&nbsp;по&nbsp;итогам проверки можем подготовить для вас отчёт с&nbsp;тактикой и&nbsp;стратегией делопроизводства.</p>
            </div>

            <div class="tzp-free">
                <div class="tzp-free__icon">$chat</div>
                <div class="tzp-free__text">
                    <p class="tzp-free__kicker">Бесплатно</p>
                    <h3 class="tzp-free__title">Предварительная проверка на&nbsp;возможность регистрации</h3>
                    <p class="tzp-free__desc">Расскажите, какое обозначение хотите зарегистрировать. Специалист проверит, можно&nbsp;ли его зарегистрировать, и&nbsp;подскажет оптимальный вариант работы.</p>
                </div>
                <a href="$ask" class="tzp-btn tzp-free__btn">Проверить бесплатно</a>
            </div>

            <div class="tzp-choose">
                <p class="tzp-choose__step">Выберите вариант услуги</p>
                <div class="tzp-switch" role="radiogroup" aria-label="Отчёт о проверке по платным базам">
                    <button type="button" class="tzp-switch__opt is-active" role="radio" aria-checked="true" data-tz-go="check"><span class="tzp-switch__text"><span class="tzp-switch__main">С&nbsp;отчётом о&nbsp;проверке</span><span class="tzp-switch__sub">по&nbsp;платным базам Роспатента</span></span><em>рекомендуем</em></button>
                    <button type="button" class="tzp-switch__opt" role="radio" aria-checked="false" data-tz-go="plain"><span class="tzp-switch__text"><span class="tzp-switch__main">Без отчёта о&nbsp;проверке</span><span class="tzp-switch__sub">по&nbsp;платным базам Роспатента</span></span></button>
                </div>
                <p class="tzp-switch__note" data-tz-note="check">По&nbsp;итогам проверки по&nbsp;закрытым базам Роспатента вы&nbsp;получите отчёт с&nbsp;тактиками и&nbsp;стратегиями ведения делопроизводства&nbsp;— до&nbsp;подачи заявки и&nbsp;оплаты пошлин.</p>
                <p class="tzp-switch__note tzp-switch__note--info" data-tz-note="plain">$info<span>Проверку обозначения проводим в&nbsp;любом варианте. Без отчёта вы&nbsp;не&nbsp;получаете документ по&nbsp;её&nbsp;итогам с&nbsp;тактиками и&nbsp;стратегиями ведения делопроизводства. <a href="#tz-check">Что даёт отчёт&nbsp;→</a></span></p>
            </div>

            <div class="tzp__cards">$cards</div>

            <div class="tzp__bottom">
                <div class="tzp__matrix-wrap">
                    <p class="tzp__matrix-title">Стоимость услуг</p>
                    $matrix
                </div>
                <div class="tzp__fees">
                    <p><b>Госпошлины Роспатента</b> в&nbsp;стоимость услуг не&nbsp;входят и&nbsp;оплачиваются отдельно. Их&nbsp;размер зависит от&nbsp;числа классов МКТУ.</p>
                    <a href="#tz-fees" class="tzp-link">Сроки и&nbsp;размеры пошлин&nbsp;→</a>
                </div>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('tz_packages', 'ug_tz_packages_shortcode');

function ug_tz_check_shortcode(): string
{
    ug_tz_assets();

    // Что даёт отчёт: [иконка, заголовок, текст]
    $gains = [
        ['search', 'Картина до&nbsp;подачи', 'Итоги поиска по&nbsp;закрытым базам Роспатента среди зарегистрированных знаков и&nbsp;поданных заявок&nbsp;— в&nbsp;документе, который остаётся у&nbsp;вас.'],
        ['doc', 'Тактика и&nbsp;стратегия', 'В&nbsp;отчёте&nbsp;— тактики и&nbsp;стратегии ведения делопроизводства по&nbsp;вашему обозначению.'],
        ['coin', 'Решение до&nbsp;оплаты пошлин', 'Риски видны до&nbsp;подачи заявки: можно доработать обозначение, пока пошлины не&nbsp;уплачены.'],
    ];
    $list = '';
    foreach ($gains as [$icon, $title, $text]) {
        $list .= '<li class="tzc-gain"><span class="tzc-gain__icon">' . ug_tz_icon($icon, 24) . '</span>'
            . '<div><p class="tzc-gain__title">' . $title . '</p><p class="tzc-gain__text">' . $text . '</p></div></li>';
    }
    $alert = ug_tz_icon('alert', 26);
    $shield = ug_tz_icon('shield', 22);
    $form = UG_TZ_FORM;
    $best = esc_attr(UG_TZ_PACKAGES[1]['plain'] . ' — 52 000 ₽');

    return <<<EOHTML
    <section class="tzc" id="tz-check">
        <div class="tzc__inner">
            <div class="tzc__text">
                <h2 class="tzc__title">Что даёт отчёт о&nbsp;проверке по&nbsp;платным базам</h2>
                <p class="tzc__lead">Перед подачей заявки мы&nbsp;проверяем обозначение по&nbsp;закрытым базам Роспатента среди зарегистрированных знаков и&nbsp;поданных заявок. Отчёт по&nbsp;итогам этой проверки вы&nbsp;получаете в&nbsp;вариантах с&nbsp;отчётом.</p>
                <p class="tzc__desc">Роспатент откажет в&nbsp;регистрации, если эксперт признает обозначение повторяющим чужой товарный знак, сходным с&nbsp;ним или не&nbsp;соответствующим требованиям закона. Проверка по&nbsp;открытым источникам не&nbsp;всегда даёт полную и&nbsp;объективную информацию.</p>
                <div class="tzc__risk">
                    <span class="tzc__risk-icon">$alert</span>
                    <p><b>Уплаченные пошлины при отказе в&nbsp;регистрации не&nbsp;возвращаются.</b> Отчёт помогает оценить риски до&nbsp;подачи заявки и&nbsp;оплаты пошлин.</p>
                </div>
            </div>
            <div class="tzc__side">
                <ul class="tzc-gains">$list</ul>
                <div class="tzc-case">
                    <p class="tzc-case__kicker">$shield Из&nbsp;практики</p>
                    <p class="tzc-case__text">Клиент пришёл зарегистрировать словесный знак. Проверка на&nbsp;сходство и&nbsp;тождество показала, что он&nbsp;похож на&nbsp;уже зарегистрированные знаки,&nbsp;— это риск отказа. Мы&nbsp;видоизменили словесный элемент, подали заявку и&nbsp;через год получили свидетельство.</p>
                    <p class="tzc-case__who">ООО «ДИНАЛИТ», продажа автозапчастей</p>
                </div>
                <a href="$form" class="tzp-btn" data-tz-package="$best">Заказать регистрацию с&nbsp;отчётом</a>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('tz_check', 'ug_tz_check_shortcode');

function ug_tz_steps_shortcode(): string
{
    ug_tz_assets();

    // [иконка, заголовок, текст, срок, метки: all | check | full]
    $steps = [
        ['chat', 'Бесплатная проверка', 'Оцениваем, можно&nbsp;ли зарегистрировать ваше обозначение, и&nbsp;подсказываем оптимальный вариант работы.', '', ['free']],
        ['search', 'Проверка по&nbsp;платным базам', 'Ищем похожие знаки и&nbsp;заявки в&nbsp;закрытых базах Роспатента. В&nbsp;вариантах с&nbsp;отчётом передаём вам отчёт с&nbsp;тактиками и&nbsp;стратегиями ведения делопроизводства.', '', ['all', 'check']],
        ['send', 'Заявка и&nbsp;приоритет', 'Подбираем классы МКТУ, формируем и&nbsp;подаём заявку в&nbsp;Роспатент, получаем приоритет.', '', ['all']],
        ['coin', 'Пошлины', 'Присылаем информацию по&nbsp;оплате пошлин. При регистрации под&nbsp;ключ ещё и&nbsp;контролируем оплату.', '', ['all', 'full']],
        ['scale', 'Экспертиза', 'Формальная экспертиза и&nbsp;экспертиза по&nbsp;существу. Отвечаем на&nbsp;запросы и&nbsp;уведомления Роспатента, контролируем делопроизводство.', 'По&nbsp;существу&nbsp;— до&nbsp;12&nbsp;месяцев со&nbsp;дня принятия заявки к&nbsp;рассмотрению', ['full']],
        ['award', 'Свидетельство', 'Получаем решение о&nbsp;регистрации и&nbsp;свидетельство на&nbsp;товарный знак. Исключительное право действует 10&nbsp;лет.', '', ['full']],
    ];
    $tags = [
        'free'  => ['Бесплатно', 'tzs-tag--free'],
        'check' => ['С&nbsp;отчётом', 'tzs-tag--check'],
        'all'   => ['Во&nbsp;всех вариантах', 'tzs-tag--all'],
        'full'  => ['Под&nbsp;ключ', 'tzs-tag--full'],
    ];
    $html = '';
    foreach ($steps as $n => [$icon, $title, $text, $term, $marks]) {
        $chips = '';
        foreach ($marks as $m) {
            $chips .= '<span class="tzs-tag ' . $tags[$m][1] . '">' . $tags[$m][0] . '</span>';
        }
        $html .= '<li class="tzs-step">'
            . '<div class="tzs-step__head"><span class="tzs-step__icon">' . ug_tz_icon($icon, 26) . '</span><span class="tzs-step__num">' . sprintf('%02d', $n + 1) . '</span></div>'
            . '<h3 class="tzs-step__title">' . $title . '</h3>'
            . '<p class="tzs-step__text">' . $text . '</p>'
            . ($term !== '' ? '<p class="tzs-step__term">' . $term . '</p>' : '')
            . '<div class="tzs-step__tags">' . $chips . '</div>'
            . '</li>';
    }

    return <<<EOHTML
    <section class="tzs" id="tz-steps">
        <div class="tzs__inner">
            <div class="tzs__head">
                <h2 class="tzs__title">Как проходит регистрация</h2>
                <p class="tzs__lead">Рассмотрение заявки в&nbsp;Роспатенте занимает от&nbsp;6&nbsp;месяцев. Мы&nbsp;ведём заявку на&nbsp;каждом этапе и&nbsp;держим вас в&nbsp;курсе.</p>
            </div>
            <ol class="tzs__list">$html</ol>
        </div>
    </section>
    EOHTML;
}

add_shortcode('tz_steps', 'ug_tz_steps_shortcode');

/**
 * Выбранный вариант услуги — в письмо менеджеру по форме 9965 («Расчёт стоимости»).
 * Кнопка «Заказать» пишет «<вариант> — <цена>» в скрытое поле [hidden tz-package]
 * формы (tz_packages.js), в теле письма стоит спецтег [_tz_package]: строка
 * «Вариант услуги: …» — только когда поле заполнено, на остальных 30 страницах
 * с этой формой тег даёт пустую строку. Flamingo сохраняет поле tz-package сам.
 */
add_filter('wpcf7_special_mail_tags', static function ($output, $name, $html) {
    if ($name !== '_tz_package' || !class_exists('WPCF7_Submission')) {
        return $output;
    }
    $submission = WPCF7_Submission::get_instance();
    $value = $submission ? trim((string) $submission->get_posted_data('tz-package')) : '';
    if ($value === '') {
        return '';
    }
    $value = mb_substr(wp_strip_all_tags($value), 0, 200);
    return 'Вариант услуги: ' . ($html ? esc_html($value) : $value);
}, 10, 3);
