<?php
/**
 * [about_hero] — первый экран страницы «О компании» (/about/, page 8).
 *
 * Слева хлебные крошки, заголовок, подзаголовок, абзац и кнопка на /services/;
 * справа «Umbrella Group сегодня» с пятью цифрами на фоне большой серой «U».
 * Ниже — четыре карточки преимуществ. Шапку page.php с крошками и h1 страница
 * не выводит — см. проверку has_shortcode() там.
 */

defined('ABSPATH') || exit;

/** Цифры справа: [число, подпись]. Первые три — те же, что в [team_page]. */
const UG_ABOUT_STATS = [
    ['35+', 'лет на&nbsp;рынке<br>консалтинговых услуг'],
    ['55+', 'специалистов<br>в&nbsp;штате'],
    ['7', 'направлений<br>экспертизы'],
    ['7&nbsp;845+', 'клиентов<br>доверяют нам'],
    ['5&nbsp;млрд&nbsp;₽', 'экономят наши клиенты<br>ежегодно'],
];

/** Карточки под первым экраном: [заголовок, текст, иконка — содержимое svg 24 × 24]. */
const UG_ABOUT_CARDS = [
    ['Комплексный подход', 'Объединяем экспертизу юристов, бухгалтеров, аудиторов и&nbsp;налоговых консультантов, чтобы решить задачу бизнеса целиком.',
        '<rect x="3.75" y="7.25" width="16.5" height="12" rx="1.75"/><path d="M9 7.25V5.5c0-.7.55-1.25 1.25-1.25h3.5c.7 0 1.25.55 1.25 1.25v1.75M3.75 12.25h16.5M10.5 12.25v1.5h3v-1.5" stroke-linejoin="round"/>'],
    ['Реальные решения', 'Предлагаем не&nbsp;теорию, а&nbsp;понятные и&nbsp;рабочие решения с&nbsp;учётом всех рисков и&nbsp;особенностей вашей отрасли.',
        '<path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke-linejoin="round"/><path d="m9 12 2.2 2.2L15.5 10" stroke-linecap="round" stroke-linejoin="round"/>'],
    ['Команда экспертов', 'За каждым проектом стоит команда специалистов, которые вовлечены в&nbsp;результат.',
        '<circle cx="9" cy="8.5" r="3.25"/><path d="M3.75 19.25c0-2.9 2.35-5 5.25-5s5.25 2.1 5.25 5" stroke-linecap="round"/><path d="M15.5 5.6a3.25 3.25 0 0 1 0 5.8M17.25 14.6c1.8.6 3 2.4 3 4.65" stroke-linecap="round"/>'],
    ['Долгосрочное сотрудничество', 'Сопровождаем бизнес на&nbsp;всех этапах развития и&nbsp;остаёмся рядом, когда появляются новые задачи.',
        '<rect x="4.25" y="13" width="3.5" height="6.75" rx=".75"/><rect x="10.25" y="9" width="3.5" height="10.75" rx=".75"/><rect x="16.25" y="4.25" width="3.5" height="15.5" rx=".75"/>'],
];

function ug_about_hero_shortcode(): string
{
    $stats = '';
    foreach (UG_ABOUT_STATS as [$num, $label]) {
        $stats .= '<div class="about-hero__stat"><b>' . $num . '</b><span>' . $label . '</span></div>';
    }
    $cards = '';
    foreach (UG_ABOUT_CARDS as [$title, $text, $icon]) {
        $cards .= '<div class="about-hero__card">'
            . '<div class="about-hero__icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' . $icon . '</svg></div>'
            . '<h3 class="about-hero__card-title">' . $title . '</h3>'
            . '<p class="about-hero__card-text">' . $text . '</p>'
            . '</div>';
    }
    $breadcrumbs = get_breadcrumbs();
    $services    = esc_url(home_url('/services/'));

    ug_enqueue_style('assets/css/blocks/about_hero.css');

    return <<<EOHTML
    <section class="about-hero">
        <div class="about-hero__inner">
            <div class="breadcrumbs">$breadcrumbs</div>
            <div class="about-hero__body">
                <div class="about-hero__text">
                    <h1 class="about-hero__title">О компании<br>Umbrella&nbsp;Group</h1>
                    <p class="about-hero__lead">Юридические, бухгалтерские, аудиторские и&nbsp;налоговые услуги для бизнеса&nbsp;— в&nbsp;одном месте, от&nbsp;одной команды.</p>
                    <p class="about-hero__desc">Мы помогаем предпринимателям на&nbsp;всех этапах развития бизнеса: от&nbsp;регистрации и&nbsp;получения лицензий до&nbsp;налогового планирования, бухгалтерского сопровождения, аудита и&nbsp;защиты интересов в&nbsp;спорах.</p>
                    <a href="$services" class="about-hero__btn">Подробнее о&nbsp;наших услугах</a>
                </div>
                <div class="about-hero__today">
                    <h2 class="about-hero__heading">Umbrella Group сегодня</h2>
                    <p class="about-hero__today-text">За годы работы мы выстроили сильную команду экспертов, собрали уникальную экспертизу и&nbsp;отработали эффективные процессы, чтобы бизнес наших клиентов развивался безопасно и&nbsp;уверенно.</p>
                    <div class="about-hero__stats">$stats</div>
                </div>
            </div>
            <div class="about-hero__cards">$cards</div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('about_hero', 'ug_about_hero_shortcode');

/** Иконки [about_specialization] — содержимое svg 24 × 24, обводка. */
const UG_ABOUT_ICONS = [
    'doc'    => '<path d="M14 3.75H7c-.7 0-1.25.55-1.25 1.25v14c0 .7.55 1.25 1.25 1.25h10c.7 0 1.25-.55 1.25-1.25V8L14 3.75Z" stroke-linejoin="round"/><path d="M13.75 4v4.25H18M9 12.5h6M9 16h6" stroke-linecap="round" stroke-linejoin="round"/>',
    'shield' => '<path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke-linejoin="round"/><path d="m9 12 2.2 2.2L15.5 10" stroke-linecap="round" stroke-linejoin="round"/>',
    'calc'   => '<rect x="5.25" y="3.75" width="13.5" height="16.5" rx="1.5"/><path d="M8.5 7.5h7" stroke-linecap="round"/><path d="M8.75 11.5h.5M11.75 11.5h.5M14.75 11.5h.5M8.75 14.5h.5M11.75 14.5h.5M14.75 14.5h.5M8.75 17.5h.5M11.75 17.5h.5M14.75 17.5h.5" stroke-width="2" stroke-linecap="round"/>',
    'bars'   => '<rect x="4.25" y="13" width="3.5" height="6.75" rx=".75"/><rect x="10.25" y="9" width="3.5" height="10.75" rx=".75"/><rect x="16.25" y="4.25" width="3.5" height="15.5" rx=".75"/>',
    'gear'   => '<circle cx="12" cy="12" r="2.75"/><path d="M10.4 3.75h3.2l.45 2.2 1.5.85 2.1-.75 1.6 2.75-1.7 1.5v1.4l1.7 1.5-1.6 2.75-2.1-.75-1.5.85-.45 2.2h-3.2l-.45-2.2-1.5-.85-2.1.75-1.6-2.75 1.7-1.5v-1.4L4.75 8.8l1.6-2.75 2.1.75 1.5-.85.45-2.2Z" stroke-linejoin="round"/>',
    'search' => '<circle cx="10.75" cy="10.75" r="6"/><path d="m15.25 15.25 4.5 4.5" stroke-linecap="round"/>',
    'scales' => '<path d="M12 4v16M7.5 20h9M5 7.25h14M12 4.75v2.5" stroke-linecap="round"/><path d="M7 7.5 4.25 13.5a2.75 2.75 0 0 0 5.5 0L7 7.5ZM17 7.5l-2.75 6a2.75 2.75 0 0 0 5.5 0L17 7.5Z" stroke-linejoin="round"/>',
    'bulb'    => '<path d="M9 17.5h6M10 20.5h4" stroke-linecap="round"/><path d="M12 3.5a5.75 5.75 0 0 0-3.4 10.4c.55.4.9 1 .9 1.7v1.9h5v-1.9c0-.7.35-1.3.9-1.7A5.75 5.75 0 0 0 12 3.5Z" stroke-linejoin="round"/>',
    'percent' => '<circle cx="12" cy="12" r="8.25"/><path d="m9 15 6-6" stroke-linecap="round"/><circle cx="9.25" cy="9.25" r="1"/><circle cx="14.75" cy="14.75" r="1"/>',
    'users'   => '<circle cx="9" cy="8.5" r="3.25"/><path d="M3.75 19.25c0-2.9 2.35-5 5.25-5s5.25 2.1 5.25 5" stroke-linecap="round"/><path d="M15.5 5.6a3.25 3.25 0 0 1 0 5.8M17.25 14.6c1.8.6 3 2.4 3 4.65" stroke-linecap="round"/>',
    'swap'   => '<path d="M5 8.5h13M15 5.25l3.25 3.25L15 11.75M19 15.5H6M9 12.25 5.75 15.5 9 18.75" stroke-linecap="round" stroke-linejoin="round"/>',
];

/**
 * Направления [about_specialization]: заголовок вкладки (\n — перенос), ID страницы
 * раздела для ссылки «Подробнее», иконка, заголовок и текст панели, теги,
 * три пункта [иконка, заголовок, текст].
 */
const UG_ABOUT_SPECS = [
    [
        'tab'   => "Регистрация\nи ликвидация",
        'page'  => 30,
        'icon'  => 'doc',
        'title' => 'Регистрация и&nbsp;ликвидация',
        'text'  => 'Полный спектр услуг по&nbsp;созданию бизнеса, его сопровождению и&nbsp;ликвидации. Берёмся за&nbsp;задачи любой сложности&nbsp;— от&nbsp;внесения изменений в&nbsp;учредительные документы до&nbsp;защиты интересов в&nbsp;корпоративных спорах.',
        'tags'  => ['Регистрация ООО и&nbsp;ИП', 'Изменения в&nbsp;ЕГРЮЛ', 'Ликвидация', 'Реорганизация', 'Корпоративные споры'],
        'items' => [
            ['doc', 'Регистрация бизнеса', 'Подготовим документы, подадим их&nbsp;в&nbsp;налоговую и&nbsp;получим готовые документы без вашего участия.'],
            ['swap', 'Изменения в&nbsp;компании', 'Сменим директора, адрес, состав участников, уставный капитал и&nbsp;виды деятельности.'],
            ['shield', 'Закрытие без рисков', 'Ликвидируем или реорганизуем компанию с&nbsp;минимальными рисками для собственников и&nbsp;директора.'],
        ],
    ],
    [
        'tab'   => "Юридические\nуслуги",
        'page'  => 32,
        'icon'  => 'shield',
        'title' => 'Юридические услуги',
        'text'  => 'Поможем наладить документооборот, взыскать долги, разрешить налоговые споры и&nbsp;представим интересы вашей компании в&nbsp;суде. Наши юристы&nbsp;— опытные специалисты, за&nbsp;результат ручаемся.',
        'tags'  => ['Договоры', 'Взыскание долгов', 'Арбитраж', 'Налоговые споры', 'Абонентское обслуживание'],
        'items' => [
            ['doc', 'Договорная работа', 'Разработаем и&nbsp;проверим договоры, учтём риски и&nbsp;интересы вашей стороны.'],
            ['search', 'Взыскание долгов', 'Проведём досудебную работу, подготовим иск и&nbsp;добьёмся исполнения решения.'],
            ['scales', 'Защита в&nbsp;суде', 'Представим интересы компании в&nbsp;арбитражных судах, судах общей юрисдикции и&nbsp;в&nbsp;спорах с&nbsp;налоговой.'],
        ],
    ],
    [
        'tab'   => 'Бухгалтерия',
        'page'  => 26,
        'icon'  => 'calc',
        'title' => 'Бухгалтерия',
        'text'  => 'Полностью или частично возьмём на&nbsp;себя обязанности по&nbsp;ведению бухгалтерского и&nbsp;налогового учёта. Поможем снизить налоговые риски, восстановим учёт и&nbsp;оптимизируем процессы.',
        'tags'  => ['Налоги', 'Отчётность', 'Кадровый учёт', 'Зарплата', 'Консультации'],
        'items' => [
            ['doc', 'Ведение учёта', 'Составим и&nbsp;сдадим всю необходимую отчётность в&nbsp;срок, с&nbsp;учётом требований законодательства.'],
            ['shield', 'Снижение рисков', 'Проконсультируем по&nbsp;сложным вопросам, поможем избежать ошибок и&nbsp;штрафов, предложим оптимальные решения.'],
            ['gear', 'Оптимизация процессов', 'Настроим удобный формат взаимодействия, автоматизируем рутинные задачи и&nbsp;поможем сэкономить на&nbsp;бухгалтерских услугах.'],
        ],
    ],
    [
        'tab'   => 'Аудит',
        'page'  => 34,
        'icon'  => 'bars',
        'title' => 'Аудит',
        'text'  => 'Все виды аудита от&nbsp;аттестованных специалистов. После проверки предоставим полный экспертный отчёт и&nbsp;дадим детальные рекомендации по&nbsp;устранению ошибок.',
        'tags'  => ['Обязательный аудит', 'Инициативный аудит', 'Налоговый аудит', 'Due diligence'],
        'items' => [
            ['doc', 'Аудиторское заключение', 'Проверим отчётность и&nbsp;выдадим аудиторское заключение в&nbsp;установленный срок.'],
            ['search', 'Проверка учёта', 'Найдём ошибки в&nbsp;бухгалтерском и&nbsp;налоговом учёте раньше, чем их&nbsp;найдёт налоговая.'],
            ['bars', 'Рекомендации', 'Дадим понятный отчёт и&nbsp;план, как исправить ошибки и&nbsp;выстроить учёт.'],
        ],
    ],
    [
        'tab'   => 'Лицензирование',
        'page'  => 28,
        'icon'  => 'doc',
        'title' => 'Лицензирование',
        'text'  => 'Оформим лицензию под ключ: соберём документы, пройдём экспертизы, сопроводим рассмотрение заявки и&nbsp;проверки, поможем с&nbsp;продлением и&nbsp;переоформлением. Работаем максимально оперативно.',
        'tags'  => ['Медицинская', 'Образовательная', 'Алкоголь', 'МЧС', 'Отходы'],
        'items' => [
            ['doc', 'Подготовка документов', 'Проверим соответствие лицензионным требованиям и&nbsp;соберём полный пакет документов.'],
            ['shield', 'Сопровождение проверок', 'Сопроводим экспертизы и&nbsp;проверки лицензирующего органа до&nbsp;получения лицензии.'],
            ['swap', 'Переоформление', 'Внесём изменения в&nbsp;реестр лицензий при смене адреса, видов работ или реквизитов.'],
        ],
    ],
    [
        'tab'   => "Интеллектуальное\nправо",
        'page'  => 29220,
        'icon'  => 'bulb',
        'title' => 'Интеллектуальное право',
        'text'  => 'Интеллектуальная собственность&nbsp;— важный актив бизнеса, который требует не&nbsp;только регистрации, но&nbsp;и&nbsp;правовой защиты. Сопровождаем оформление, использование и&nbsp;защиту исключительных прав.',
        'tags'  => ['Товарные знаки', 'Лицензионные договоры', 'Коммерческая концессия', 'Защита прав', 'Споры'],
        'items' => [
            ['doc', 'Регистрация товарного знака', 'Проверим обозначение, подготовим заявку и&nbsp;сопроводим регистрацию в&nbsp;Роспатенте.'],
            ['swap', 'Договоры на&nbsp;права', 'Оформим лицензионный договор, коммерческую концессию или отчуждение исключительного права.'],
            ['scales', 'Защита и&nbsp;споры', 'Проведём претензионную работу и&nbsp;защитим ваши права в&nbsp;суде при нарушениях.'],
        ],
    ],
    [
        'tab'   => "Налоговое\nправо",
        'page'  => 30352,
        'icon'  => 'percent',
        'title' => 'Налоговое право',
        'text'  => 'Сопровождаем налоговые проверки, разрешаем споры с&nbsp;налоговыми органами и&nbsp;консультируем по&nbsp;структурированию бизнеса с&nbsp;учётом действующего законодательства и&nbsp;возможных рисков.',
        'tags'  => ['Налоговые проверки', 'Налоговые споры', 'Оценка рисков', 'Структурирование бизнеса', 'Консультации'],
        'items' => [
            ['shield', 'Сопровождение проверок', 'Подготовим компанию к&nbsp;проверке, будем рядом на&nbsp;всех этапах и&nbsp;оспорим необоснованные требования.'],
            ['scales', 'Налоговые споры', 'Обжалуем решения налоговой в&nbsp;вышестоящем органе и&nbsp;в&nbsp;суде.'],
            ['search', 'Оценка рисков', 'Проверим сделки и&nbsp;структуру бизнеса и&nbsp;предложим, как законно снизить налоговые риски.'],
        ],
    ],
];

function ug_about_icon(string $name, int $size): string
{
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' . UG_ABOUT_ICONS[$name] . '</svg>';
}

/**
 * [about_specialization] — «Наша специализация» на странице «О компании»:
 * слева вкладки направлений, справа панель выбранного. Первая вкладка открыта сразу.
 * Высоту задаёт панель; вкладки, что не влезли, листаются полосой прокрутки справа.
 */
function ug_about_specialization_shortcode(): string
{
    $tabs   = '';
    $panels = '';
    foreach (UG_ABOUT_SPECS as $i => $s) {
        $on   = $i === 0;
        $tabs .= '<button type="button" class="about-spec__tab' . ($on ? ' is-active' : '') . '" role="tab" id="about-spec-tab-' . $i . '" aria-controls="about-spec-panel-' . $i . '" aria-selected="' . ($on ? 'true' : 'false') . '">'
            . '<span class="about-spec__tab-icon">' . ug_about_icon($s['icon'], 30) . '</span>'
            . '<span class="about-spec__tab-label">' . nl2br(esc_html($s['tab']), false) . '</span>'
            . '</button>';

        $tags = '';
        foreach ($s['tags'] as $tag) {
            $tags .= '<span class="about-spec__tag">' . $tag . '</span>';
        }
        $items = '';
        foreach ($s['items'] as [$icon, $title, $text]) {
            $items .= '<div class="about-spec__item">'
                . '<div class="about-spec__item-head"><span class="about-spec__item-icon">' . ug_about_icon($icon, 24) . '</span>'
                . '<span class="about-spec__item-title">' . $title . '</span></div>'
                . '<p class="about-spec__item-text">' . $text . '</p>'
                . '</div>';
        }
        $panels .= '<div class="about-spec__panel" role="tabpanel" id="about-spec-panel-' . $i . '" aria-labelledby="about-spec-tab-' . $i . '"' . ($on ? '' : ' hidden') . '>'
            . '<h3 class="about-spec__title">' . $s['title'] . '</h3>'
            . '<p class="about-spec__text">' . $s['text'] . '</p>'
            . '<div class="about-spec__tags">' . $tags . '</div>'
            . '<div class="about-spec__items">' . $items . '</div>'
            . '<a href="' . esc_url(get_permalink($s['page'])) . '" class="about-spec__more">Подробнее о&nbsp;направлении</a>'
            . '<a href="#header-contact-form-lightbox" class="about-hero__btn">Получить консультацию</a>'
            . '</div>';
    }

    ug_enqueue_style('assets/css/blocks/about_hero.css');
    ug_enqueue_script('assets/js/blocks/about_hero.js');

    return <<<EOHTML
    <section class="about-spec">
        <div class="about-spec__inner">
            <h2 class="about-spec__heading">Наша специализация</h2>
            <p class="about-spec__lead">Комплексные решения для бизнеса на&nbsp;всех этапах его развития. Объединяем экспертизу в&nbsp;правовой, бухгалтерской, налоговой и&nbsp;аудиторской сферах, чтобы вы&nbsp;могли сосредоточиться на&nbsp;главном&nbsp;— развитии своего дела.</p>
            <div class="about-spec__body">
                <div class="about-spec__tabs"><div class="about-spec__tabs-scroll" role="tablist" aria-orientation="vertical">$tabs</div></div>
                <div class="about-spec__panels">$panels</div>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('about_specialization', 'ug_about_specialization_shortcode');

/** Принципы [about_mission]: [заголовок, текст, иконка из UG_ABOUT_ICONS]. */
const UG_ABOUT_PRINCIPLES = [
    ['Прибыль важнее всего, <br>но&nbsp;честь дороже прибыли.', 'Мы выстраиваем долгосрочные отношения и&nbsp;всегда действуем в&nbsp;интересах клиента, даже если это требует непростых решений.', 'bars'],
    ['Оправдывай доверие, <br>в&nbsp;нем основа предпринимательства <br>и&nbsp;ключ к&nbsp;успеху.', 'Мы ценим доверие клиентов, партнёров и&nbsp;команды. Выполняем обязательства и&nbsp;держим слово в&nbsp;каждой задаче.', 'shield'],
    ['Конкурируй достойно. <br>Самый надежный партнер тот, <br>который тоже выигрывает от&nbsp;сделки.', 'Мы за&nbsp;честный рынок, профессиональный подход и&nbsp;взаимную выгоду. Строим партнёрство, в&nbsp;котором каждая сторона растёт и&nbsp;достигает своих целей.', 'users'],
];

/** [about_mission] — «Миссия и принципы» на странице «О компании»: цитата слева, три принципа справа. */
function ug_about_mission_shortcode(): string
{
    $items = '';
    foreach (UG_ABOUT_PRINCIPLES as $i => [$title, $text, $icon]) {
        $items .= '<div class="about-mission__item">'
            . '<span class="about-mission__num">' . sprintf('%02d', $i + 1) . '</span>'
            . '<div class="about-mission__body"><div class="about-mission__title">' . $title . '</div>'
            . '<p class="about-mission__text">' . $text . '</p></div>'
            . '<span class="about-mission__icon">' . ug_about_icon($icon, 40) . '</span>'
            . '</div>';
    }

    ug_enqueue_style('assets/css/blocks/about_hero.css');

    return <<<EOHTML
    <section class="about-mission">
        <div class="about-mission__inner">
            <div class="about-mission__intro">
                <h2 class="about-spec__heading about-mission__heading">Миссия<br>и&nbsp;принципы</h2>
                <blockquote class="about-mission__quote">Помогая российскому бизнесу родиться, жить, расти и&nbsp;развиваться, мы&nbsp;помогаем России в&nbsp;целом, улучшая качество жизни каждого в&nbsp;отдельности</blockquote>
            </div>
            <div class="about-mission__list">$items</div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('about_mission', 'ug_about_mission_shortcode');

/**
 * Вехи [about_history]: год, заголовок окна, события и место узла на дереве
 * в координатах viewBox 1000 × UG_ABOUT_TREE_H. 1990 — корни, 1995 и 2000 — ствол,
 * дальше — раскидистые ветки, 2025 — макушка. Ветки в ug_about_history_tree()
 * проложены через эти точки — двигаешь узел, двигай и ветку.
 */
const UG_ABOUT_TREE_H = 840;

const UG_ABOUT_HISTORY = [
    ['1990', 'Начало пути', [
        'В&nbsp;августе 1990 года на&nbsp;зарождающемся рынке создана компания Объединение «Регистрация».',
        'В&nbsp;названии учредители отразили объединение специалистов разных направлений и&nbsp;главное дело&nbsp;— помощь в&nbsp;создании новых компаний.',
    ], [520, 765]],
    ['1995', 'Стремительное развитие', [
        'Открыли бухгалтерское направление.',
        'В&nbsp;1996 году создали аудиторское направление&nbsp;— сейчас это самостоятельный бизнес «Центр-Аудит».',
        'Клиенты приходят с&nbsp;новыми вопросами: налоговое планирование, аудит, бухгалтерское сопровождение, лицензирование.',
        'Открыли офисы в&nbsp;Барнауле, Санкт-Петербурге и&nbsp;Москве.',
    ], [540, 625]],
    ['2000', 'Второе десятилетие', [
        'Запустили сайт для клиентов taxlab.ru.',
        'Зарегистрировали более 15&nbsp;000 юридических лиц.',
        'В&nbsp;2004 году начал работу собственный учебный центр.',
    ], [497, 535]],
    ['2005', 'Качество во&nbsp;главе', [
        'Ввели внутреннюю систему контроля качества услуг.',
        'Учебный центр вошёл в&nbsp;тройку лидеров рейтинга «Делового квартала» среди центров бизнес-образования Новосибирска.',
        'Застраховали свои услуги на&nbsp;50&nbsp;000&nbsp;000 рублей.',
    ], [330, 425]],
    ['2010', 'Новые высоты', [
        'Открыли офис в&nbsp;МФЦ на&nbsp;пл.&nbsp;Труда,&nbsp;1&nbsp;— услуги стали быстрее и&nbsp;разнообразнее.',
        'Учебный центр «Бизнес Магистр» получил новую образовательную лицензию.',
        'В&nbsp;2014 году&nbsp;— 1&nbsp;место среди новосибирских компаний в&nbsp;рейтинге правовых компаний Право.ру и&nbsp;1&nbsp;место в&nbsp;рейтинге аудиторско-консалтинговых компаний «Эксперт-Сибирь».',
    ], [560, 405]],
    ['2015', '25&nbsp;лет и&nbsp;новый бренд', [
        'Отметили 25-летие и&nbsp;начали работать под брендом Umbrella Group, объединившим все направления.',
        'Реализовали более 30&nbsp;проектов по&nbsp;структурированию бизнеса клиентов.',
        'Аудиторское направление успешно прошло проверки Федерального казначейства и&nbsp;СРО «Российский союз аудиторов».',
        'Премия «Новосибирская марка» за&nbsp;развитие юридического консалтинга, в&nbsp;2017 году&nbsp;— 1&nbsp;место среди юридических компаний Новосибирска в&nbsp;рейтинге Право.ру.',
    ], [735, 300]],
    ['2020', '30&nbsp;лет', [
        'Быстро перешли на&nbsp;удалённый формат и&nbsp;были рядом с&nbsp;клиентами по&nbsp;всей России онлайн.',
        'Бесплатно консультировали предпринимателей в&nbsp;тяжёлое время пандемии.',
        'Запустили отдел продаж и&nbsp;расширили спектр услуг.',
        'Углубили контроль качества: ежемесячный замер NPS, внутренний аудит обслуживания, чёткие регламенты и&nbsp;стандарты.',
    ], [330, 255]],
    ['2025', '35&nbsp;лет', [
        'Одни из&nbsp;первых в&nbsp;Новосибирске зарегистрировали личный фонд.',
        'За&nbsp;плечами десятки вызовов, тысячи клиентов и&nbsp;десятки тысяч решений.',
        'Продолжаем расти: усиливаем экспертизу и&nbsp;расширяем направления&nbsp;— лучшие годы впереди!',
    ], [515, 120]],
];

/** Лист: основание в (x, y), повёрнут на $a градусов, длина $len; с чёрной обводкой. */
function ug_about_leaf(float $x, float $y, float $a, float $len, string $fill): string
{
    $w = $len * 0.4;
    return sprintf(
        '<path d="M0 0C%1$.1f %2$.1f %3$.1f %2$.1f %4$.1f 0C%3$.1f %5$.1f %1$.1f %5$.1f 0 0Z" fill="%6$s" stroke="#1a1a1a" stroke-width="3" stroke-linejoin="round" transform="translate(%7$.1f %8$.1f) rotate(%9$.1f)"/>',
        $len * 0.25, -$w, $len * 0.75, $len, $w, $fill, $x, $y, $a
    );
}

/**
 * Дерево [about_history] — раскидистое, в манере ретро-иллюстрации: белые ствол,
 * ветки и корни в жирном чёрном контуре, красные листья на концах веток.
 * Контур без швов: сначала все чёрные обводки, потом белые заливки поверх.
 */
function ug_about_history_tree(): string
{
    $ink = '#1a1a1a';
    $red = '#ec1c23';
    mt_srand(1990);
    $bez = static function (array $p, float $t): array {
        $u = 1 - $t;
        return [
            $u ** 3 * $p[0] + 3 * $u * $u * $t * $p[2] + 3 * $u * $t * $t * $p[4] + $t ** 3 * $p[6],
            $u ** 3 * $p[1] + 3 * $u * $u * $t * $p[3] + 3 * $u * $t * $t * $p[5] + $t ** 3 * $p[7],
        ];
    };

    // Ствол — S-образный, тремя кусками всё тоньше: [кривая, ширина белой части].
    $parts = [
        [[525, 712, 548, 650, 552, 600, 535, 560], 62],
        [[535, 560, 512, 515, 486, 470, 500, 420], 46],
        [[500, 420, 512, 380, 510, 350, 505, 320], 34],
    ];
    // Ветки: [кривая, ширина, есть ли листья на конце]. Через узлы лет — по точкам из UG_ABOUT_HISTORY.
    $limbs = [
        [[497, 470, 440, 455, 385, 440, 330, 425], 15, false],   // к 2005
        [[330, 425, 275, 410, 225, 390, 165, 395], 9, true],
        [[505, 330, 450, 300, 390, 270, 330, 255], 15, false],   // к 2020
        [[330, 255, 270, 240, 210, 235, 140, 265], 9, true],
        [[512, 395, 530, 400, 545, 404, 560, 405], 12, false],   // к 2010
        [[560, 405, 640, 400, 760, 390, 880, 420], 10, true],
        [[508, 330, 580, 320, 660, 315, 735, 300], 16, false],   // к 2015
        [[735, 300, 800, 288, 870, 300, 935, 340], 9, true],
        [[505, 320, 507, 250, 512, 190, 515, 120], 12, false],   // к 2025
        [[515, 120, 517, 95, 520, 75, 525, 50], 6, true],
        [[505, 320, 560, 270, 610, 220, 640, 150], 8, true],
        [[505, 330, 470, 290, 440, 240, 420, 165], 8, true],
        [[540, 560, 590, 545, 625, 520, 650, 490], 6, true],
        [[497, 520, 455, 510, 425, 495, 400, 470], 6, true],
    ];
    // Корни веером от основания.
    $roots = [];
    foreach ([[300, 795, 13], [380, 820, 11], [470, 835, 9], [575, 835, 9], [665, 820, 11], [745, 795, 13], [230, 755, 7], [815, 755, 7]] as [$x, $y, $w]) {
        $roots[] = [[525, 705, 525 + ($x - 525) * 0.25, 728, 525 + ($x - 525) * 0.7, $y - 60, $x, $y], $w];
    }

    $black  = '';
    $white  = '';
    $leaves = '';
    $stroke = static function (array $p, float $w) use ($ink, &$black, &$white): void {
        $d      = vsprintf('M%.1f %.1fC%.1f %.1f %.1f %.1f %.1f %.1f', $p);
        $black .= sprintf('<path d="%s" stroke="%s" stroke-width="%.1f" fill="none" stroke-linecap="round"/>', $d, $ink, $w + 10);
        $white .= sprintf('<path d="%s" stroke="#fff" stroke-width="%.1f" fill="none" stroke-linecap="round"/>', $d, $w);
    };
    // Тон листа: больше всего красных, много бледно-розовых для пышности, изредка белые.
    $tone = static fn (): string => ['#ec1c23', '#ec1c23', '#ec1c23', '#ec1c23', '#f7b2b5', '#f7b2b5', '#fbd3d5', '#fff'][mt_rand(0, 7)];
    // Листья вдоль конца ветки — по очереди с двух сторон, последний — по направлению ветки.
    $along = static function (array $p, float $t0, int $n) use (&$leaves, $bez, $tone): void {
        for ($k = 0; $k < $n; $k++) {
            $t         = $t0 + (1 - $t0) * $k / max(1, $n - 1);
            [$x, $y]   = $bez($p, $t);
            [$x2, $y2] = $bez($p, min(1, $t + 0.02));
            [$x1, $y1] = $bez($p, max(0, $t - 0.02));
            $a         = atan2($y2 - $y1, $x2 - $x1) * 180 / M_PI;
            $a        += $k === $n - 1 ? mt_rand(-10, 10) : ($k % 2 ? 55 : -55) + mt_rand(-12, 12);
            $len       = mt_rand(32, 44);
            // Вокруг плодов-годов — поляна без листьев, иначе красный плод тонет в листве.
            $cx        = $x + cos(deg2rad($a)) * $len / 2;
            $cy        = $y + sin(deg2rad($a)) * $len / 2;
            foreach (UG_ABOUT_HISTORY as [, , , [$nx, $ny]]) {
                if (hypot($cx - $nx, $cy - $ny) < 62) {
                    continue 2;
                }
            }
            $leaves   .= ug_about_leaf($x, $y, $a, $len, $tone());
        }
    };
    foreach ($roots as [$p, $w]) {
        $stroke($p, $w);
        // Отросток корня.
        [$bx, $by] = $bez($p, 0.6);
        $stroke([$bx, $by, $bx, $by + 15, $bx + ($p[6] < 525 ? 15 : -15), $by + 30, $bx + ($p[6] < 525 ? 10 : -10), $by + 50], 4);
    }
    foreach ($parts as [$p, $w]) {
        $stroke($p, $w);
    }
    foreach ($limbs as [$p, $w, $tip]) {
        $stroke($p, $w);
        if ($tip) {
            $along($p, 0.3, 10);
        }
        // Побеги на 25–85 % длины, по очереди вверх и вниз, с листьями вдоль.
        foreach ([0.25, 0.45, 0.65, 0.85] as $i => $t) {
            if ($w < 7 && $t < 0.4) {
                continue;
            }
            [$bx, $by] = $bez($p, $t);
            $side      = $p[6] < $p[0] ? -1 : 1;
            $up        = $i % 2 ? 0.6 : -1;
            $tx        = $bx + $side * 34;
            $ty        = $by + $up * 55;
            $twig      = [$bx, $by, $bx + $side * 4, $by + $up * 25, $tx - $side * 10, $ty - $up * 12, $tx, $ty];
            $stroke($twig, 3.5);
            $along($twig, 0.25, 5);
        }
    }

    // Кора: красная тень по правому краю — та же кривая со сдвигом вправо — и штрихи.
    foreach (array_slice($parts, 0, 1) as [$p, $w]) {
        $q = $p;
        foreach ([0, 2, 4, 6] as $i) {
            $q[$i] += $w * 0.3;
        }
        $white .= sprintf('<path d="%s" stroke="%s" stroke-width="%.1f" fill="none"/>', vsprintf('M%.1f %.1fC%.1f %.1f %.1f %.1f %.1f %.1f', $q), $red, $w * 0.18);
    }
    $white .= '<path d="M512 690q8 -22 4 -44M518 615q6 -18 2 -32M496 525q-6 -16 -2 -30" stroke="' . $ink . '" stroke-width="4" fill="none" stroke-linecap="round"/>';

    // Земля — жирная линия с травинками.
    $ground = '<path d="M150 712H880" stroke="' . $ink . '" stroke-width="8" stroke-linecap="round"/>'
        . '<path d="M200 712l-6 -18M212 712l4 -16M820 712l-5 -17M832 712l6 -14M330 712l-3 -12M710 712l4 -12" stroke="' . $ink . '" stroke-width="5" stroke-linecap="round"/>';

    return '<svg class="about-history__svg" viewBox="0 0 1000 ' . UG_ABOUT_TREE_H . '" aria-hidden="true" focusable="false">' . $black . $white . $ground . $leaves . '</svg>';
}

/**
 * [about_history] — «История компании» деревом: корни 1990, ветки по годам, крона 2025.
 * Наведение или нажатие на год открывает окно с событиями; на телефоне — та же история
 * вертикальной лентой снизу вверх.
 */
function ug_about_history_shortcode(): string
{
    $nodes = '';
    $list  = '';
    foreach (UG_ABOUT_HISTORY as $i => [$year, $title, $events, [$x, $y]]) {
        $items = '<li>' . implode('</li><li>', $events) . '</li>';
        // Окно: у корней — над годом, у макушки — под ним, правее середины — слева, иначе — справа.
        $side   = $y > 700 ? ' is-center is-low' : ($y < 200 ? ' is-center' : ($x >= 600 ? ' is-right' : ' is-left'));
        $nodes .= '<div class="about-history__node' . $side . '" style="left:' . ($x / 10) . '%;top:' . round($y / UG_ABOUT_TREE_H * 100, 2) . '%">'
            . '<button type="button" class="about-history__year" aria-expanded="false" aria-controls="about-history-card-' . $i . '">' . $year . '</button>'
            . '<div class="about-history__card" id="about-history-card-' . $i . '" role="tooltip">'
            . '<div class="about-history__card-year">' . $year . '</div>'
            . '<div class="about-history__card-title">' . $title . '</div>'
            . '<ul class="about-history__events">' . $items . '</ul>'
            . '</div></div>';
        $list = '<li class="about-history__step"><div class="about-history__step-year">' . $year . '</div>'
            . '<div class="about-history__card-title">' . $title . '</div>'
            . '<ul class="about-history__events">' . $items . '</ul></li>' . $list;
    }
    $tree = ug_about_history_tree();

    ug_enqueue_style('assets/css/blocks/about_hero.css');
    ug_enqueue_script('assets/js/blocks/about_hero.js');

    return <<<EOHTML
    <section class="about-history">
        <div class="about-history__inner">
            <h2 class="about-spec__heading about-history__heading">История компании</h2>
            <p class="about-spec__lead about-history__lead">35+ лет роста&nbsp;— от&nbsp;корней в&nbsp;1990 году до&nbsp;кроны сегодня.<span class="about-history__hint"> Наведите на&nbsp;год, чтобы узнать, что происходило.</span></p>
            <div class="about-history__tree">$tree$nodes</div>
            <ol class="about-history__list">$list</ol>
        </div>
    </section>
    EOHTML;
}

add_shortcode('about_history', 'ug_about_history_shortcode');

/** [about_experience] — «Более 35 лет опыта» после дерева истории: текст с кнопкой слева, карточка «35+» справа. */
function ug_about_experience_shortcode(): string
{
    ug_enqueue_style('assets/css/blocks/about_hero.css');

    return <<<EOHTML
    <section class="about-exp">
        <div class="about-exp__inner">
            <div class="about-exp__text">
                <h2 class="about-exp__heading">Более 35&nbsp;лет опыта&nbsp;— чтобы решать задачи бизнеса сегодня</h2>
                <p>За&nbsp;историей Umbrella Group&nbsp;— не&nbsp;просто даты и&nbsp;этапы развития. Это накопленная экспертиза, команда специалистов разных направлений и&nbsp;тысячи бизнес-задач, которые мы&nbsp;уже проходили вместе с&nbsp;клиентами.</p>
                <p>Если у&nbsp;вашего бизнеса есть вопрос&nbsp;— начнём с&nbsp;него. Подключим нужных специалистов и&nbsp;предложим решение с&nbsp;учётом права, налогов, бухгалтерии и&nbsp;рисков.</p>
                <a href="#header-contact-form-lightbox" class="about-hero__btn about-exp__btn">Обсудить задачу</a>
            </div>
            <div class="about-exp__card">
                <div class="about-exp__num">35+</div>
                <div class="about-exp__caption">рядом с&nbsp;бизнесом</div>
                <p class="about-exp__note">От&nbsp;первой регистрации компании&nbsp;— до&nbsp;сложных корпоративных, налоговых и&nbsp;аудиторских задач.</p>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('about_experience', 'ug_about_experience_shortcode');
