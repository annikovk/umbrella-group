<?php
/**
 * Правки родительской Flatsome, которые до обновления на 3.20.11 вносились
 * руками прямо в её файлы на сервере и стирались бы каждым обновлением темы.
 * Сверено с чистой 3.13.4; остальное, что было правлено в родителе, уже
 * перекрыто дочерней темой (footer.php, header.php, аккордеон —
 * template-parts/shortcodes/accordion.php) или нигде не используется
 * ([team_member] — только в ревизиях, плашка #cookie_note из index.php —
 * её заменила inc/cookie-notice.php).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

// CSS из flatsome.css (assets/css/flatsome-legacy.css) — сразу после
// flatsome-main и до style.css дочерней темы (flatsome-style), где он и стоял
// в каскаде: flatsome-style получает его в зависимости, поэтому печатается
// после. Flatsome ставит оба хэндла на wp_enqueue_scripts:100.
add_action('wp_enqueue_scripts', function (): void {
    $rel = 'assets/css/flatsome-legacy.css';
    wp_enqueue_style('ug-flatsome-legacy', get_theme_file_uri($rel), ['flatsome-main'], ug_asset_ver($rel));
    $style = wp_styles()->query('flatsome-style');
    if ($style) {
        $style->deps[] = 'ug-flatsome-legacy';
    }
}, 101);

// Отрывок (excerpt) над страницей не выводится: в flatsome/inc/structure/
// structure-pages.php the_excerpt() был закомментирован, а отрывки заполнены
// у 72 страниц — для SEO-описаний и плиток, не для показа.
add_action('after_setup_theme', function (): void {
    remove_action('flatsome_before_page', 'flatsome_page_top_excerpt', 20);
});

// Второй проход шорткодов по контенту. До 3.20 flatsome_contentfix() на
// the_content:10 сама звала do_shortcode() — после wpautop, до штатного прохода
// ядра на :11, — и контент, сниппеты (inc/snippets.php) и шаблоны темы, которые
// печатают [row]/[col]/[ux_slider] из шорткода, на это рассчитывают: без
// прохода такие шорткоды выводятся сырым текстом. Ставим его там же — сразу
// за flatsome_contentfix на :10.
add_action('after_setup_theme', function (): void {
    add_filter('the_content', 'do_shortcode', 10);
});

/**
 * Шрифты из Customizer (type_texts, type_nav, type_alt) — как их видел браузер
 * при 3.13. Та версия печатала в custom-css семейство одной строкой в кавычках
 * с &quot; внутри и вес intval('regular') = 0. Браузер такие объявления
 * отбрасывал: текст и меню шли запасным sans-serif, вес меню — из flatsome.css
 * (у нижнего меню 700), у .alt-font — без курсива. 3.20 печатает их правильно
 * (Georgia, 400, italic!important) и этим меняет вид сайта. Возвращаем прежний
 * результат; остальной custom-css не трогаем.
 */
function ug_flatsome_legacy_fonts(string $css): string
{
    $nav = '\.nav > li > a|\.mobile-sidebar-levels-2 \.nav > li > ul > li > a';
    return (string) preg_replace(
        [
            '~body\s*\{\s*font-family:[^}]*\}~',
            '~body\s*\{\s*font-weight:[^}]*\}~',
            '~(' . $nav . ')\s*\{\s*font-family:[^}]*\}~',
            '~(?:' . $nav . ')\s*,\s*(?:' . $nav . ')\s*\{\s*font-weight:[^}]*\}~',
            '~\.alt-font\s*\{\s*font-weight:[^}]*\}~',
        ],
        ['body{font-family: sans-serif}', '', '$1 {font-family: sans-serif;}', '', ''],
        $css
    );
}

add_action('after_setup_theme', function (): void {
    if (remove_action('wp_head', 'flatsome_custom_css', 100)) {
        add_action('wp_head', function (): void {
            ob_start();
            flatsome_custom_css();
            echo ug_flatsome_legacy_fonts((string) ob_get_clean());
        }, 100);
    }
});
