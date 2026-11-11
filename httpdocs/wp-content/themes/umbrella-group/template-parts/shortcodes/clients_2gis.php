<?php
/**
 * [clients_2gis] — блок «Ещё больше отзывов о нас — в 2ГИС» на странице
 * «Клиенты» (/about/clients/, page 1000), сразу под плитками [ug_client_tiles]
 * и перед [clients_tasks].
 *
 * Сверху заголовок и подзаголовок, ниже три карточки — три самых свежих
 * отзыва на 5 из 5 по обоим офисам, в которых есть что прочитать (не короче
 * UG_CLIENTS_2GIS_MIN_TEXT символов), под ними кнопка «Все отзывы в 2ГИС» —
 * на отзывы офиса на Горького, 34 (UG_CLIENTS_2GIS_MAIN). Общую оценку и
 * число отзывов офисов не показываем — так решила заказчица (4,6 у офиса на
 * площади Труда смотрится низко). Длинный текст режется CSS в 7 строк, под
 * ним «Читать полностью в 2ГИС» — на отзывы того офиса, о котором отзыв.
 *
 * Данные — публичный API отзывов 2ГИС (тот же, что у веб-версии 2gis.ru,
 * ключ оттуда же), кэш — transient на 12 часов. Последний удачный ответ
 * лежит в опции UG_CLIENTS_2GIS_LAST; не ответил 2ГИС и её нет — берём
 * UG_CLIENTS_2GIS_FALLBACK (отзывы на 11.11.2026), блок не ломается никогда.
 * После неудачи следующая попытка — через час, а не на каждом запросе.
 * Аватары 2ГИС не тянем: вместо них буква имени.
 *
 * Офисы — UG_CONTACTS_OFFICES из contacts_page.php (id карточек 2ГИС).
 * Фон и ширина — .clients-bottom из clients_tasks.css.
 */

defined('ABSPATH') || exit;

const UG_CLIENTS_2GIS_API = 'https://public-api.reviews.2gis.com/2.0/branches/%s/reviews';
const UG_CLIENTS_2GIS_KEY = '37c04fe6-a560-4549-b459-02309cf643ad';
const UG_CLIENTS_2GIS_CACHE = 'ug_clients_2gis_v2';
const UG_CLIENTS_2GIS_LAST = 'ug_clients_2gis_last_v2';
const UG_CLIENTS_2GIS_MAIN = '141265769353602';
const UG_CLIENTS_2GIS_MIN_TEXT = 80;
/** Длиннее — на карточке появляется «Читать полностью в 2ГИС» (7 строк ≈ 240 знаков на телефоне). */
const UG_CLIENTS_2GIS_LONG_TEXT = 240;

/** Слепок на 11.11.2026: три отзыва. */
const UG_CLIENTS_2GIS_FALLBACK = [
    'reviews' => [
        [
            'branch' => '141265769353602',
            'name'   => 'Сергей Sup_Nsk',
            'date'   => '2026-11-01',
            'text'   => 'Очень хорошая компания. Юристы решили все вопросы по двум фирмам. Консультации по бух учёту провели очень грамотно. Всем рекомендую',
        ],
        [
            'branch' => '141265769353602',
            'name'   => 'Ирина Черногаева',
            'date'   => '2026-10-22',
            'text'   => 'Хочу отметить высокий профессиональный уровень Демиденко Никиты за сопровождение по вопросам налогового структурирования бизнеса. Проведено несколько результативных встреч с собственниками по вопросам безопасности, ответственности и распределения рисков. Одни из тех на рынке, кому точно можно доверять юридическое сопровождение, особенно в части налогового и корпоративного права. Также благодарю Насонову Татьяну за помощь в разработке нормативной документации в сфере Трудового права. Татьяна подробно и без «жадности», как это часто бывает у консультантов, отвечала на вопросы, предлагала варианты, подсвечивала риски. В целом не раз обращались за разными услугами в юридический отдел. Очень довольны и благодарны. Во времена налогового прессинга бизнеса рекомендую компанию как надежного партнера.',
        ],
        [
            'branch' => '141265769353602',
            'name'   => 'Екатерина Василенко',
            'date'   => '2026-10-16',
            'text'   => 'Хочу выразить искреннюю благодарность юридической компании Umbrella Group и лично её руководителю Никите Алексеевичу. Обращалась не один раз и получала намного больше, чем ожидала: полную юридическую поддержку по всем вопросам, внимательное отношение и оперативность на каждом этапе. Руководитель лично вникал в детали, давал чёткие и понятные рекомендации, а вся команда работала слаженно и профессионально. Спасибо Вам за терпение, ответственность и готовность помочь в любой ситуации. Сейчас обратилась и за бухгалтерским сопровождением, уверена сработаемся. Рекомендую всем, кому важны профессионализм, честность и результат!',
        ],
    ],
];

function ug_clients_2gis_reviews_url(string $branch): string
{
    return 'https://2gis.ru/novosibirsk/firm/' . $branch . '/tab/reviews';
}

/** Отзывы одного офиса из API (подходящие) или null. */
function ug_clients_2gis_fetch_branch(string $branch): ?array
{
    $url = add_query_arg([
        'limit' => 20,
        'is_advertiser' => 'false',
        'sort_by' => 'date_created',
        'key' => UG_CLIENTS_2GIS_KEY,
        'locale' => 'ru_RU',
    ], sprintf(UG_CLIENTS_2GIS_API, $branch));
    $res = wp_remote_get($url, ['timeout' => 4, 'headers' => ['Referer' => 'https://2gis.ru/']]);
    if (is_wp_error($res) || wp_remote_retrieve_response_code($res) !== 200) {
        return null;
    }
    $data = json_decode(wp_remote_retrieve_body($res), true);
    if (!is_array($data) || !isset($data['reviews']) || !is_array($data['reviews'])) {
        return null;
    }
    $reviews = [];
    foreach ($data['reviews'] as $r) {
        $text = trim(preg_replace('/\s+/u', ' ', (string) ($r['text'] ?? '')));
        $name = trim((string) ($r['user']['name'] ?? ''));
        if ((int) ($r['rating'] ?? 0) !== 5 || !empty($r['is_hidden']) || !empty($r['on_moderation'])
            || $name === '' || mb_strlen($text) < UG_CLIENTS_2GIS_MIN_TEXT) {
            continue;
        }
        $reviews[] = [
            'branch' => $branch,
            'name'   => $name,
            'date'   => substr((string) ($r['date_created'] ?? ''), 0, 10),
            'text'   => $text,
        ];
    }
    return $reviews;
}

/** Три отзыва: кэш → API → последний удачный → слепок. */
function ug_clients_2gis_data(): array
{
    $cached = get_transient(UG_CLIENTS_2GIS_CACHE);
    if (is_array($cached)) {
        return $cached;
    }

    $reviews = [];
    $ok = true;
    foreach (UG_CONTACTS_OFFICES as $office) {
        $b = ug_clients_2gis_fetch_branch($office['org']);
        if ($b === null) {
            $ok = false;
            break;
        }
        $reviews = array_merge($reviews, $b);
    }
    usort($reviews, static fn($a, $b) => strcmp($b['date'], $a['date']));
    $reviews = array_slice($reviews, 0, 3);

    if ($ok && count($reviews) === 3) {
        $data = ['reviews' => $reviews];
        update_option(UG_CLIENTS_2GIS_LAST, $data, false);
        set_transient(UG_CLIENTS_2GIS_CACHE, $data, 12 * HOUR_IN_SECONDS);
        return $data;
    }

    $last = get_option(UG_CLIENTS_2GIS_LAST);
    $data = is_array($last) && !empty($last['reviews']) ? $last : UG_CLIENTS_2GIS_FALLBACK;
    set_transient(UG_CLIENTS_2GIS_CACHE, $data, HOUR_IN_SECONDS);
    return $data;
}

/** «1 ноября 2026». */
function ug_clients_2gis_date(string $ymd): string
{
    $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $ymd, $m)) {
        return '';
    }
    return (int) $m[3] . '&nbsp;' . $months[(int) $m[2] - 1] . ' ' . $m[1];
}

/** Пять жёлтых звёзд — оценка отзыва 5 из 5. */
function ug_clients_2gis_stars(): string
{
    $star = '<svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.6l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.4l-5.8 3.1 1.1-6.5L2.6 9.4l6.5-.9L12 2.6z"/></svg>';
    return '<span class="clients-2gis__stars" role="img" aria-label="Оценка 5 из 5">' . str_repeat($star, 5) . '</span>';
}

function ug_clients_2gis_shortcode(): string
{
    $data = ug_clients_2gis_data();
    $offices = [];
    foreach (UG_CONTACTS_OFFICES as $o) {
        $offices[$o['org']] = $o;
    }
    $main = UG_CLIENTS_2GIS_MAIN;
    $arrow = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    $pin = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21.25s-6.75-5.9-6.75-11.5a6.75 6.75 0 0 1 13.5 0c0 5.6-6.75 11.5-6.75 11.5Z" stroke-linejoin="round"/><circle cx="12" cy="9.75" r="2.2"/></svg>';

    $cards = '';
    foreach ($data['reviews'] as $r) {
        $branch = isset($offices[$r['branch']]) ? $r['branch'] : $main;
        $url = esc_url(ug_clients_2gis_reviews_url($branch));
        $initial = esc_html(mb_strtoupper(mb_substr($r['name'], 0, 1)));
        $more = mb_strlen($r['text']) > UG_CLIENTS_2GIS_LONG_TEXT
            ? '<a class="clients-2gis__more" href="' . $url . '" target="_blank" rel="noopener">Читать полностью в&nbsp;2ГИС' . $arrow . '</a>'
            : '';
        $cards .= '<article class="clients-2gis__card">'
            . '<header class="clients-2gis__author">'
            . '<span class="clients-2gis__avatar" aria-hidden="true">' . $initial . '</span>'
            . '<span class="clients-2gis__who">'
            . '<span class="clients-2gis__name">' . esc_html($r['name']) . '</span>'
            . '<span class="clients-2gis__meta">'
            . ug_clients_2gis_stars()
            . '<span class="clients-2gis__date">' . ug_clients_2gis_date($r['date']) . '</span>'
            . '</span>'
            . '</span>'
            . '</header>'
            . '<p class="clients-2gis__text">' . esc_html($r['text']) . '</p>'
            . $more
            . '<footer class="clients-2gis__office">' . $pin . $offices[$branch]['street'] . '</footer>'
            . '</article>';
    }

    $all = esc_url(ug_clients_2gis_reviews_url($main));
    ug_enqueue_style('assets/css/blocks/clients_tasks.css');
    ug_enqueue_style('assets/css/blocks/clients_2gis.css');

    return <<<EOHTML
    <section class="clients-bottom clients-bottom--2gis">
        <div class="clients-bottom__inner">
            <div class="clients-2gis">
                <div class="clients-2gis__head">
                    <div class="clients-2gis__intro">
                        <span class="clients-2gis__badge">2ГИС</span>
                        <h2 class="clients-2gis__title">Ещё больше отзывов о&nbsp;нас&nbsp;— в&nbsp;2ГИС</h2>
                        <p class="clients-2gis__lead">Что пишут о&nbsp;нас клиенты</p>
                    </div>
                </div>
                <div class="clients-2gis__grid">$cards</div>
                <div class="clients-2gis__foot">
                    <a class="clients-2gis__all" href="$all" target="_blank" rel="noopener">Все отзывы в&nbsp;2ГИС$arrow</a>
                </div>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('clients_2gis', 'ug_clients_2gis_shortcode');
