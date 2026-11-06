<?php
/**
 * [clients_cases] — блок «Кейсы Umbrella Group», ведёт на страницу кейсов
 * /about/cases/. Стоит внизу страницы «Клиенты» (/about/clients/, page 1000)
 * и страниц услуг — вместо сквозной секции «Наши кейсы» с [ug_blog_tiles]
 * (та показывала две свежие статьи блога, а не кейсы).
 *
 * Два варианта (выбирает заказчик):
 *  - variant="cards" (по умолчанию) — заголовок, подзаголовок, «Все кейсы →»
 *    и три карточки кейсов: услуга-таблетка, заголовок, итог (case_outcome),
 *    направление внизу. Карточка ведёт на /about/cases/#<вкладка> — страница
 *    открывается на вкладке направления кейса (хэш разбирает cases_page.js).
 *    Кейсы: на странице раздела услуг (UG_CLIENTS_CASES_SECTIONS) — три
 *    самых свежих (post_date) кейса его направления, недостающие — как вне
 *    раздела; вне раздела — самый свежий из каждого направления, три самых
 *    свежих направления. ids="1,2,3" — свои кейсы по порядку;
 *  - variant="banner" — светлая плашка как [clients_tasks]: слева заголовок,
 *    подзаголовок и красная кнопка, справа цифры как в шапке: число
 *    опубликованных кейсов и число направлений (вкладок /about/cases/).
 *
 * Направление кейса — та же вкладка, что на /about/cases/: cases::page_tab().
 * Тот же блок «cards» рисует [cases] везде, кроме самой /about/cases/ —
 * сквозной блок кейсов страниц услуг и главной (см. cases::generate_shortcode).
 * Ширина — .clients-bottom из clients_tasks.css, заголовки PT Serif. Фон
 * секции прозрачный; сразу под [clients_cta] — серый фон страницы «Клиенты»
 * и стык с баннером (clients_cases.css, селектор соседа).
 */

defined('ABSPATH') || exit;

const UG_CLIENTS_CASES_URL = '/about/cases/';

/** Раздел услуг (ug_current_service_section) => вкладка /about/cases/. */
const UG_CLIENTS_CASES_SECTIONS = [
    'licensing' => 'Лицензирование',
    'licensing-maket1' => 'Лицензирование',
    'register-elimination' => 'Корпоративное право',
    'services-le' => 'Юридические услуги',
    'audit' => 'Аудит',
    'bukhgalterskie-uslugi' => 'Бухгалтерия',
    'intellektualnoe-pravo' => 'Интеллектуальное право',
    'nalogovoe-pravo' => 'Налоговое право',
];

/** Опубликованные кейсы: [пост, вкладка], свежие первыми. Мета — одним запросом. */
function ug_clients_cases_posts(): array
{
    static $list = null;
    if ($list === null) {
        $list = [];
        $posts = get_posts(['post_type' => 'case', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC']);
        foreach ($posts as $post) {
            $tab = cases::page_tab(
                $post->ID,
                (string) get_post_meta($post->ID, 'case_branch', true),
                (string) get_post_meta($post->ID, 'case_category', true)
            );
            if ($tab !== '') {
                $list[] = [$post, $tab];
            }
        }
    }
    return $list;
}

/** 1 кейс, 2 кейса, 5 кейсов. */
function ug_clients_cases_plural(int $n, string $one, string $few, string $many): string
{
    $m10 = $n % 10;
    $m100 = $n % 100;
    if ($m10 === 1 && $m100 !== 11) {
        return $one;
    }
    if ($m10 >= 2 && $m10 <= 4 && ($m100 < 12 || $m100 > 14)) {
        return $few;
    }
    return $many;
}

function ug_clients_cases_icon(string $path, int $size): string
{
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">' . $path . '</svg>';
}

const UG_CLIENTS_CASES_ICONS = [
    'arrow' => '<path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'tag' => '<path d="M4.75 4.75h6.6l8.15 8.15-6.6 6.6-8.15-8.15V4.75Z" stroke-linejoin="round"/><circle cx="8.5" cy="8.5" r="1.25"/>',
    'check' => '<circle cx="12" cy="12" r="8.75"/><path d="m8.25 12.25 2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>',
];

/**
 * Три кейса для карточек: сначала ids (по порядку), потом свежие направления
 * $tab — кейсы с case_category = $category первыми, — добор по одному самому
 * свежему из других направлений.
 */
function ug_clients_cases_pick(array $ids, string $tab = '', string $category = ''): array
{
    $all = ug_clients_cases_posts();
    $picked = [];
    $taken = [];
    $add = static function (array $item) use (&$picked, &$taken): void {
        if (count($picked) < 3 && !isset($taken[$item[0]->ID])) {
            $picked[] = $item;
            $taken[$item[0]->ID] = true;
        }
    };
    if ($ids) {
        $by_id = [];
        foreach ($all as $item) {
            $by_id[$item[0]->ID] = $item;
        }
        foreach ($ids as $id) {
            if (isset($by_id[$id])) {
                $add($by_id[$id]);
            }
        }
    }
    if ($tab !== '') {
        if ($category !== '') {
            foreach ($all as $item) {
                if ($item[1] === $tab && trim((string) get_post_meta($item[0]->ID, 'case_category', true)) === $category) {
                    $add($item);
                }
            }
        }
        foreach ($all as $item) {
            if ($item[1] === $tab) {
                $add($item);
            }
        }
    }
    $seen = array_fill_keys(array_column($picked, 1), true);
    foreach ($all as $item) {
        if (!isset($seen[$item[1]])) {
            $seen[$item[1]] = true;
            $add($item);
        }
    }
    return $picked;
}

function ug_clients_cases_card(WP_Post $post, string $tab): string
{
    $meta = static fn(string $key): string => trim((string) get_post_meta($post->ID, $key, true));
    $url = esc_url(home_url(UG_CLIENTS_CASES_URL) . '#' . transliterate($tab));
    $title = esc_html(get_the_title($post));
    $service = esc_html($meta('case_service') ?: $meta('case_category'));
    $outcome = esc_html($meta('case_outcome'));
    $tag = $service !== '' ? '<span class="clients-cases__tag">' . ug_clients_cases_icon(UG_CLIENTS_CASES_ICONS['tag'], 15) . $service . '</span>' : '';
    $result = $outcome !== ''
        ? '<span class="clients-cases__result">' . ug_clients_cases_icon(UG_CLIENTS_CASES_ICONS['check'], 22) . '<span><b>Результат</b>' . $outcome . '</span></span>'
        : '';
    $arrow = ug_clients_cases_icon(UG_CLIENTS_CASES_ICONS['arrow'], 18);
    $branch = esc_html($tab);

    return <<<EOHTML
    <a class="clients-cases__card" href="$url">
        $tag
        <span class="clients-cases__name">$title</span>
        $result
        <span class="clients-cases__foot">$branch$arrow</span>
    </a>
    EOHTML;
}

function ug_clients_cases_shortcode($atts): string
{
    $atts = shortcode_atts(['variant' => 'cards', 'ids' => ''], $atts, 'clients_cases');
    $url = esc_url(home_url(UG_CLIENTS_CASES_URL));
    $arrow = ug_clients_cases_icon(UG_CLIENTS_CASES_ICONS['arrow'], 20);
    $title = 'Кейсы Umbrella&nbsp;Group';
    $lead = 'Реальные задачи клиентов и&nbsp;результаты, которых мы добились.';

    ug_enqueue_style('assets/css/blocks/clients_tasks.css');
    ug_enqueue_style('assets/css/blocks/clients_cases.css');

    if ($atts['variant'] === 'banner') {
        $all = ug_clients_cases_posts();
        $cases = count($all);
        // Вкладки /about/cases/: направления кейсов и вкладки из PAGE_EXTRA_TABS
        $tabs = count(array_unique(array_merge(array_column($all, 1), array_values(cases::PAGE_EXTRA_TABS))));
        $stats = '';
        foreach ([
            [$cases, ug_clients_cases_plural($cases, 'кейс', 'кейса', 'кейсов') . '<br>с&nbsp;результатом'],
            [$tabs, ug_clients_cases_plural($tabs, 'направление', 'направления', 'направлений') . '<br>экспертизы'],
        ] as [$num, $label]) {
            $stats .= '<div class="clients-cases__stat"><b>' . $num . '</b><span>' . $label . '</span></div>';
        }

        return <<<EOHTML
        <section class="clients-bottom clients-bottom--cases">
            <div class="clients-bottom__inner">
                <div class="clients-cases clients-cases--banner">
                    <div class="clients-cases__text">
                        <h2 class="clients-cases__title">$title</h2>
                        <p class="clients-cases__lead">Показываем, как помогаем бизнесу снижать риски, экономить деньги и&nbsp;наводить порядок в&nbsp;учёте, документах и&nbsp;процессах.</p>
                        <a class="clients-cases__btn" href="$url">Смотреть все кейсы$arrow</a>
                    </div>
                    <div class="clients-cases__stats">$stats</div>
                </div>
            </div>
        </section>
        EOHTML;
    }

    $ids = array_values(array_filter(array_map('intval', explode(',', (string) $atts['ids']))));
    return ug_clients_cases_cards(UG_CLIENTS_CASES_SECTIONS[ug_current_service_section() ?? ''] ?? '', $ids);
}

/**
 * Вариант «cards»: $tab — вкладка /about/cases/ (пусто — без направления),
 * ids и $category — см. ug_clients_cases_pick(). Его же рисует [cases]
 * вне /about/cases/ (cases::generate_shortcode).
 */
function ug_clients_cases_cards(string $tab, array $ids = [], string $category = ''): string
{
    $url = esc_url(home_url(UG_CLIENTS_CASES_URL));
    $arrow = ug_clients_cases_icon(UG_CLIENTS_CASES_ICONS['arrow'], 20);
    $title = 'Кейсы Umbrella&nbsp;Group';
    $lead = 'Реальные задачи клиентов и&nbsp;результаты, которых мы добились.';

    ug_enqueue_style('assets/css/blocks/clients_tasks.css');
    ug_enqueue_style('assets/css/blocks/clients_cases.css');

    $cards = '';
    foreach (ug_clients_cases_pick($ids, $tab, $category) as [$post, $case_tab]) {
        $cards .= ug_clients_cases_card($post, $case_tab);
    }
    // С направлением «Все кейсы» открывает его вкладку
    $all_url = $tab !== '' ? esc_url(home_url(UG_CLIENTS_CASES_URL) . '#' . transliterate($tab)) : $url;

    return <<<EOHTML
    <section class="clients-bottom clients-bottom--cases">
        <div class="clients-bottom__inner">
            <div class="clients-cases clients-cases--cards">
                <div class="clients-cases__head">
                    <div>
                        <h2 class="clients-cases__title">$title</h2>
                        <p class="clients-cases__lead">$lead</p>
                    </div>
                    <a class="clients-cases__all" href="$all_url">Все кейсы$arrow</a>
                </div>
                <div class="clients-cases__list">$cards</div>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('clients_cases', 'ug_clients_cases_shortcode');

/**
 * Блок кейсов на странице один: [clients_cases] и [cases] рядом (или два
 * [cases], как на ликвидации ИП) дали бы плитки подряд — второй и дальше
 * вырезаются. Фильтр, а не флаг в шорткоде: schema.php прогоняет контент
 * через do_shortcode ещё до вывода. Внутри блока <section> нет — нежадного
 * совпадения до </section> хватает.
 */
add_filter('the_content', static function ($html) {
    $marker = '<section class="clients-bottom clients-bottom--cases">';
    $first = strpos((string) $html, $marker);
    if ($first === false || strpos($html, $marker, $first + 1) === false) {
        return $html;
    }
    $head = substr($html, 0, $first + strlen($marker));
    return $head . preg_replace('~' . preg_quote($marker, '~') . '.*?</section>~s', '', substr($html, $first + strlen($marker)));
}, 99);
