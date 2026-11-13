<?php
/**
 * Страница «Реквизиты» /rekvizity/ (page 27388) — шорткоды в post_content.
 *
 *     [ug_rekvizity title="Реквизиты компании" lead="Выберите организацию, …"
 *                   docs="/about/dokumenty-kompanii/" contacts="/about/contacts/"]
 *     [ug_rekv_org id="akkura" name="ООО «Аккура»"
 *                  full="Общество с ограниченной ответственностью «Аккура»"
 *                  inn="…" kpp="…" ogrn="…" director="…" contact="…"
 *                  address="…" postal="…" bank="ПАО Сбербанк" account="…" corr="…" bik="…"]
 *     …
 *     [/ug_rekvizity]
 *
 * Реквизиты правятся в контенте страницы (wp-admin или wp post update): одна
 * организация — один [ug_rekv_org], атрибут на строку; пустой атрибут — строки
 * нет. Порядок шорткодов — порядок вкладок, id — якорь вкладки (#akkura).
 * Кавычки внутри значений — только «ёлочки»: прямая " закрывает атрибут.
 *
 * Всё рисует [ug_rekvizity]: разбирает вложенные [ug_rekv_org] сам. Без JS
 * организации идут подряд; JS (assets/js/pages/rekvizity.js) делает вкладки,
 * копирование в буфер (все реквизиты и отдельные значения).
 *
 * «Скачать карточку» — ссылка на PDF assets/docs/rekvizity-<id>.pdf в теме.
 * PDF собирает tools/rekvizity-cards.mjs из этих же [ug_rekv_org]: поменяли
 * реквизиты — перегенерируйте и залейте PDF (порядок — в шапке скрипта).
 * Нет файла — нет и кнопки.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/** Поля организации: атрибут => подпись (в карточке и в тексте для буфера). */
function ug_rekv_fields(): array
{
    return [
        'inn'      => 'ИНН',
        'kpp'      => 'КПП',
        'ogrn'     => 'ОГРН',
        'director' => 'Генеральный директор',
        'contact'  => 'Контактное лицо',
        'address'  => 'Юридический адрес',
        'postal'   => 'Почтовый адрес',
        'bank'     => 'Банк',
        'account'  => 'Расчётный счёт',
        'corr'     => 'Корреспондентский счёт',
        'bik'      => 'БИК',
    ];
}

function ug_rekv_svg(string $name): string
{
    $paths = [
        'copy'     => '<rect x="8.5" y="8.5" width="11" height="11" rx="2"/><path d="M15.5 8.5V6.5a2 2 0 0 0-2-2h-7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h2"/>',
        'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
        'download' => '<path d="M12 4v11"/><path d="m7.5 10.5 4.5 4.5 4.5-4.5"/><path d="M4.5 16.5v2a1.5 1.5 0 0 0 1.5 1.5h12a1.5 1.5 0 0 0 1.5-1.5v-2"/>',
    ];
    return '<svg class="ugr-ic ugr-ic--' . $name . '" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ($paths[$name] ?? '') . '</svg>';
}

/** Организации из содержимого [ug_rekvizity]: [['id' => …, 'name' => …, 'full' => …, 'inn' => …], …]. */
function ug_rekv_parse(string $content): array
{
    $orgs = [];
    if (!preg_match_all('/' . get_shortcode_regex(['ug_rekv_org']) . '/su', $content, $m, PREG_SET_ORDER)) {
        return $orgs;
    }
    $keys = array_merge(['id', 'name', 'full'], array_keys(ug_rekv_fields()));
    foreach ($m as $x) {
        if ($x[1] === '[' && $x[6] === ']') {
            continue; // [[экранированный]]
        }
        $atts = shortcode_parse_atts($x[3]);
        $atts = is_array($atts) ? $atts : [];
        $org  = [];
        foreach ($keys as $k) {
            $v = html_entity_decode((string) ($atts[$k] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $org[$k] = trim((string) preg_replace('/\s+/u', ' ', wp_strip_all_tags($v)));
        }
        if ($org['name'] === '') {
            continue;
        }
        $org['id'] = sanitize_title($org['id'] !== '' ? $org['id'] : 'org-' . (count($orgs) + 1));
        $orgs[] = $org;
    }
    return $orgs;
}

/** Одна строка «подпись — значение»; $copy — кнопка копирования значения. */
function ug_rekv_field(array $org, string $key, bool $copy = false): string
{
    $value = $org[$key];
    if ($value === '') {
        return '';
    }
    $label = ug_rekv_fields()[$key];
    $btn = $copy
        ? '<button class="ugr-f__copy" type="button" data-copy="' . esc_attr($value) . '" aria-label="' . esc_attr('Скопировать: ' . $label) . '">'
            . ug_rekv_svg('copy') . ug_rekv_svg('check') . '<span class="ugr-f__tip" aria-hidden="true">Скопировано</span></button>'
        : '';
    return '<div class="ugr-f" data-k="' . esc_attr($key) . '"><dt class="ugr-f__k">' . esc_html($label) . '</dt>'
        . '<dd class="ugr-f__v"><span class="ugr-f__t">' . esc_html($value) . '</span>' . $btn . '</dd></div>';
}

/** Кнопка «Скачать карточку» — ссылка на PDF из tools/rekvizity-cards.mjs, если он есть. */
function ug_rekv_card(array $org): string
{
    $rel  = 'assets/docs/rekvizity-' . $org['id'] . '.pdf';
    $path = get_theme_file_path($rel);
    if (!is_file($path)) {
        return '';
    }
    $name = 'Реквизиты ' . trim((string) preg_replace(['/[«»"\/\\\\:*?<>|]+/u', '/\s+/u'], [' ', ' '], $org['name'])) . '.pdf';
    $url  = add_query_arg('ver', (string) filemtime($path), get_theme_file_uri($rel));
    return '<a class="ugr-btn ugr-btn--card" href="' . esc_url($url) . '" download="' . esc_attr($name) . '" type="application/pdf">'
        . ug_rekv_svg('download') . '<span class="ugr-btn__t">Скачать карточку</span>'
        . '<span class="screen-reader-text"> (PDF, ' . esc_html(size_format((int) filesize($path))) . ')</span></a>';
}

function ug_rekv_render_org(array $org, bool $active): string
{
    $id   = esc_attr($org['id']);
    $name = esc_html($org['name']);
    $full = $org['full'] !== '' ? '<p class="ugr-org__full">' . esc_html($org['full']) . '</p>' : '';

    $ids = '';
    foreach (['inn', 'kpp', 'ogrn'] as $k) {
        $ids .= ug_rekv_field($org, $k, $k === 'ogrn');
    }
    $ids  = $ids !== '' ? '<dl class="ugr-ids">' . $ids . '</dl>' : '';
    $info = '';
    foreach (['director', 'contact', 'address', 'postal'] as $k) {
        $info .= ug_rekv_field($org, $k);
    }
    $bank = '';
    foreach (['bank' => false, 'account' => true, 'corr' => true, 'bik' => false] as $k => $copy) {
        $bank .= ug_rekv_field($org, $k, $copy);
    }

    // Банковских реквизитов ещё нет — колонки нет вовсе, а не пустой заголовок.
    $bank = $bank !== ''
        ? '<div class="ugr-col"><h3 class="ugr-col__title">Банковские реквизиты</h3><dl class="ugr-list">' . $bank . '</dl></div>'
        : '';

    $hidden  = $active ? '' : ' data-off';
    $copy_ic = ug_rekv_svg('copy');
    $card    = ug_rekv_card($org);

    return <<<HTML
        <section class="ugr-org" id="ugr-{$id}" data-org="{$id}" role="tabpanel" aria-labelledby="ugr-tab-{$id}" tabindex="-1"{$hidden}>
            <h2 class="ugr-org__name">{$name}</h2>
            {$full}
            <div class="ugr-org__acts">
                <button class="ugr-btn ugr-btn--copy" type="button" data-copy-all>
                    {$copy_ic}<span class="ugr-btn__t" data-t="Скопировать реквизиты" data-done="Скопировано">Скопировать реквизиты</span>
                </button>
                {$card}
            </div>
            <div class="ugr-cols">
                <div class="ugr-col">
                    <h3 class="ugr-col__title">Сведения об организации</h3>
                    {$ids}<dl class="ugr-list">{$info}</dl>
                </div>
                {$bank}
            </div>
        </section>
        HTML;
}

add_shortcode('ug_rekvizity', static function ($atts, $content = ''): string {
    $atts = shortcode_atts([
        'title'    => 'Реквизиты компании',
        'lead'     => '',
        'docs'     => '/about/dokumenty-kompanii/',
        'contacts' => '/about/contacts/',
    ], (array) $atts);
    $orgs = ug_rekv_parse((string) $content);
    if (!$orgs) {
        return '';
    }

    $tabs = '';
    $main = '';
    foreach ($orgs as $i => $org) {
        $on   = $i === 0;
        $id   = esc_attr($org['id']);
        $tabs .= '<a class="ugr-tab' . ($on ? ' is-active' : '') . '" id="ugr-tab-' . $id . '" href="#' . $id . '" role="tab"'
            . ' aria-controls="ugr-' . $id . '" aria-selected="' . ($on ? 'true' : 'false') . '"' . ($on ? '' : ' tabindex="-1"') . '>'
            . esc_html($org['name']) . '</a>';
        $main .= ug_rekv_render_org($org, $on);
    }

    $crumbs = '<a href="' . esc_url(home_url('/')) . '">Главная</a><span class="ugr-crumbs__sep" aria-hidden="true">/</span>'
        . '<span aria-current="page">' . esc_html(get_the_title() ?: 'Реквизиты') . '</span>';
    $h1   = esc_html($atts['title']);
    $lead = $atts['lead'] !== '' ? '<p class="ugr-head__lead">' . esc_html($atts['lead']) . '</p>' : '';

    $links = '';
    if ($atts['docs'] !== '') {
        $links .= '<a class="ugr-more__link" href="' . esc_url($atts['docs']) . '">Документы компании<span class="ugr-arr" aria-hidden="true">→</span></a>';
    }
    if ($atts['contacts'] !== '') {
        $links .= '<a class="ugr-more__link" href="' . esc_url($atts['contacts']) . '">Связаться с нами<span class="ugr-arr" aria-hidden="true">→</span></a>';
    }

    return <<<HTML
        <div class="ugr" id="ugr">
            <script>document.getElementById('ugr').classList.add('is-js');</script>
            <div class="ugr__inner">
                <nav class="ugr-crumbs breadcrumbs" aria-label="Хлебные крошки">{$crumbs}</nav>
                <header class="ugr-head">
                    <h1 class="ugr-head__title">{$h1}</h1>
                    {$lead}
                </header>
                <div class="ugr-tabs" role="tablist" aria-label="Организации">{$tabs}</div>
                {$main}
                <p class="ugr-live screen-reader-text" aria-live="polite"></p>
                <aside class="ugr-more">
                    <p class="ugr-more__title">Нужны документы для договора?</p>
                    <div class="ugr-more__links">{$links}</div>
                </aside>
            </div>
        </div>
        HTML;
});

// Вложенный шорткод сам по себе ничего не выводит — его разбирает [ug_rekvizity].
add_shortcode('ug_rekv_org', static fn(): string => '');

function ug_is_rekv_page(): bool
{
    if (!is_singular('page')) {
        return false;
    }
    $post = get_queried_object();
    return $post instanceof WP_Post && has_shortcode($post->post_content, 'ug_rekvizity');
}

// wpautop не трогает страницу: атрибуты [ug_rekv_org] идут по строкам.
add_filter('the_content', static function ($content) {
    if (is_string($content) && str_contains($content, '[ug_rekvizity')) {
        remove_filter('the_content', 'wpautop');
        add_filter('the_content', static function ($c) {
            if (!has_filter('the_content', 'wpautop')) {
                add_filter('the_content', 'wpautop');
            }
            return $c;
        }, 99);
    }
    return $content;
}, 1);

// CSS — в <head> один раз (без копии в конце <body>), JS — в подвале.
add_action('wp', static function (): void {
    if (ug_is_rekv_page()) {
        ug_head_style('assets/css/pages/rekvizity.css', false);
    }
});
add_action('wp_enqueue_scripts', static function (): void {
    if (ug_is_rekv_page()) {
        ug_enqueue_script('assets/js/pages/rekvizity.js');
    }
});
