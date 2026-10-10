<?php
/**
 * Бывшие PHP-сниппеты insert-php (Woody) — шорткоды темы ug_* (спека 1, WS-8).
 *
 * Раньше 25 сниппетов типа wbcr-snippets жили кодом в БД и исполнялись eval'ом
 * из шорткода [wbcr_php_snippet id=N]. Теперь код каждого — в
 * inc/snippets/<id>-<slug>.php, а контент ссылается на [ug_<slug>] (миграция
 * tools/migrations/02-snippets-to-theme.php). Вывод — тот же: функция печатает,
 * обёртка собирает буфер, как WINP_SnippetShortcodePhp::html() под ob_start().
 * Шорткоды Flatsome в выводе ([row], [col] …) раскрывает, как и раньше,
 * следующий проход do_shortcode у вызывающего.
 *
 * Атрибуты проходят ту же обработку, что в плагине
 * (WINP_SnippetShortcode::filterAttributes): urldecode, вырезание <script>,
 * on*-атрибутов и javascript:/vbscript:, FILTER_SANITIZE_SPECIAL_CHARS и
 * addslashes; при заданном белом списке (snippet_tags сниппета) прочие имена
 * отбрасываются. Переменная «задана ли» у сниппета = isset($a['имя']).
 *
 * Пока плагин стоит, [wbcr_php_snippet] продолжает работать (ревизии, откат):
 * тема выкатывается раньше миграции контента, и оба шорткода живут вместе.
 */

declare(strict_types=1);

/**
 * Атрибуты шорткода — как их видел код сниппета после filterAttributes().
 *
 * @param mixed    $atts    то, что WordPress передал в обработчик ('' без атрибутов)
 * @param string[] $allowed белый список имён (пусто — любые валидные имена)
 * @return array<string, string>
 */
function ug_snippet_atts($atts, array $allowed = []): array
{
    $out = [];
    foreach (is_array($atts) ? $atts : [] as $name => $value) {
        $name = (string) $name;
        $is_allowed = in_array($name, ['id', 'title'], true);
        $valid_name = preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $name) === 1;
        if (!$is_allowed && (($allowed && !in_array($name, $allowed, true)) || !$valid_name)) {
            continue;
        }
        $value = urldecode((string) $value);
        $value = (string) preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
        $value = (string) preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $value);
        $value = (string) preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $value);
        $value = (string) preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $value);
        $value = (string) preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $value);
        $value = (string) filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        $out[$name] = (string) filter_var($value, FILTER_SANITIZE_ADD_SLASHES);
    }
    return $out;
}

/**
 * Регистрирует шорткод бывшего сниппета: $render печатает, вывод — из буфера.
 *
 * @param callable(array<string, string>): void $render
 * @param string[] $allowed белый список атрибутов (snippet_tags сниппета)
 */
function ug_snippet_shortcode(string $tag, callable $render, array $allowed = []): void
{
    add_shortcode($tag, static function ($atts) use ($render, $allowed): string {
        ob_start();
        $render(ug_snippet_atts($atts, $allowed));
        return (string) ob_get_clean();
    });
}

/**
 * Сниппеты, которые стоят в шаблонах форм CF7 (заголовки и подзаголовки форм):
 * их разрешено исполнять внутри формы (белый список inc/attribution.php).
 */
const UG_SNIPPET_CF7_SHORTCODES = [
    'ug_form_title',            // 8122
    'ug_callback_subtitle',     // 8152
    'ug_footer_form_subtitle',  // 9110
    'ug_seminar_title',         // 29516
    'ug_seminar_subtitle',      // 29522
    'ug_docs_package_title',    // 29840
    'ug_docs_package_text',     // 29843
];

add_filter('ug_cf7_form_shortcodes', static fn(array $tags): array => array_merge($tags, UG_SNIPPET_CF7_SHORTCODES));

// Порядок — по id сниппета; имя файла = <id>-<slug>, шорткод = ug_<slug>.
require __DIR__ . '/snippets/373-clients-carousel.php';
require __DIR__ . '/snippets/701-menu.php';
require __DIR__ . '/snippets/772-title-breadcrumbs.php';
require __DIR__ . '/snippets/786-service-tiles.php';
require __DIR__ . '/snippets/1031-client-tiles.php';
require __DIR__ . '/snippets/1870-feedback-page.php';
require __DIR__ . '/snippets/1961-blog-tiles.php';
require __DIR__ . '/snippets/1997-blog-side-panel.php';
require __DIR__ . '/snippets/2906-child-pages.php';
require __DIR__ . '/snippets/5645-seo-logo.php';
require __DIR__ . '/snippets/7300-learning-menu.php';
require __DIR__ . '/snippets/8122-form-title.php';
require __DIR__ . '/snippets/8152-callback-subtitle.php';
require __DIR__ . '/snippets/9110-footer-form-subtitle.php';
require __DIR__ . '/snippets/9123-footer-form-title.php';
require __DIR__ . '/snippets/9309-rating-badge.php';
require __DIR__ . '/snippets/10644-download-memo.php';
require __DIR__ . '/snippets/14263-footer-bottom.php';
require __DIR__ . '/snippets/24189-download-program.php';
require __DIR__ . '/snippets/29221-service-tiles-ip.php';
require __DIR__ . '/snippets/29516-seminar-title.php';
require __DIR__ . '/snippets/29522-seminar-subtitle.php';
require __DIR__ . '/snippets/29840-docs-package-title.php';
require __DIR__ . '/snippets/29843-docs-package-text.php';
require __DIR__ . '/snippets/30355-service-tiles-tax.php';
