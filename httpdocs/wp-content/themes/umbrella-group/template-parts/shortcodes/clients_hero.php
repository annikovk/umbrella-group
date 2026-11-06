<?php
/**
 * [clients_hero] — первый экран страницы «Клиенты и отзывы» (/about/clients/, page 1000).
 *
 * Слева хлебные крошки, заголовок в две строки, подзаголовок и три цифры
 * с вертикальными разделителями; справа картинка. Фильтры по отраслям и плитки
 * ниже рисует [ug_client_tiles] — этот блок их не трогает. Шапку page.php
 * с крошками и h1 страница не выводит — см. проверку has_shortcode() там.
 */

defined('ABSPATH') || exit;

/**
 * Фон всей секции — баннер заказчика 2172 × 724 (3 : 1): левые ~45% — пустой
 * светлый фон, справа бумаги и красный блокнот с логотипом. Рядом лежат
 * <имя>.webp (полный размер) и <имя>-m.{webp,jpg} — правая часть кадра
 * 1100 × 626 под телефон (≤849px), как -ug-m.webp в inc/perf.php.
 * Пусто — серая заглушка.
 */
const UG_CLIENTS_HERO_IMAGE = '/wp-content/themes/umbrella-group/assets/img/clients-hero.jpg';

/**
 * Цифры: [число, подпись в две строки] — оформление как у «Команды»
 * (team_hero_type.css). 35+ лет — как в [about_hero]; 7 — направления
 * экспертизы, как на «Команде».
 */
const UG_CLIENTS_STATS = [
    ['35+', 'лет работаем<br>с&nbsp;бизнесом'],
    ['55+', 'специалистов<br>в&nbsp;штате'],
    ['7', 'направлений<br>экспертизы'],
];

function ug_clients_hero_shortcode(): string
{
    $stats = '';
    foreach (UG_CLIENTS_STATS as [$num, $label]) {
        $stats .= '<div class="team-hero__stat"><b>' . $num . '</b><span>' . $label . '</span></div>';
    }
    $image = '<span class="clients-hero__stub">Картинка 2172&nbsp;×&nbsp;724</span>';
    if (UG_CLIENTS_HERO_IMAGE) {
        $base = esc_url(home_url(preg_replace('~\\.jpg$~', '', UG_CLIENTS_HERO_IMAGE)));
        $image = '<picture>'
            . '<source type="image/webp" media="(max-width: 849px)" srcset="' . $base . '-m.webp">'
            . '<source media="(max-width: 849px)" srcset="' . $base . '-m.jpg">'
            . '<source type="image/webp" srcset="' . $base . '.webp">'
            . '<img src="' . $base . '.jpg" alt="Красный блокнот с логотипом Umbrella Group на рабочем столе" width="2172" height="724" loading="eager" fetchpriority="high" decoding="async">'
            . '</picture>';
    }
    $breadcrumbs = get_breadcrumbs();

    // Заголовок с чертой, подзаголовок и цифры — классы и CSS первого экрана «Команды»
    ug_enqueue_style('assets/css/blocks/team_hero_type.css');
    ug_enqueue_style('assets/css/blocks/clients_hero.css');

    return <<<EOHTML
    <section class="clients-hero">
        <div class="clients-hero__inner">
            <div class="clients-hero__image">$image</div>
            <div class="breadcrumbs">$breadcrumbs</div>
            <h1 class="team-hero__title clients-hero__title">Клиенты и&nbsp;отзывы<br>Umbrella&nbsp;Group</h1>
            <p class="team-hero__lead clients-hero__lead">Работаем с&nbsp;компаниями из&nbsp;разных отраслей&nbsp;— от&nbsp;производства и&nbsp;энергетики до&nbsp;недвижимости и&nbsp;сферы услуг.</p>
            <div class="team-hero__stats">$stats</div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('clients_hero', 'ug_clients_hero_shortcode');
