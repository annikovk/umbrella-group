<?php
/**
 * Страница «Стоимость» /price/ (page 12) — шорткоды прайса в post_content.
 *
 *     [ug_price_page title="Стоимость услуг в Новосибирске" lead="…" form="<ID CF7>"]
 *       [ug_price_dir id="lic" name="Лицензирование" title="Лицензирование" lead="…"
 *                     example="Например: …" anchor="section-licensing" url="/price/…/"]
 *         [ug_price_promo akciya="9897" title="…" text="…"]
 *         [ug_price title="Медицинская лицензия" sub="Подпись" price="от 85 000 ₽"
 *                   note="мелко под ценой" installment="1" group="Медицина и фармацевтика"
 *                   icon="cross" url="/services/…/"]
 *         - пункт «Что входит»
 *         Вариант | цена
 *         * сноска
 *         Просто текст
 *         [/ug_price]
 *         [ug_price_how]
 *         Фактор | пояснение
 *         [/ug_price_how]
 *       [/ug_price_dir]
 *     [/ug_price_page]
 *
 * Всё рисует [ug_price_page]: разбирает вложенные шорткоды сам (ug_price_parse)
 * и выводит весь прайс в HTML — все направления и все услуги, для поисковиков
 * и без JS (тогда направления идут подряд, детали раскрыты). JS
 * (assets/js/pages/price-list.js) показывает одно направление, по
 * UG_PRICE_PER_PAGE услуг на страницу, раскрывает «Подробнее», ищет по
 * услугам и подставляет услугу в форму внизу.
 *
 * Форма — CF7 «Форма расчёта на странице стоимости» (ID в атрибуте form).
 * Список «Какая услуга нужна?» (поле price-service) собирается из этой же
 * страницы фильтром wpcf7_form_tag: добавили услугу в прайс — она есть и в
 * форме, и в проверке значения при отправке.
 *
 * Иконки — inline SVG из ug_price_icons() (имя в атрибуте icon).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const UG_PRICE_PAGE_ID  = 12;
const UG_PRICE_PER_PAGE = 6;
const UG_PRICE_INSTALLMENT_TIP = 'Возможна предоплата 70%, остальные 30% оплачиваются после подписания акта о проделанных работах. Уточните детали и условия у специалиста';
const UG_PRICE_OPT_PLACEHOLDER = 'Выберите услугу';
const UG_PRICE_OPT_HELP  = 'Нужна помощь с выбором';
const UG_PRICE_OPT_OTHER = 'Другая услуга';

/** Иконки строк: содержимое <svg viewBox="0 0 24 24">, штрих currentColor. */
function ug_price_icons(): array
{
    return [
        'building'  => '<path d="M3.5 20.5h17"/><path d="M6 20.5V5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v15.5"/><path d="M15 9h3a1 1 0 0 1 1 1v10.5"/><path d="M9 8h3M9 11.5h3M9 15h3"/>',
        'person'    => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20c.8-3.6 3.6-5.5 7-5.5s6.2 1.9 7 5.5"/>',
        'chat'      => '<path d="M6.5 4.5h11a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H11l-4.5 3.5v-3.5a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2Z"/><path d="M8.5 9h7M8.5 12h4.5"/>',
        'cap'       => '<path d="M12 5 2.5 9.5 12 14l9.5-4.5L12 5Z"/><path d="M6.5 11.5v4c1.4 1.5 3.4 2.3 5.5 2.3s4.1-.8 5.5-2.3v-4"/><path d="M21.5 9.5v5"/>',
        'cross'     => '<path d="M9.5 4h5v5.5H20v5h-5.5V20h-5v-5.5H4v-5h5.5V4Z"/>',
        'heart'     => '<path d="M12 19.5s-7.5-4.4-7.5-10A4 4 0 0 1 12 7a4 4 0 0 1 7.5 2.5c0 5.6-7.5 10-7.5 10Z"/><path d="M7.5 12.5h2.5l1.2-2 1.8 4 1.2-2h2.3"/>',
        'capsule'   => '<g transform="rotate(-45 12 12)"><rect x="4" y="8.5" width="16" height="7" rx="3.5"/><path d="M12 8.5v7"/></g>',
        'drop'      => '<path d="M12 3.5s-6 6.6-6 10.9a6 6 0 0 0 12 0C18 10.1 12 3.5 12 3.5Z"/><path d="M9.2 14.5a2.9 2.9 0 0 0 2.8 2.8"/>',
        'glass'     => '<path d="M7.5 3.5h9l-.5 5a4 4 0 0 1-8 0l-.5-5Z"/><path d="M12 12.5v7.5M8.5 20.5h7"/><path d="M7.8 7h8.4"/>',
        'recycle'   => '<path d="M7 19H4.815a1.83 1.83 0 0 1-1.57-.881 1.785 1.785 0 0 1-.004-1.784L7.196 9.5"/><path d="M11 19h8.203a1.83 1.83 0 0 0 1.556-.89 1.784 1.784 0 0 0 0-1.775l-1.226-2.12"/><path d="m14 16-3 3 3 3"/><path d="M8.293 13.596 7.196 9.5 3.1 10.598"/><path d="m9.344 5.811 1.093-1.892A1.83 1.83 0 0 1 11.985 3a1.784 1.784 0 0 1 1.546.888l3.943 6.843"/><path d="m13.378 9.633 4.096 1.098 1.097-4.096"/>',
        'flame'     => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5Z"/>',
        'bug'       => '<ellipse cx="12" cy="14.5" rx="4.2" ry="5.8"/><path d="M12 10.5v9.5"/><ellipse cx="12" cy="7.3" rx="2.4" ry="1.8"/><path d="M10.9 5.7C9.9 3.9 8 3 5.5 3.2M13.1 5.7c1-1.8 2.9-2.7 5.4-2.5"/><path d="M8.2 11.6 5.3 10M7.8 14.8H4.3M8.3 18 5.6 20.4M15.8 11.6l2.9-1.6M16.2 14.8h3.5M15.7 18l2.7 2.4"/>',
        'bolt'      => '<path d="M13 3 5 13.5h6L10.5 21 19 10.5h-6L13 3Z"/>',
        'seal'      => '<path d="M11 20.5H7A1.5 1.5 0 0 1 5.5 19V5A1.5 1.5 0 0 1 7 3.5h7l4.5 4.5v2.5"/><path d="M14 3.5V8h4.5"/><path d="M8.5 11h5M8.5 14h3"/><circle cx="17" cy="15" r="2.6"/><path d="m15.6 17.3-.6 3.7 2-1.1 2 1.1-.6-3.7"/>',
        'renew'     => '<path d="M18.5 10V8L14 3.5H7A1.5 1.5 0 0 0 5.5 5v14A1.5 1.5 0 0 0 7 20.5h3"/><path d="M14 3.5V8h4.5"/><path d="M20 15.5a3.5 3.5 0 0 0-6.4-1.9"/><path d="M13 18a3.5 3.5 0 0 0 6.4 1.9"/><path d="M13.4 11.6v2.2h2.2"/><path d="M19.6 21.9v-2.2h-2.2"/>',
        'audit'     => '<rect x="5.5" y="4.5" width="13" height="16" rx="1.5"/><path d="M9 4.5v-1h6v1"/><path d="m9 13 2 2 4-4.5"/>',
        'inspect'   => '<circle cx="11" cy="11" r="6.5"/><path d="m20.5 20.5-4.9-4.9"/><path d="M8.5 11h5M11 8.5v5"/>',
        'chart'     => '<path d="M4 4v16h16"/><path d="m7.5 14.5 3.5-4 3 3 5-6"/>',
        'shield'    => '<path d="M12 3.5 5 6v5.5c0 4.3 3 7.6 7 9 4-1.4 7-4.7 7-9V6l-7-2.5Z"/><path d="m9 12 2 2 4-4"/>',
        'doc'       => '<path d="M14 3.5H7A1.5 1.5 0 0 0 5.5 5v14A1.5 1.5 0 0 0 7 20.5h10a1.5 1.5 0 0 0 1.5-1.5V8L14 3.5Z"/><path d="M14 3.5V8h4.5"/><path d="M8.5 12.5h7M8.5 16h5"/>',
        'pen'       => '<path d="M14 3.5H7A1.5 1.5 0 0 0 5.5 5v14A1.5 1.5 0 0 0 7 20.5h4"/><path d="M14 3.5V8h4.5v2"/><path d="m19.5 13.5-6 6-2.5.5.5-2.5 6-6a1.4 1.4 0 0 1 2 2Z"/>',
        'users'     => '<circle cx="9" cy="8.5" r="3"/><path d="M3.5 19c.6-3 2.8-4.8 5.5-4.8s4.9 1.8 5.5 4.8"/><path d="M15.5 5.8a3 3 0 0 1 0 5.4M17.5 14.5c1.6.6 2.7 2.2 3 4.5"/>',
        'usercheck' => '<circle cx="10" cy="8" r="3.5"/><path d="M3.5 20c.8-3.6 3.4-5.5 6.5-5.5 1.3 0 2.5.3 3.5.9"/><path d="m15.5 18 2 2 4-4"/>',
        'home'      => '<path d="M3.5 11 12 4l8.5 7"/><path d="M5.5 9.5v10h13v-10"/><path d="M10 19.5v-5h4v5"/>',
        'close'     => '<circle cx="12" cy="12" r="8.5"/><path d="m9 9 6 6M15 9l-6 6"/>',
        'alert'     => '<path d="M12 4 2.8 19.5h18.4L12 4Z"/><path d="M12 10v4.5M12 17v.5"/>',
        'pin'       => '<path d="M12 21s-6.5-6-6.5-11a6.5 6.5 0 0 1 13 0c0 5-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.5"/>',
        'coins'     => '<ellipse cx="9" cy="6.5" rx="5.5" ry="2.5"/><path d="M3.5 6.5v4c0 1.4 2.5 2.5 5.5 2.5s5.5-1.1 5.5-2.5v-4"/><path d="M3.5 10.5v4c0 1.4 2.5 2.5 5.5 2.5"/><ellipse cx="15" cy="14.5" rx="5.5" ry="2.5"/><path d="M9.5 14.5v3c0 1.4 2.5 2.5 5.5 2.5s5.5-1.1 5.5-2.5v-3"/>',
        'tag'       => '<path d="M3.5 12.2V4.5a1 1 0 0 1 1-1h7.7l8.3 8.3a1.4 1.4 0 0 1 0 2l-6.7 6.7a1.4 1.4 0 0 1-2 0L3.5 12.2Z"/><circle cx="8" cy="8" r="1.5"/>',
        'list'      => '<path d="M9 6h11M9 12h11M9 18h11"/><path d="M4 6h.5M4 12h.5M4 18h.5"/>',
        'swap'      => '<path d="M4 8h15l-3.5-3.5"/><path d="M20 16H5l3.5 3.5"/>',
        'merge'     => '<circle cx="18" cy="18" r="2.5"/><circle cx="6" cy="6" r="2.5"/><path d="M6 21V8.5a9 9 0 0 0 9.5 9.5"/>',
        'split'     => '<path d="M16 3.5h4.5V8"/><path d="M8 3.5H3.5V8"/><path d="M12 21v-8.3a4 4 0 0 0-1.2-2.9L3.5 3.5"/><path d="m14.5 9.5 6-6"/>',
        'sitemap'   => '<rect x="9" y="3.5" width="6" height="5" rx="1"/><rect x="3" y="15.5" width="6" height="5" rx="1"/><rect x="15" y="15.5" width="6" height="5" rx="1"/><path d="M12 8.5V12M6 15.5V12h12v3.5"/>',
        'lock'      => '<rect x="5" y="10.5" width="14" height="10" rx="1.5"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/><path d="M12 14.5V17"/>',
        'tm'        => '<circle cx="12" cy="12" r="8.5"/><path d="M9.5 16.5v-9h3.2a2.6 2.6 0 0 1 0 5.2H9.5"/><path d="m12.8 12.7 2.7 3.8"/>',
        'eye'       => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
        'cube'      => '<path d="M12 3 4 7.5v9L12 21l8-4.5v-9L12 3Z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
        'bulb'      => '<path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-3.6 10.8c.7.6 1.1 1.4 1.1 2.2h5c0-.8.4-1.6 1.1-2.2A6 6 0 0 0 12 3Z"/>',
        'landmark'  => '<path d="M3 9.5 12 4l9 5.5"/><path d="M5 9.5v8M9.5 9.5v8M14.5 9.5v8M19 9.5v8"/><path d="M3 20.5h18"/>',
        'scale'     => '<path d="M12 4v16.5M7.5 20.5h9M5 7h14"/><path d="M5 7 2.5 13a2.5 2.5 0 0 0 5 0L5 7Z"/><path d="m19 7-2.5 6a2.5 2.5 0 0 0 5 0L19 7Z"/>',
        'gavel'     => '<path d="m14.5 12.5-8 8a2.1 2.1 0 1 1-3-3l8-8"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/>',
        'briefcase' => '<rect x="3.5" y="7" width="17" height="12.5" rx="1.5"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/><path d="M3.5 12.5h17"/>',
        'vault'     => '<rect x="3.5" y="4.5" width="17" height="15" rx="1.5"/><circle cx="12" cy="12" r="3.5"/><path d="M12 8.5v1M12 14.5v1M8.5 12h1M14.5 12h1"/>',
    ];
}

function ug_price_svg(string $name, int $size = 28): string
{
    $icons = ug_price_icons();
    $inner = $icons[$name] ?? $icons['doc'];
    return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $inner . '</svg>';
}

/** Текст шорткода без разметки wpautop: <p>, </p>, <br> → переводы строк, сущности раскрыты. */
function ug_price_plain(string $text): string
{
    $text = (string) preg_replace('~<br\s*/?>|</?p(?:\s[^>]*)?>~iu', "\n", $text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return str_replace("\r", '', $text);
}

/** Все вхождения шорткода $tag в $text: [[атрибуты, содержимое], …]. */
function ug_price_tags(string $text, string $tag): array
{
    $out = [];
    if (preg_match_all('/' . get_shortcode_regex([$tag]) . '/su', $text, $m, PREG_SET_ORDER)) {
        foreach ($m as $x) {
            if ($x[1] === '[' && $x[6] === ']') {
                continue; // [[экранированный]]
            }
            $atts = shortcode_parse_atts($x[3]);
            $out[] = [is_array($atts) ? $atts : [], $x[5] ?? ''];
        }
    }
    return $out;
}

/** Строки деталей услуги → блоки: text / list / prices / note. */
function ug_price_lines(string $text): array
{
    $blocks = [];
    foreach (preg_split('/\n/u', ug_price_plain($text)) ?: [] as $line) {
        $line = trim(wp_strip_all_tags($line));
        if ($line === '') {
            continue;
        }
        if (preg_match('/^[-–—•]\s*(.+)$/u', $line, $m)) {
            $type = 'list';
            $val  = rtrim($m[1], ' ;,.');
            $val  = mb_strtoupper(mb_substr($val, 0, 1)) . mb_substr($val, 1);
        } elseif (str_starts_with($line, '*')) {
            $type = 'note';
            $val  = $line;
        } elseif (str_contains($line, '|')) {
            $type = 'prices';
            [$a, $b] = array_map('trim', explode('|', $line, 2));
            $val = [$a, $b];
        } else {
            $type = 'text';
            $val  = $line;
        }
        $last = count($blocks) - 1;
        if ($last >= 0 && $blocks[$last]['type'] === $type && $type !== 'text') {
            $blocks[$last]['items'][] = $val;
        } else {
            $blocks[] = ['type' => $type, 'items' => [$val]];
        }
    }
    return $blocks;
}

/** Строка поиска: нижний регистр, ё → е, одни пробелы. */
function ug_price_norm(string $s): string
{
    $s = mb_strtolower(wp_strip_all_tags($s));
    $s = str_replace('ё', 'е', $s);
    return trim((string) preg_replace('/\s+/u', ' ', $s));
}

/**
 * Разбор содержимого [ug_price_page]: направления, их услуги (с номером
 * страницы направления), акции и «Как формируется стоимость».
 */
function ug_price_parse(string $content): array
{
    $content = ug_price_plain($content);
    $dirs = [];
    foreach (ug_price_tags($content, 'ug_price_dir') as [$a, $inner]) {
        $id = sanitize_key((string) ($a['id'] ?? ''));
        if ($id === '') {
            $id = 'dir' . (count($dirs) + 1);
        }
        $items = [];
        foreach (ug_price_tags($inner, 'ug_price') as [$ia, $body]) {
            $title = trim((string) ($ia['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $lines = ug_price_lines($body);
            $text  = '';
            foreach ($lines as $b) {
                foreach ($b['items'] as $v) {
                    $text .= ' ' . (is_array($v) ? implode(' ', $v) : $v);
                }
            }
            $items[] = [
                'title'   => $title,
                'sub'     => trim((string) ($ia['sub'] ?? '')),
                'price'   => trim((string) ($ia['price'] ?? '')),
                'note'    => trim((string) ($ia['note'] ?? '')),
                'inst'    => in_array(strtolower(trim((string) ($ia['installment'] ?? ''))), ['1', 'yes', 'true', 'да'], true),
                'group'   => trim((string) ($ia['group'] ?? '')),
                'icon'    => sanitize_key((string) ($ia['icon'] ?? 'doc')),
                'url'     => trim((string) ($ia['url'] ?? '')),
                'lines'   => $lines,
                'search'  => ug_price_norm(($ia['title'] ?? '') . ' ' . ($ia['sub'] ?? '') . ' ' . $text),
            ];
        }
        foreach ($items as $n => &$it) {
            $it['page'] = intdiv($n, UG_PRICE_PER_PAGE) + 1;
        }
        unset($it);

        $promo = null;
        foreach (ug_price_tags($inner, 'ug_price_promo') as [$pa]) {
            $promo = [
                'akciya' => (int) ($pa['akciya'] ?? 0),
                'title'  => trim((string) ($pa['title'] ?? '')),
                'text'   => trim((string) ($pa['text'] ?? '')),
            ];
        }
        $how = [];
        foreach (ug_price_tags($inner, 'ug_price_how') as [, $body]) {
            foreach (preg_split('/\n/u', $body) ?: [] as $line) {
                $line = trim(wp_strip_all_tags($line));
                if ($line !== '') {
                    $how[] = array_map('trim', explode('|', $line, 2)) + [1 => ''];
                }
            }
        }
        $dirs[] = [
            'id'      => $id,
            'name'    => trim((string) ($a['name'] ?? ($a['title'] ?? $id))),
            'title'   => trim((string) ($a['title'] ?? ($a['name'] ?? $id))),
            'url'     => trim((string) ($a['url'] ?? '')),
            'anchor'  => sanitize_html_class((string) ($a['anchor'] ?? '')),
            'lead'    => trim((string) ($a['lead'] ?? '')),
            'example' => trim((string) ($a['example'] ?? '')),
            'items'   => $items,
            'promo'   => $promo,
            'how'     => $how,
        ];
    }
    ug_price_label_items($dirs);
    return $dirs;
}

/**
 * Подпись услуги в списке формы: название, а у повторяющихся названий
 * («Ведение учёта для ООО» ×2) — ещё и подпись строки.
 */
function ug_price_label_items(array &$dirs): void
{
    $count = [];
    foreach ($dirs as $d) {
        foreach ($d['items'] as $it) {
            $key = $it['title'];
            $count[$key][$it['sub']] = true;
        }
    }
    foreach ($dirs as &$d) {
        foreach ($d['items'] as &$it) {
            $label = $it['title'];
            if (count($count[$it['title']]) > 1 && $it['sub'] !== '') {
                $label .= ' — ' . mb_strtolower(mb_substr($it['sub'], 0, 1)) . mb_substr($it['sub'], 1);
            }
            $it['option'] = $label;
        }
        unset($it);
    }
    unset($d);
}

/** Варианты поля «Какая услуга нужна?» — из текущего прайса page 12. */
function ug_price_form_options(): array
{
    static $opts = null;
    if ($opts !== null) {
        return $opts;
    }
    $opts = [];
    $content = (string) get_post_field('post_content', UG_PRICE_PAGE_ID, 'raw');
    foreach (ug_price_tags($content, 'ug_price_page') as [, $inner]) {
        foreach (ug_price_parse($inner) as $d) {
            foreach ($d['items'] as $it) {
                $opts[$it['option']] = true;
            }
        }
    }
    $opts = array_keys($opts);
    return $opts;
}

// Список услуг в форме — из прайса. Тот же фильтр работает и при отправке
// (CF7 сверяет значение со списком), так что список и проверка не расходятся.
add_filter('wpcf7_form_tag', static function ($tag) {
    if (!is_array($tag) || ($tag['name'] ?? '') !== 'price-service' || ($tag['basetype'] ?? '') !== 'select') {
        return $tag;
    }
    $values = array_merge([UG_PRICE_OPT_PLACEHOLDER], ug_price_form_options(), [UG_PRICE_OPT_HELP, UG_PRICE_OPT_OTHER]);
    $tag['raw_values'] = $values;
    $tag['values']     = $values;
    $tag['labels']     = $values;
    if (class_exists('WPCF7_Pipes')) {
        $tag['pipes'] = new WPCF7_Pipes($values);
    }
    return $tag;
});

// «Телефон или email»: e-mail или номер не короче 10 цифр.
add_filter('wpcf7_validate_text*', static function ($result, $tag) {
    if (($tag->name ?? '') !== 'price-contact') {
        return $result;
    }
    $v = trim((string) wp_unslash($_POST['price-contact'] ?? ''));
    $digits = (string) preg_replace('/\D+/', '', $v);
    if ($v !== '' && !is_email($v) && strlen($digits) < 10) {
        $result->invalidate($tag, 'Укажите телефон или e-mail, чтобы мы могли связаться');
    }
    return $result;
}, 20, 2);

function ug_price_render_details(array $it, string $id): string
{
    $html = '';
    $has_list = false;
    foreach ($it['lines'] as $b) {
        switch ($b['type']) {
            case 'list':
                $has_list = true;
                $long = max(array_map('mb_strlen', $b['items'])) > 60 || count($b['items']) < 4;
                $html .= '<ul class="ugp-inc__list' . ($long ? ' is-one' : '') . '">';
                foreach ($b['items'] as $v) {
                    $html .= '<li>' . esc_html($v) . '</li>';
                }
                $html .= '</ul>';
                break;
            case 'prices':
                $html .= '<table class="ugp-inc__prices"><tbody>';
                foreach ($b['items'] as [$a, $p]) {
                    $html .= '<tr><td>' . esc_html($a) . '</td><td>' . esc_html($p) . '</td></tr>';
                }
                $html .= '</tbody></table>';
                break;
            case 'note':
                foreach ($b['items'] as $v) {
                    $html .= '<p class="ugp-inc__fn">' . esc_html($v) . '</p>';
                }
                break;
            default:
                $html .= '<p class="ugp-inc__text">' . esc_html($b['items'][0]) . '</p>';
        }
    }
    $head = $has_list ? 'Что входит' : 'Подробнее';
    $link = $it['url'] !== ''
        ? '<a class="ugp-inc__link" href="' . esc_url($it['url']) . '">Подробнее об услуге<span class="ugp-arr" aria-hidden="true">→</span></a>'
        : '';
    return '<div class="ugp-inc" id="' . esc_attr($id) . '"><p class="ugp-inc__h">' . $head . '</p>' . $html . $link . '</div>';
}

function ug_price_render_row(array $it, string $dir, string $id): string
{
    $title = esc_html($it['title']);
    $sub   = $it['sub'] !== '' ? '<p class="ugp-row__cap">' . esc_html($it['sub']) . '</p>' : '';
    $more  = '';
    $inc   = '';
    if ($it['lines']) {
        $more = '<button type="button" class="ugp-row__more" aria-expanded="false" aria-controls="' . esc_attr($id) . '">Подробнее<span class="ugp-arr" aria-hidden="true">↓</span></button>';
        $inc  = ug_price_render_details($it, $id);
    } elseif ($it['url'] !== '') {
        $more = '<a class="ugp-row__more ugp-row__more--link" href="' . esc_url($it['url']) . '">Подробнее<span class="ugp-arr" aria-hidden="true">→</span></a>';
    }
    $note = $it['note'] !== '' ? '<small class="ugp-row__note">' . esc_html($it['note']) . '</small>' : '';
    $inst = $it['inst']
        ? '<span class="ugp-inst" tabindex="0">Оплата частями<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M9.6 9.3a2.5 2.5 0 0 1 4.8.9c0 1.7-2.4 2.2-2.4 3.6M12 17v.2"/></svg><span class="ugp-inst__tip" role="tooltip">' . esc_html(UG_PRICE_INSTALLMENT_TIP) . '</span></span>'
        : '';
    $price  = esc_html($it['price']);
    $icon   = ug_price_svg($it['icon']);
    $option = esc_attr($it['option']);
    $search = esc_attr($it['search']);
    $page   = (int) $it['page'];
    $cur    = $page === 1 ? ' is-cur' : '';

    return <<<HTML
        <div class="ugp-row{$cur}" data-p="{$page}" data-search="{$search}">
            <div class="ugp-row__main">
                <span class="ugp-row__ic">{$icon}</span>
                <div class="ugp-row__info">
                    <h3 class="ugp-row__t">{$title}</h3>
                    {$sub}
                    {$more}
                </div>
                <div class="ugp-row__price"><span class="ugp-row__sum">{$price}</span>{$note}{$inst}</div>
                <a class="ugp-row__go" href="#ugp-form" data-option="{$option}" data-dir="{$dir}">Рассчитать<span class="ugp-arr" aria-hidden="true">→</span></a>
            </div>
            {$inc}
        </div>
        HTML;
}

function ug_price_render_dir(array $d, bool $active): string
{
    $id    = esc_attr($d['id']);
    $title = esc_html($d['title']);
    $h2    = $d['url'] !== '' ? '<a href="' . esc_url($d['url']) . '">' . $title . '</a>' : $title;
    $anchor = $d['anchor'] !== '' ? '<span class="ugp-anchor" id="' . esc_attr($d['anchor']) . '"></span>' : '';
    $lead  = $d['lead'] !== '' ? '<p class="ugp-dir__lead">' . esc_html($d['lead']) . '</p>' : '';

    $promo = '';
    if ($d['promo'] && $d['promo']['title'] !== '') {
        $p    = $d['promo'];
        $link = '';
        $date = '';
        if ($p['akciya'] && get_post_status($p['akciya']) === 'publish') {
            $link = '<a class="ugp-promo__link" href="' . esc_url(get_permalink($p['akciya'])) . '">Условия<span class="ugp-arr" aria-hidden="true">→</span></a>';
            $date = function_exists('ug_akcii_deadline') ? ' <span class="ugp-promo__date">До ' . esc_html(ug_akcii_deadline()) . '</span>' : '';
        }
        $promo = '<div class="ugp-promo"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3.5" y="8" width="17" height="4" rx="1"/><path d="M5 12v8h14v-8M12 8v12"/><path d="M12 8c-1.5-3.5-5.5-4-5.5-1.5S10 8 12 8Zm0 0c1.5-3.5 5.5-4 5.5-1.5S14 8 12 8Z"/></svg>'
            . '<span class="ugp-promo__text"><b>' . esc_html($p['title']) . '</b> ' . esc_html($p['text']) . $date . '</span>' . $link . '</div>';
    }

    $rows  = '';
    $group = null;
    $gpage = 0;
    foreach ($d['items'] as $n => $it) {
        $page = (int) $it['page'];
        if ($it['group'] !== '' && $it['group'] !== $group) {
            $rows .= '<p class="ugp-grp' . ($page === 1 ? ' is-cur' : '') . '" data-p="' . $page . '">' . esc_html($it['group']) . '</p>';
        } elseif ($it['group'] !== '' && $page !== $gpage) {
            // группа перешла на следующую страницу — заголовок повторяется (только с JS)
            $rows .= '<p class="ugp-grp ugp-grp--cont" data-p="' . $page . '" aria-hidden="true">' . esc_html($it['group']) . '</p>';
        }
        $group = $it['group'];
        $gpage = $page;
        $rows .= ug_price_render_row($it, $d['id'], 'ugp-' . $d['id'] . '-' . ($n + 1));
    }
    $pages = (int) ceil(count($d['items']) / UG_PRICE_PER_PAGE);

    $how = '';
    if ($d['how']) {
        $how .= '<div class="ugp-how"><h3 class="ugp-how__title">Как формируется стоимость</h3><ol class="ugp-how__grid">';
        foreach ($d['how'] as [$b, $s]) {
            $how .= '<li class="ugp-how__it"><b>' . esc_html($b) . '</b><span>' . esc_html($s) . '</span></li>';
        }
        $how .= '</ol><p class="ugp-how__note">Нужна отдельная консультация или разовая задача? <a href="#ugp-form">Опишите её в заявке<span class="ugp-arr" aria-hidden="true">→</span></a></p></div>';
    }

    $cls     = 'ugp-dir' . ($active ? ' is-active' : '');
    $example = esc_attr($d['example']);
    $name    = esc_attr($d['name']);
    return <<<HTML
        <section class="{$cls}" id="{$id}" data-dir="{$id}" data-name="{$name}" data-pages="{$pages}" data-example="{$example}">
            {$anchor}
            <h2 class="ugp-dir__title">{$h2}</h2>
            {$lead}
            {$promo}
            <div class="ugp-tbl">
                <div class="ugp-tbl__h" aria-hidden="true"><span></span><span>Услуга</span><span>Стоимость</span><span></span></div>
                {$rows}
                <nav class="ugp-pager" aria-label="Страницы направления «{$name}»"></nav>
                <p class="ugp-notfound">Не нашли свою услугу? <a href="#ugp-form">Опишите задачу — рассчитаем<span class="ugp-arr" aria-hidden="true">→</span></a></p>
            </div>
            {$how}
        </section>
        HTML;
}

add_shortcode('ug_price_page', static function ($atts, $content = ''): string {
    $atts = shortcode_atts(['title' => 'Стоимость услуг', 'lead' => '', 'form' => '0'], (array) $atts);
    $dirs = ug_price_parse((string) $content);
    if (!$dirs) {
        return '';
    }

    $sep    = ' <span class="ugp-crumbs__sep">›</span> ';
    $crumbs = '<a href="' . esc_url(home_url('/')) . '">Главная</a>' . $sep . esc_html(get_the_title() ?: 'Стоимость');

    $nav = '';
    $main = '';
    foreach ($dirs as $i => $d) {
        $on    = $i === 0;
        $nav  .= '<li><a class="ugp-nav__item' . ($on ? ' is-active' : '') . '" href="#' . esc_attr($d['id']) . '" data-dir="' . esc_attr($d['id']) . '"'
            . ($d['anchor'] !== '' ? ' data-alias="' . esc_attr($d['anchor']) . '"' : '') . '>' . str_replace(' и ', ' и&nbsp;', esc_html($d['name'])) . '</a></li>';
        $main .= ug_price_render_dir($d, $on);
    }

    $form_id = (int) $atts['form'];
    $has_form = $form_id && get_post_type($form_id) === 'wpcf7_contact_form';
    $form = $has_form
        ? do_shortcode('[contact-form-7 id="' . $form_id . '" html_class="ugp-cf7"]')
        : '<p>Позвоните нам — рассчитаем стоимость по телефону.</p>';
    // Та же форма поп-апом (Flatsome lightbox, как «Задать вопрос» в блоге):
    // её открывают «Рассчитать», «Помочь с выбором», «Не нашли свою услугу?».
    // Ссылка-триггер нужна Flatsome: он вешает открытие на a[href="#ugp-calc"],
    // а JS страницы щёлкает по ней, подставив услугу.
    $popup = $has_form
        ? do_shortcode('[lightbox id="ugp-calc" width="480px" padding="0px" class="ugp-pop"]'
            . '<div class="ugp-pop__box ugp-cf7box"><p class="ugp-pop__title">Получите расчёт</p>'
            . '<p class="ugp-pop__text">Уточним задачу и подготовим предложение для вашей компании.</p>'
            . '[contact-form-7 id="' . $form_id . '" html_class="ugp-cf7"]</div>[/lightbox]')
            . '<a class="ugp-pop__open" href="#ugp-calc" hidden aria-hidden="true" tabindex="-1"></a>'
        : '';

    $h1   = esc_html($atts['title']);
    $lead = $atts['lead'] !== '' ? '<p class="ugp-head__lead">' . esc_html($atts['lead']) . '</p>' : '';

    return <<<HTML
        <div class="ugp" id="ugp">
            <script>document.getElementById('ugp').classList.add('is-js');</script>
            <div class="ugp__inner">
                <div class="ugp-crumbs breadcrumbs">{$crumbs}</div>
                <header class="ugp-head">
                    <div class="ugp-head__text">
                        <h1 class="ugp-head__title">{$h1}</h1>
                        {$lead}
                    </div>
                    <form class="ugp-search" role="search" method="get" action="/">
                        <label class="screen-reader-text" for="ugp-q">Найти услугу</label>
                        <input class="ugp-search__input" id="ugp-q" type="search" name="s" placeholder="Найти услугу" autocomplete="off">
                        <span class="ugp-search__ic" aria-hidden="true"></span>
                        <button class="ugp-search__clear" type="button" aria-label="Очистить поиск" hidden></button>
                    </form>
                </header>

                <div class="ugp-layout">
                    <aside class="ugp-side">
                        <nav class="ugp-nav" aria-label="Направления">
                            <p class="ugp-nav__title">Направления</p>
                            <ul class="ugp-nav__list">{$nav}</ul>
                        </nav>
                        <div class="ugp-help">
                            <p class="ugp-help__text">Не знаете, что выбрать?</p>
                            <a class="ugp-help__link" href="#ugp-form">Помочь с выбором<span class="ugp-arr" aria-hidden="true">→</span></a>
                        </div>
                    </aside>
                    <div class="ugp-main" aria-live="polite">
                        <p class="ugp-found" hidden></p>
                        {$main}
                    </div>
                </div>

                <section class="ugp-form" id="ugp-form">
                    <div class="ugp-form__about">
                        <h2 class="ugp-form__title">Получите расчёт для вашей компании</h2>
                        <p class="ugp-form__text">Расскажите, какая помощь нужна. Уточним задачу и подготовим предложение.</p>
                        <p class="ugp-form__tel contacts-text"><a href="tel:+73833731717">+7 (383) 373-17-17</a></p>
                    </div>
                    <div class="ugp-form__box ugp-cf7box">{$form}</div>
                </section>
            </div>
            {$popup}
        </div>
        HTML;
});

// Вложенные шорткоды сами по себе ничего не выводят — их разбирает [ug_price_page].
foreach (['ug_price_dir', 'ug_price', 'ug_price_promo', 'ug_price_how'] as $ug_tag) {
    add_shortcode($ug_tag, static fn(): string => '');
}
unset($ug_tag);

function ug_is_price_page(): bool
{
    if (!is_singular('page')) {
        return false;
    }
    $post = get_queried_object();
    return $post instanceof WP_Post && has_shortcode($post->post_content, 'ug_price_page');
}

// wpautop не трогает прайс: строки деталей разбираются по переводам строк.
add_filter('the_content', static function ($content) {
    if (is_string($content) && str_contains($content, '[ug_price_page')) {
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
    if (ug_is_price_page()) {
        ug_head_style('assets/css/pages/price-list.css', false);
    }
});
add_action('wp_enqueue_scripts', static function (): void {
    if (ug_is_price_page()) {
        ug_enqueue_script('assets/js/pages/price-list.js');
    }
});
