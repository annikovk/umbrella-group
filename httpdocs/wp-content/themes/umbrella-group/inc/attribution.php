<?php
/**
 * UTM-атрибуция заявок: серверная часть.
 *
 * Источник визита считает браузер (assets/js/attribution.js): кладёт его в
 * cookie ug_src и в скрытые поля omatraffic / omacid форм CF7. Здесь — только:
 *  - [omatraffic] / [omacid] в шаблоне формы → пустой <input type="hidden">
 *    (раньше — do_shortcode по всей форме и строка атрибуции прямо в HTML:
 *    XSS через utm_*, а кэш раздавал всем атрибуцию первого посетителя);
 *  - остальные шорткоды в форме — только из белого списка (сейчас
 *    wbcr_php_snippet: заголовки 22 форм — сниппеты insert-php);
 *  - при отправке оба поля очищаются, а если JS не отработал (поля нет или оно
 *    пустое) — берутся из cookie ug_src / _gaexp. Шаблоны писем ссылаются на
 *    [omatraffic] / [omacid], CF7 подставляет их как поля формы — строка
 *    «Источник - …, Канал - …» в письме сохраняется.
 *
 * Старую cookie source_cookie (PHP serialize) не читаем: unserialize данных из
 * cookie — object injection. Вернувшийся посетитель один раз получит атрибуцию
 * заново (спека 1, §7.1).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const UG_ATTRIBUTION_FIELDS = ['omatraffic', 'omacid'];

add_action('wp_enqueue_scripts', static function (): void {
    $rel = 'assets/js/attribution.js';
    wp_enqueue_script(
        'ug-attribution',
        get_theme_file_uri($rel),
        [],
        (string) filemtime(get_theme_file_path($rel)),
        ['in_footer' => true, 'strategy' => 'defer']
    );
});

/**
 * Шорткоды, которые разрешено исполнять внутри шаблона формы CF7.
 * WS-8 (сниппеты → тема) добавляет сюда свои ug_* через фильтр.
 *
 * @return string[]
 */
function ug_cf7_form_shortcodes(): array
{
    return (array) apply_filters('ug_cf7_form_shortcodes', ['wbcr_php_snippet']);
}

add_filter('wpcf7_form_elements', static function ($html) {
    $html = (string) $html;

    $html = (string) preg_replace_callback(
        '/\[(omatraffic|omacid)\]/',
        static fn(array $m): string => '<input type="hidden" name="' . $m[1] . '" value="">',
        $html
    );

    $allowed = array_values(array_filter(
        ug_cf7_form_shortcodes(),
        static fn($tag): bool => is_string($tag) && shortcode_exists($tag)
    ));
    if ($allowed && str_contains($html, '[')) {
        $html = (string) preg_replace_callback(
            '/' . get_shortcode_regex($allowed) . '/',
            'do_shortcode_tag',
            $html
        );
    }

    return $html;
});

/**
 * Строка атрибуции из cookie ug_src — запасной путь, если поле не пришло из JS.
 * Формат тот же, что в attribution.js.
 */
function ug_attribution_from_cookie(): string
{
    $raw = isset($_COOKIE['ug_src']) ? wp_unslash((string) $_COOKIE['ug_src']) : '';
    $src = $raw !== '' ? json_decode($raw, true) : null;
    if (!is_array($src) || empty($src['medium'])) {
        $src = ['source' => 'none', 'medium' => 'direct'];
    }

    $get = static function (string $key) use ($src): string {
        return isset($src[$key]) && is_scalar($src[$key]) ? (string) $src[$key] : '';
    };
    $has = static fn(string $v): bool => $v !== '' && $v !== '0';

    $out = 'Источник - ' . $get('source') . ', Канал - ' . $get('medium');
    if ($has($get('campaign'))) {
        $out .= ', Кампания - ' . $get('campaign');
    }
    if ($has($get('term'))) {
        $out .= ', Объявление - ' . $get('term');
    }
    if ($has($get('content'))) {
        $out .= ', Ключевое слово - ' . $get('content');
    }

    return $out;
}

add_filter('wpcf7_posted_data', static function ($posted_data) {
    if (!is_array($posted_data)) {
        return $posted_data;
    }

    foreach (UG_ATTRIBUTION_FIELDS as $name) {
        $value = $posted_data[$name] ?? '';
        $posted_data[$name] = is_scalar($value) ? sanitize_text_field((string) $value) : '';
    }

    if ($posted_data['omatraffic'] === '') {
        $posted_data['omatraffic'] = sanitize_text_field(ug_attribution_from_cookie());
    }
    if ($posted_data['omacid'] === '' && isset($_COOKIE['_gaexp'])) {
        $posted_data['omacid'] = sanitize_text_field(wp_unslash((string) $_COOKIE['_gaexp']));
    }

    return $posted_data;
});
