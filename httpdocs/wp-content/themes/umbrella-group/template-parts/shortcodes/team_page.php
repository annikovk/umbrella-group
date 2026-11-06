<?php
/**
 * [team_page img="…" cta_img="…"] — страница «Команда» (/about/team/, page 1262).
 *
 * Первый экран: хлебные крошки, заголовок, три цифры и групповое фото справа
 * (img — ID вложения; без него колонка фото пустая). Дальше карусель
 * руководства, «Работаем командой», вкладки по направлениям и карусель
 * специалистов: вкладка оставляет в ней своих людей и меняет её заголовок.
 * Последним — баннер «Не знаете, какой специалист нужен?» с фото справа
 * (cta_img — ID вложения, кадр 1500 × 560; без него справа серый фон). Карточки — руководство из UG_TEAM_LEADERS и записи
 * типа specialist (те же, что в [komanda_specialistov]). Шапку page.php с крошками и h1 страница не выводит —
 * см. проверку has_shortcode() там.
 */

defined('ABSPATH') || exit;

/**
 * Вкладки карусели специалистов: ключ → [подпись вкладки, заголовок карусели].
 * Порядок = порядок групп в «Все». Вкладка без единого человека не выводится.
 */
const UG_TEAM_TABS = [
    'all'     => ['Все специалисты', 'Команда специалистов'],
    'audit'   => ['Аудит', 'Аудит'],
    'buh'     => ['Бухгалтерия', 'Бухгалтерия'],
    'license' => ['Лицензирование', 'Лицензирование'],
    'ip'      => ['Интеллектуальное право', 'Интеллектуальное право'],
    'reg'     => ['Регистрация и ликвидация', 'Регистрация и ликвидация'],
    'tax'     => ['Налоговое право', 'Налоговое право'],
    'law'     => ['Юридические услуги', 'Юридические услуги'],
];

/** specialist_branch → [вкладка, тег направления]. */
const UG_TEAM_BRANCH_TAB = [
    'Аудит'                    => ['audit', 'Аудит'],
    'Бухгалтерия'              => ['buh', 'Бухгалтерия'],
    'Лицензирование'           => ['license', 'Лицензирование'],
    'Регистрация и ликвидация' => ['reg', 'Регистрация и ликвидация'],
    'Сделки с недвижимостью'   => ['reg', 'Регистрация и ликвидация'],
    'Юридические услуги'       => ['law', 'Юридические услуги'],
];

/**
 * Руководство — своя карусель под первым экраном и во главе своих направлений. img — ID вложения,
 * 0 — пустое место под фото (кадр 800 × 500); img_full — фото для окна
 * «Подробнее» (1200 × 1600), без него там фото карточки. specialist — ID записи того же
 * человека в specialist: её в общем списке пропускаем, а описание для окна
 * «Подробнее» берём из неё. quote — цитата, help — «Чем помогу», done — «Достижения», exp — опыт.
 */
const UG_TEAM_LEADERS = [
    [
        'name'  => 'Александр Владимирович Климов',
        'title' => 'Генеральный директор Umbrella Group',
        'tags'  => ['Налоги', 'Аудит', 'Холдинговые структуры'],
        'tabs'  => ['tax'],
        'img'   => 31099,
        'img_full' => 31154,
        'exp'   => 'Более 20 лет',
        'quote' => 'Выстрою налоговую модель, в которой бизнес растёт без лишних рисков. И останусь рядом, когда понадобится следующее решение',
        'done'  => 'Аттестованный аудитор, имеет аттестат Минфина России на право осуществления деятельности в области аудита. Ведущий аудитор компании с 2009 года. С 2014 года — генеральный директор Umbrella Group',
        'help'  => 'Выстраиваю налоговую стратегию бизнеса: от консультаций и налогового планирования, в том числе международного, до построения холдингов и реструктуризации. Провожу аудит и due diligence, комплексный экономический анализ и готовлю бизнес-планы — в том числе прошедшие экспертизу Минэкономразвития РФ',
    ],
    [
        'name'  => 'Ольга Викторовна Батурина',
        'title' => 'Директор департамента корпоративного, интеллектуального права и лицензирования',
        'tags'  => ['Корпоративное право', 'Регистрация', 'Сделки', 'Лицензирование', 'Интеллектуальное право'],
        'tabs'  => ['reg', 'license', 'ip'],
        'img'   => 31257,
        'img_full' => 31258,
        'exp'   => 'Более 25 лет',
        'quote' => 'Корпоративное право — это не про ограничения, а про безопасные возможности для роста. Мы делаем сложное простым, а риски — управляемыми',
        'help'  => 'Сопровождаю бизнес на всех этапах его жизненного цикла: от регистрации до реорганизации и ликвидации. Помогаю выстраивать изменения в структуре и документах так, чтобы они работали на цели компании, а не создавали новые риски',
        'done'  => 'Более 25 лет в корпоративном праве. Сопровождаю сложные корпоративные изменения, реорганизации и нестандартные правовые задачи бизнеса',
    ],
    [
        'name'       => 'Наталья Александровна Хохлова',
        'title'      => 'Директор департамента бухгалтерского учета и аудита',
        'tags'       => ['Аудит', 'Бухгалтерия', 'Отчётность'],
        'tabs'       => ['audit', 'buh'],
        'img'        => 0,
        'quote'      => 'Моя задача — чтобы аудит и бухгалтерия работали для клиента как единая система, без пробелов и лишних рисков',
        'specialist' => 11800,
    ],
    [
        'name'       => 'Никита Алексеевич Демиденко',
        'title'      => 'Директор департамента юридических услуг',
        'tags'       => ['Юридические услуги', 'Налоги', 'Суды'],
        'tabs'       => ['law', 'tax'],
        'img'        => 0,
        'specialist' => 11811,
    ],
];

/**
 * Вкладка направления → [доп. вкладка => должность в ней]: специалисты
 * направления показываются и в доп. вкладке, с другой подписью под именем.
 */
const UG_TEAM_ALSO_IN = [
    'license' => ['ip' => 'Юрист по интеллектуальному праву'],
];

/** Должности записей specialist по ID; без записи — по отделу, как в [komanda_specialistov]. */
const UG_TEAM_TITLES = [
    11807 => 'Ведущий аттестованный аудитор',
    11217 => 'Руководитель отдела бухгалтерского обслуживания',
    11226 => 'Руководитель отдела бухгалтерского обслуживания',
    11819 => 'Руководитель отдела корпоративного права',
    12851 => 'Руководитель отдела корпоративного права',
    11821 => 'Заместитель руководителя отдела корпоративного права',
];

/** Руководство; у кого есть запись specialist — фото из её полей «для карточки» и «для окна», опыт. */
function ug_team_page_leaders(): array
{
    $leaders = [];
    foreach (UG_TEAM_LEADERS as $p) {
        if (!empty($p['specialist'])) {
            $p['img']      = (int) get_post_meta($p['specialist'], 'specialist_photo_card', true) ?: $p['img'];
            $p['img_full'] = (int) get_post_meta($p['specialist'], 'specialist_photo_full', true);
        }
        // Тег опыта первым, как у специалистов: exp из кода или из записи specialist.
        $exp = preg_replace('/\s*опыта$/u', '', trim($p['exp'] ?? (string) get_post_meta($p['specialist'] ?? 0, 'specialist_experience', true)));
        if ($exp !== '') {
            array_unshift($p['tags'], 'Опыт ' . mb_strtolower($exp));
        }
        $leaders[] = $p;
    }
    return $leaders;
}

function ug_team_page_people(): array
{
    $people = ug_team_page_leaders();
    $skip   = array_filter(array_column(UG_TEAM_LEADERS, 'specialist'));

    $posts = get_posts([
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'ASC',
        'post_type'   => 'specialist',
    ]);
    ug_prime_thumbnails($posts);

    $rest = [];
    $seen = [];
    foreach ($posts as $post) {
        $branch = get_post_meta($post->ID, 'specialist_branch', true);
        $thumb  = (int) get_post_thumbnail_id($post->ID);
        $card   = (int) get_post_meta($post->ID, 'specialist_photo_card', true);
        $full   = (int) get_post_meta($post->ID, 'specialist_photo_full', true);
        // Вящикова заведена дважды (два отдела, одно фото) — показываем один раз.
        if (!isset(UG_TEAM_BRANCH_TAB[$branch]) || in_array($post->ID, $skip, true) || ($thumb && isset($seen[$thumb]))) {
            continue;
        }
        $seen[$thumb] = true;

        [$tab, $tag] = UG_TEAM_BRANCH_TAB[$branch];
        $main = get_post_meta($post->ID, 'specialist_main', true) === 'Да';
        $exp  = preg_replace('/\s*опыта$/u', '', trim(get_post_meta($post->ID, 'specialist_experience', true)));

        // Поля «Теги» и «Доп. вкладки» (через запятую): свои теги вместо
        // направления и вкладки сверх вкладки направления.
        $split = fn(string $k) => array_values(array_filter(array_map('trim', explode(',', (string) get_post_meta($post->ID, $k, true)))));
        $tags  = $split('specialist_team_tags') ?: [$tag];
        $extra = [];
        foreach ($split('specialist_team_tabs') as $label) {
            foreach (UG_TEAM_TABS as $key => [$tab_label]) {
                if ($key !== 'all' && mb_strtolower($tab_label) === mb_strtolower($label)) {
                    $extra[] = $key;
                }
            }
        }

        $rest[] = [
            // Специалисты — «Имя Фамилия» (отчество из specialist_name не берём),
            // руководство — «Имя Отчество Фамилия» в UG_TEAM_LEADERS.
            'name'  => trim(strtok(trim((string) get_post_meta($post->ID, 'specialist_name', true)), ' ') . ' ' . get_the_title($post->ID)),
            'title' => UG_TEAM_TITLES[$post->ID] ?? ug_team_page_position($post->ID, $branch, $main),
            'tags'  => array_filter(array_merge([$exp !== '' ? 'Опыт ' . mb_strtolower($exp) : ''], $tags)),
            'tabs'  => array_values(array_unique(array_merge([$tab], $extra, array_keys(UG_TEAM_ALSO_IN[$tab] ?? [])))),
            'tab_titles' => ug_team_page_tab_titles($post->ID, UG_TEAM_ALSO_IN[$tab] ?? []),
            'img'      => $card ?: $thumb,
            'card_img' => (bool) $card,
            'img_full' => $full,
            'post'     => $post->ID,
            'sort'  => [array_search($tab, array_keys(UG_TEAM_TABS), true), $main ? 0 : 1, count($rest)],
        ];
    }

    // Руководство уже стоит своей каруселью выше, поэтому в «Все» оно не первым,
    // а во главе своего направления. Дальше по порядку вкладок, руководители
    // отделов первыми.
    $tabs = array_keys(UG_TEAM_TABS);
    foreach ($people as $i => $p) {
        $rest[] = $p + ['sort' => [array_search($p['tabs'][0], $tabs, true), -1, $i]];
    }
    usort($rest, fn($a, $b) => $a['sort'] <=> $b['sort']);

    return $rest;
}

/** Должности по вкладкам: UG_TEAM_ALSO_IN плюс мета specialist_title_<вкладка>. */
function ug_team_page_tab_titles(int $id, array $titles): array
{
    foreach (array_keys(UG_TEAM_TABS) as $tab) {
        $title = trim((string) get_post_meta($id, 'specialist_title_' . $tab, true));
        if ($title !== '') {
            $titles[$tab] = $title;
        }
    }
    return $titles;
}

/**
 * У записей «только для страницы Команда» (specialist_team_only) должность —
 * поле «Должность»; у старых оно длинное и под карточку не подходит.
 */
function ug_team_page_position(int $id, string $branch, bool $main): string
{
    $position = trim((string) get_post_meta($id, 'specialist_position', true));
    if ($position !== '' && get_post_meta($id, 'specialist_team_only', true) === 'Да') {
        return $position;
    }
    return ug_team_page_title($branch, $main);
}

function ug_team_page_title(string $branch, bool $main): string
{
    if ($main) {
        return in_array($branch, ['Аудит', 'Юридические услуги'], true) ? 'Директор направления' : 'Руководитель отдела';
    }
    return [
        'Бухгалтерия'        => 'Бухгалтер',
        'Аудит'              => 'Ведущий аттестованный аудитор',
        'Юридические услуги' => 'Юрист',
        'Лицензирование'     => 'Юрист по лицензированию',
    ][$branch] ?? 'Специалист';
}

/**
 * Содержимое окна «Подробнее» — у всех одна схема: опыт, цитата, «Чем помогу»,
 * «Достижения». Тексты из записи specialist (post / specialist); у руководства
 * без записи «Чем помогу» — help. Чего нет — заглушка «В работе».
 */
function ug_team_page_more(array $p, string $tab = ''): string
{
    $id   = $p['post'] ?? $p['specialist'] ?? 0;
    $meta = fn(string $k) => $id ? trim((string) get_post_meta($id, $k, true)) : '';
    // Для вкладки — specialist_<поле>_<вкладка>, если заполнено; иначе как без вкладки.
    $tabbed = fn(string $k) => $tab !== '' ? $meta($k . '_' . $tab) : '';
    $todo = '<p class="team-modal__todo">В работе</p>';

    $exp   = preg_replace('/\s*опыта$/u', '', $p['exp'] ?? $meta('specialist_experience'));
    $quote = rtrim($tabbed('specialist_description') ?: ($p['quote'] ?? $meta('specialist_description')), '.');
    $help  = rtrim($tabbed('specialist_specialization') ?: ($p['help'] ?? $meta('specialist_specialization')), '.');
    $done  = $tabbed('specialist_achievements') ?: ($p['done'] ?? str_replace('{текущий минус 1}', (string) (date('Y') - 1), $meta('specialist_achievements')));

    $html = $exp !== ''
        ? '<div class="team-modal__exp">' . esc_html($exp) . ' опыта</div>'
        : '<div class="team-modal__exp team-modal__todo">Опыт — в работе</div>';
    $html .= $quote !== ''
        ? '<blockquote class="team-modal__quote">«' . esc_html($quote) . '»</blockquote>'
        : '<blockquote class="team-modal__quote team-modal__todo">В работе</blockquote>';

    $html .= '<div class="team-modal__label">Чем помогу</div>';
    if ($help !== '') {
        $html .= '<p>' . esc_html(mb_strtoupper(mb_substr($help, 0, 1)) . mb_substr($help, 1)) . '.</p>';
    } else {
        $html .= $todo;
    }

    $html .= '<div class="team-modal__label">Достижения</div>';
    $html .= $done !== '' ? '<p>' . wp_kses_post(rtrim($done, '.')) . '.</p>' : $todo;

    return $html;
}

function ug_team_page_card(array $p): string
{
    $photo = $p['img'] ? wp_get_attachment_image($p['img'], 'medium_large', false, [
        'class'   => 'team-leads__img',
        'loading' => 'lazy',
        'alt'     => esc_attr($p['name']),
    ]) : '';
    $tags = '';
    foreach ($p['tags'] as $tag) {
        $tags .= '<span class="team-leads__tag">' . esc_html($tag) . '</span>';
    }

    // Окно «Подробнее» собирает JS: фото, имя, должность и теги берёт из самой
    // карточки, остальное — из <template>. Свои тексты для вкладки (мета
    // specialist_*_<вкладка>) — в <template data-tab>, JS берёт его в этой вкладке.
    $link = '<button type="button" class="team-leads__more">Подробнее</button><template>' . ug_team_page_more($p) . '</template>';
    $id   = $p['post'] ?? $p['specialist'] ?? 0;
    foreach (array_unique(array_merge($p['tabs'], array_keys($p['tab_titles'] ?? []))) as $tab) {
        foreach (['specialist_description_', 'specialist_specialization_', 'specialist_achievements_'] as $k) {
            if ($id && get_post_meta($id, $k . $tab, true) !== '') {
                $link .= '<template data-tab="' . esc_attr($tab) . '">' . ug_team_page_more($p, $tab) . '</template>';
                break;
            }
        }
    }

    // Большое фото окна грузится только при открытии: здесь лишь его адрес.
    // Фото, снятое под карточку (800 × 500: у руководства всегда, у специалиста —
    // поле «Фото для карточки»), в окно не берём: пока нет 1200 × 1600, там пусто.
    $full = '';
    if (!empty($p['img_full']) && ($src = wp_get_attachment_image_url($p['img_full'], 'full'))) {
        $full = ' data-full="' . esc_url($src) . '"';
    } elseif (!isset($p['post']) || !empty($p['card_img'])) {
        $full = ' data-full=""';
    }

    // Должность по вкладкам: JS ставит data-title-<вкладка>, иначе data-title.
    // Во «Все» — должности всех вкладок человека через запятую (без повторов).
    $title  = $p['title'];
    $titles = '';
    if (!empty($p['tab_titles'])) {
        $all = [];
        foreach ($p['tabs'] as $tab) {
            $all[] = $p['tab_titles'][$tab] ?? $p['title'];
        }
        $all = array_values(array_unique($all));
        foreach ($all as $i => $t) {
            if ($i > 0) {
                $all[$i] = mb_strtolower(mb_substr($t, 0, 1)) . mb_substr($t, 1);
            }
        }
        $title  = implode(', ', $all);
        $titles = ' data-title="' . esc_attr($p['title']) . '" data-title-all="' . esc_attr($title) . '"';
        foreach ($p['tab_titles'] as $tab => $t) {
            $titles .= ' data-title-' . $tab . '="' . esc_attr($t) . '"';
        }
    }

    // Руководство — первым в любой вкладке направления (JS ставит order).
    $lead = isset($p['post']) ? '' : ' data-lead=""';

    // data-specialist — для ссылки /about/team/#specialist-<ID> (окно «Подробнее» кейса)
    $sid = $id ? ' data-specialist="' . (int) $id . '"' : '';

    return '<div class="team-leads__card has-more"' . $titles . $lead . $sid . ' data-tabs="' . esc_attr(implode(' ', $p['tabs'])) . '"' . $full . '>'
        . '<div class="team-leads__photo">' . $photo . '</div>'
        . '<div class="team-leads__body">'
        . '<div class="team-leads__name">' . esc_html($p['name']) . '</div>'
        . '<div class="team-leads__title">' . esc_html($title) . '</div>'
        . '<div class="team-leads__tags">' . $tags . '</div>'
        . $link
        . '</div></div>';
}

function ug_team_page_carousel(string $heading, array $people): string
{
    $heading = esc_html($heading);
    $cards   = implode('', array_map('ug_team_page_card', $people));

    return <<<EOHTML
    <div class="team-leads">
        <div class="team-leads__inner">
            <div class="team-leads__head">
                <h2 class="team-leads__heading">$heading</h2>
            </div>
            <div class="team-leads__viewport">
                <div class="team-leads__track">$cards</div>
                <button type="button" class="team-leads__arrow" data-dir="-1" aria-label="Назад"></button>
                <button type="button" class="team-leads__arrow team-leads__arrow--next" data-dir="1" aria-label="Вперёд"></button>
            </div>
        </div>
    </div>
    EOHTML;
}

/** «Работаем командой»: направление → [описание, иконка]. */
const UG_TEAM_TOGETHER = [
    'Право'       => ['Регистрация, ликвидация, корпоративные вопросы, лицензирование, юридическое обслуживание', '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 3.75H7A1.25 1.25 0 0 0 5.75 5v14A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V8L14 3.75Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M13.75 4v4.25H18" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M8.75 12.5h6.5M8.75 16h4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>'],
    'Налоги'      => ['Консалтинг, оптимизация, сопровождение проверок', '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5.75" y="3.75" width="12.5" height="16.5" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M8.75 7.25h6.5v2.5h-6.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 13h.01M12 13h.01M15 13h.01M9 16.5h.01M12 16.5h.01M15 16.5h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'],
    'Аудит'       => ['Финансовый анализ, выдача заключений, оценка рисков', '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 20.25V14.5M9.67 20.25V10M14.33 20.25V12.5M19 20.25V4.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M3.75 20.25h16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>'],
    'Бухгалтерия' => ['Ведение учета, отчетность, кадровые вопросы', '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="8.25" stroke="currentColor" stroke-width="1.5"/><path d="M10.25 16.5v-9h3a2.5 2.5 0 0 1 0 5h-4.5M8.75 14.75h4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'],
];

function ug_team_page_together(): string
{
    $items = [];
    foreach (UG_TEAM_TOGETHER as $title => [$text, $icon]) {
        $items[] = '<div class="team-together__item">'
            . '<div class="team-together__icon">' . $icon . '</div>'
            . '<div class="team-together__title">' . esc_html($title) . '</div>'
            . '<div class="team-together__text">' . esc_html($text) . '</div>'
            . '</div>';
    }
    $items = implode('<div class="team-together__plus" aria-hidden="true">+</div>', $items);

    return <<<EOHTML
    <section class="team-together">
        <div class="team-together__inner">
            <h2 class="team-together__heading">Работаем командой, а&nbsp;не&nbsp;отдельными специалистами</h2>
            <div class="team-together__main">
                <p class="team-together__lead">Один вопрос бизнеса часто затрагивает сразу несколько областей. Мы объединяем экспертизу юристов, аудиторов, бухгалтеров и&nbsp;налоговых консультантов, чтобы предложить комплексное решение.</p>
                <div class="team-together__items">$items</div>
            </div>
        </div>
    </section>
    EOHTML;
}

function ug_team_page_shortcode($atts): string
{
    $atts = shortcode_atts(['img' => '', 'cta_img' => ''], $atts);

    $people = ug_team_page_people();
    $tabs   = '';
    foreach (UG_TEAM_TABS as $key => [$label, $heading]) {
        $has = $key === 'all';
        foreach ($people as $p) {
            $has = $has || in_array($key, $p['tabs'], true);
        }
        if ($has) {
            $active = $key === 'all' ? ' is-active' : '';
            $tabs  .= '<button type="button" class="team-tabs__btn' . $active . '" data-tab="' . $key . '" data-heading="' . esc_attr($heading) . '">' . esc_html($label) . '</button>';
        }
    }
    $leaders     = ug_team_page_carousel('Руководство компании', ug_team_page_leaders());
    $specialists = ug_team_page_carousel(UG_TEAM_TABS['all'][1], $people);

    $photo = $atts['img'] ? wp_get_attachment_image((int) $atts['img'], 'full', false, [
        'class'         => 'team-hero__img',
        'fetchpriority' => 'high',
        'alt'           => 'Команда Umbrella Group',
    ]) : '';
    $cta_photo = $atts['cta_img'] ? wp_get_attachment_image((int) $atts['cta_img'], 'full', false, [
        'class'   => 'team-cta__img',
        'loading' => 'lazy',
        'alt'     => '',
    ]) : '';
    $breadcrumbs = get_breadcrumbs();
    $together    = ug_team_page_together();

    // Типографика первого экрана — общая с [clients_hero], раньше в team_page.css: каскад тот же
    ug_enqueue_style('assets/css/blocks/team_hero_type.css');
    ug_enqueue_style('assets/css/blocks/team_page.css');
    ug_enqueue_script('assets/js/blocks/team_page.js');

    return <<<EOHTML
    <section class="team-hero">
        <div class="team-hero__inner">
            <div class="breadcrumbs">$breadcrumbs</div>
            <div class="team-hero__body">
                <div class="team-hero__text">
                    <h1 class="team-hero__title">Команда<br>Umbrella&nbsp;Group</h1>
                    <p class="team-hero__lead">Юристы, аудиторы, бухгалтеры и&nbsp;эксперты, которые решают задачи бизнеса вместе</p>
                    <div class="team-hero__stats">
                        <div class="team-hero__stat"><b>35+</b><span>лет на&nbsp;рынке<br>консалтинговых услуг</span></div>
                        <div class="team-hero__stat"><b>55+</b><span>специалистов<br>в&nbsp;штате</span></div>
                        <div class="team-hero__stat"><b>7</b><span>направлений<br>экспертизы</span></div>
                    </div>
                </div>
                <div class="team-hero__photo">$photo</div>
            </div>
        </div>
    </section>
    <section class="team-lead-block">
        $leaders
    </section>
    $together
    <section class="team-list">
        <div class="team-list__inner">
            <div class="team-tabs">
                <button type="button" class="team-tabs__arrow" data-dir="-1" aria-label="Назад"></button>
                <div class="team-tabs__track" role="tablist">$tabs</div>
                <button type="button" class="team-tabs__arrow team-tabs__arrow--next" data-dir="1" aria-label="Вперёд"></button>
            </div>
        </div>
        $specialists
    </section>
    <section class="team-cta">
        <div class="team-cta__inner">
            <div class="team-cta__box">
                <div class="team-cta__photo">$cta_photo</div>
                <div class="team-cta__text">
                    <h2 class="team-cta__heading">Не знаете,<br>какой специалист нужен?</h2>
                    <p class="team-cta__lead">Опишите задачу — мы подключим эксперта нужного направления и&nbsp;предложим решение.</p>
                    <a href="#header-contact-form-lightbox" class="team-cta__btn">Получить консультацию</a>
                </div>
            </div>
        </div>
    </section>
    <div class="team-modal" hidden>
        <div class="team-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="team-modal-name">
            <button type="button" class="team-modal__close" aria-label="Закрыть"></button>
            <div class="team-modal__photo"></div>
            <div class="team-modal__body">
                <div class="team-modal__name" id="team-modal-name"></div>
                <div class="team-modal__title"></div>
                <div class="team-modal__tags"></div>
                <div class="team-modal__content"></div>
                <a href="#header-contact-form-lightbox" class="team-cta__btn team-modal__btn">Получить консультацию</a>
            </div>
        </div>
    </div>
    EOHTML;
}

add_shortcode('team_page', 'ug_team_page_shortcode');

/** [team_benefits] — «Что получает бизнес, работая с Umbrella Group», на странице «Команда» вместо «Кто мы и что делаем». */
const UG_TEAM_BENEFITS = [
    ['Один вопрос —<br>одна команда', 'Не отправляем клиента самостоятельно искать бухгалтера, аудитора или юриста другого профиля. Подключаем нужных специалистов внутри Umbrella Group.',
        '<circle cx="9" cy="8.5" r="3.25"/><path d="M3.75 19.25c0-2.9 2.35-5 5.25-5s5.25 2.1 5.25 5" stroke-linecap="round"/><path d="M15.5 5.6a3.25 3.25 0 0 1 0 5.8M17.25 14.6c1.8.6 3 2.4 3 4.65" stroke-linecap="round"/>'],
    ['Смотрим шире<br>одной задачи', 'При регистрации, сделке, лицензировании или налоговом вопросе оцениваем не&nbsp;только текущую задачу, но&nbsp;и&nbsp;связанные риски для бизнеса.',
        '<path d="M12 20.25H7A1.25 1.25 0 0 1 5.75 19V5A1.25 1.25 0 0 1 7 3.75h7L18.25 8v3" stroke-linejoin="round"/><path d="M13.75 4v4.25H18M8.75 12.5h4M8.75 16h2.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="16.5" cy="16.5" r="2.75"/><path d="m18.5 18.5 1.75 1.75" stroke-linecap="round"/>'],
    ['Говорим, что делать<br>дальше', 'Не ограничиваемся заключением или перечнем проблем. Объясняем варианты решения, последствия каждого и&nbsp;следующий шаг.',
        '<path d="M12 4.25c4.7 0 8.25 3.1 8.25 7s-3.55 7-8.25 7c-1 0-1.95-.13-2.83-.38L5 19.75l1.2-3.4C4.85 15.1 3.75 13.3 3.75 11.25c0-3.9 3.55-7 8.25-7Z" stroke-linejoin="round"/><path d="M8.5 11.25h.01M12 11.25h.01M15.5 11.25h.01" stroke-width="2.2" stroke-linecap="round"/>'],
    ['Остаёмся рядом<br>после решения вопроса', 'Многие задачи бизнеса не&nbsp;заканчиваются одним документом. Сопровождаем изменения, помогаем с&nbsp;последующими юридическими, бухгалтерскими и&nbsp;налоговыми вопросами.',
        '<path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke-linejoin="round"/><path d="m9 12 2.2 2.2L15.5 10" stroke-linecap="round" stroke-linejoin="round"/>'],
];

function ug_team_benefits_shortcode(): string
{
    $items = '';
    foreach (UG_TEAM_BENEFITS as [$title, $text, $icon]) {
        $items .= '<div class="team-benefits__item">'
            . '<div class="team-benefits__icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">' . $icon . '</svg></div>'
            . '<div><div class="team-benefits__title">' . $title . '</div>'
            . '<p class="team-benefits__text">' . $text . '</p></div>'
            . '</div>';
    }
    ug_enqueue_style('assets/css/blocks/team_page.css');

    return <<<EOHTML
    <section class="team-benefits">
        <div class="team-benefits__inner">
            <h2 class="team-benefits__heading">Что получает бизнес, работая с&nbsp;Umbrella&nbsp;Group</h2>
            <p class="team-benefits__lead">Мы объединяем экспертизу юристов, аудиторов, бухгалтеров и&nbsp;налоговых консультантов, чтобы решать задачи бизнеса комплексно&nbsp;&mdash; быстро, понятно и&nbsp;с&nbsp;учётом всех рисков.</p>
            <div class="team-benefits__grid">$items</div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('team_benefits', 'ug_team_benefits_shortcode');
