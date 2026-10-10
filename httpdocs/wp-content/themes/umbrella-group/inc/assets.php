<?php
/**
 * Подключение ассетов темы (спека 1, §3.2).
 *
 *     ug_enqueue_style('assets/css/blocks/cases.css');      // хэндл ug-blocks-cases
 *     ug_enqueue_script('assets/js/blocks/cases.js', ['jquery']);
 *
 * Хэндл выводится из пути (ug_style_handle), регистрация ленивая — при первом
 * вызове, версия — filemtime файла. Реестр руками — только у библиотек
 * (ug-main, ug-swiper, ug-swiper-14, ug-fancybox) в ug_register_libraries().
 *
 * Где печатается CSS:
 *  - ug-base (assets/css/base.css) — в <head> до Flatsome, где раньше стояли
 *    fonts-configuration.css, form-custom.css и инлайн ul.tabs из header.php.
 *  - ug-main (assets/css/main.css) — в <head> на wp_head:101, сразу после
 *    Flatsome custom-css (wp_head:100), и ещё раз той же ссылкой в конце
 *    <body> перед CSS блоков (якорь каскада, wp_footer:19 — ниже). Раньше этот
 *    CSS стоял <style>-блоком в самом конце <body> (css/css_collector.php,
 *    удалён) и побеждал всё, что раньше по документу.
 *  - CSS страниц и первого экрана (ug_head_style) — там же, после ug-main.
 *  - CSS блоков (ug_enqueue_style из шорткодов) — late styles ядра на
 *    wp_footer:20, после разметки, в порядке первого вызова: как и у
 *    коллектора, после всего инлайна body и после ug-main.
 *    Вызов до печати head (шорткод, отрендеренный в wp_head плагином SEO, или
 *    на хуке wp) не попадает в head: хэндл ждёт до wp_head:102 и встаёт в
 *    общую очередь late styles — иначе ядро напечатало бы его на wp_head:8
 *    вместе с зависимостью ug-main, раньше custom-css.
 *  - Всё, что поставлено в очередь после wp_footer:20 (шорткод в подвале
 *    плагина), печатается страховкой на wp_footer:100.
 *
 * JS — в подвале (in_footer), зависимости явно.
 */

declare(strict_types=1);

/** Версия ассета — mtime файла: меняется файл, меняется ?ver=, кэш браузера сбрасывается. */
function ug_asset_ver(string $rel): string
{
    $path = get_theme_file_path(ltrim($rel, '/'));
    return is_file($path) ? (string) filemtime($path) : '0';
}

/** assets/css/blocks/cases.css → ug-blocks-cases; assets/js/main.js → ug-main-js. */
function ug_style_handle(string $rel): string
{
    $rel = ltrim($rel, '/');
    $rel = (string) preg_replace('~^assets/(?:css|js)/~', '', $rel);
    $rel = (string) preg_replace('~\.(?:css|js)$~', '', $rel);
    return 'ug-' . sanitize_title(str_replace('/', '-', $rel));
}

/**
 * Путь ассета внутри темы или null. Только файлы темы с нужным расширением:
 * атрибуты шорткодов (custom_css у service_tiles) приходят из контента, и
 * раньше путь вида ../../wp-config.php читался file_get_contents и уходил
 * в HTML. Несуществующий файл тоже null — без 404 на странице.
 */
function ug_asset_rel(string $rel, string $ext): ?string
{
    $rel = ltrim(trim($rel), '/');
    if ($rel === '' || str_contains($rel, '..') || !str_ends_with(strtolower($rel), '.' . $ext)) {
        return null;
    }
    $root = realpath(get_stylesheet_directory());
    $path = realpath(get_stylesheet_directory() . '/' . $rel);
    if ($root === false || $path === false || !str_starts_with($path, $root . DIRECTORY_SEPARATOR) || !is_file($path)) {
        return null;
    }
    return $rel;
}

/** Регистрирует CSS темы по пути (один раз), возвращает хэндл или null. */
function ug_register_style(string $rel, array $deps = ['ug-main']): ?string
{
    $rel = ug_asset_rel($rel, 'css');
    if ($rel === null) {
        return null;
    }
    $handle = ug_style_handle($rel);
    if (wp_style_is($handle, 'registered')) {
        return $handle;
    }
    // Файлы, которые в <link> повели бы себя не так, как в склейке коллектора,
    // печатаем инлайном на их месте в очереди, как раньше:
    //  - абсолютные https://taxlab.ru/… в url(): инлайн переписывал под адрес
    //    стенда фильтр вывода, у файла этого нет — стенд тянул бы картинки с
    //    прода (антибот отдаёт там не картинку). Сейчас это
    //    business_first_screen.css, open_business.css, static_startup.css;
    //  - @import (su-welcome-screen.css: Roboto с Google): в середине склейки
    //    браузер его игнорировал, первой строкой файла — выполнит. Вырезаем.
    // Владельцам файлов (WS-4, WS-5): сделать url() относительными и убрать
    // @import — и файл уйдёт обычным <link>.
    $css = (string) file_get_contents(get_stylesheet_directory() . '/' . $rel);
    if (str_contains($css, '//taxlab.ru/') || str_contains($css, '@import')) {
        wp_register_style($handle, false, $deps);
        wp_add_inline_style($handle, (string) preg_replace('~^\s*@import\s[^;]*;\s*~m', '', $css));
        return $handle;
    }
    wp_register_style($handle, get_theme_file_uri($rel), $deps, ug_asset_ver($rel));
    return $handle;
}

/** CSS, который ждёт печати head, — см. шапку файла. */
function ug_pending_styles(?string $add = null): array
{
    static $pending = [];
    if ($add !== null && !in_array($add, $pending, true)) {
        $pending[] = $add;
    }
    return $pending;
}

/** CSS блока: late styles на wp_footer:20 (после ug-main и разметки). */
function ug_enqueue_style(string $rel, array $deps = ['ug-main']): void
{
    $handle = ug_register_style($rel, $deps);
    if ($handle !== null) {
        ug_enqueue_style_handle($handle);
    }
}

/** CSS в <head> сразу после ug-main. Звать до wp_head:101 (хук wp, wp_enqueue_scripts). */
function ug_head_style(string $rel): void
{
    $handle = ug_register_style($rel);
    if ($handle === null) {
        return;
    }
    if (did_action('ug_head_styles_printed')) {
        wp_enqueue_style($handle);
        return;
    }
    ug_head_styles($handle);
}

function ug_head_styles(?string $add = null): array
{
    static $head = ['ug-main'];
    if ($add !== null && !in_array($add, $head, true)) {
        $head[] = $add;
    }
    return $head;
}

/**
 * Копия CSS блока в <head> — только для первого кадра (CLS, LCP). Сам файл
 * блока по-прежнему встаёт в late styles, когда шорткод отрендерится, и стоит
 * там, где стоял: копия идентична и раньше по документу, поэтому каскад тот
 * же (любое её значение перекрывается тем же значением late-копии).
 */
function ug_paint_copy(string $rel): void
{
    $handle = ug_register_style($rel);
    if ($handle === null || did_action('ug_head_styles_printed')) {
        return;
    }
    $styles = wp_styles();
    $copy = $handle . '-head';
    if (!wp_style_is($copy, 'registered')) {
        $item = $styles->registered[$handle];
        wp_register_style($copy, $item->src, [], $item->ver);
        foreach ((array) $styles->get_data($handle, 'after') as $css) {
            wp_add_inline_style($copy, (string) $css);
        }
    }
    ug_paint_styles($copy);
}

function ug_paint_styles(?string $add = null): array
{
    static $copies = [];
    if ($add !== null && !in_array($add, $copies, true)) {
        $copies[] = $add;
    }
    return $copies;
}

/**
 * Первый экран: CSS шорткодов, которые стоят в контенте записи, — копией в
 * <head> (спека §3.2, предскан на хуке wp). Шорткоды из UX-блоков (тип blocks)
 * сюда не попадают — их CSS приходит late styles, как и раньше.
 */
const UG_FIRST_SCREEN_CSS = [
    'new_main_first_screen'        => ['assets/css/blocks/new_main_first_screen.css'],
    'su_welcome_screen'            => ['assets/css/blocks/su-welcome-screen.css'],
    'su_welcome_screen_conditions' => ['assets/css/blocks/su-welcome-screen-conditions.css'],
    'business_first_screen'        => ['assets/css/blocks/business_first_screen.css'],
    'service_tiles'                => ['assets/css/blocks/services-tiles.css'],
];

add_action('wp', function (): void {
    if (!is_singular() || is_404()) {
        return;
    }
    $post = get_queried_object();
    if (!$post instanceof WP_Post || $post->post_content === '') {
        return;
    }
    foreach (UG_FIRST_SCREEN_CSS as $tag => $files) {
        if (!has_shortcode($post->post_content, $tag)) {
            continue;
        }
        foreach ($files as $rel) {
            ug_paint_copy($rel);
        }
    }
    // service_tiles custom_css: единственное значение в БД (страницы 13514, 13516).
    if (str_contains($post->post_content, 'services-tiles-new.css') && has_shortcode($post->post_content, 'service_tiles')) {
        ug_paint_copy('assets/css/blocks/services-tiles-new.css');
    }
});

/** JS темы по пути: in_footer по умолчанию, зависимости явно. */
function ug_enqueue_script(string $rel, array $deps = [], array $args = ['in_footer' => true]): void
{
    $rel = ug_asset_rel($rel, 'js');
    if ($rel === null) {
        return;
    }
    $handle = ug_style_handle($rel);
    if (!wp_script_is($handle, 'registered')) {
        wp_register_script($handle, get_theme_file_uri($rel), $deps, ug_asset_ver($rel), $args);
    }
    wp_enqueue_script($handle);
}

/** Библиотеки — реестр руками: у них стабильные хэндлы, по ним зовут шорткоды. */
function ug_register_libraries(): void
{
    wp_register_style('ug-main', get_theme_file_uri('assets/css/main.css'), [], ug_asset_ver('assets/css/main.css'));

    // Swiper — две версии, как и было: 11.2.5 (js/swiper-bundle.min.js) у
    // слайдера on_call на главной, без CSS; 14.3.0 — локальная копия того,
    // что отдавал jsdelivr без версии (/npm/swiper/…) на 14148: там он
    // грузился последним, и все слайдеры страницы инициализировались им
    // (стрелки навигации — его SVG). Одна версия на обе страницы меняет
    // снимки (проверено): свести — с прокликиванием слайдеров (WS-5).
    wp_register_script('ug-swiper', get_theme_file_uri('js/swiper-bundle.min.js'), [], '11.2.5', ['in_footer' => true]);
    wp_register_script('ug-swiper-14', get_theme_file_uri('assets/js/vendor/swiper-bundle.min.js'), [], '14.3.0', ['in_footer' => true]);
    wp_register_style('ug-swiper-14', get_theme_file_uri('assets/css/vendor/swiper-bundle.min.css'), [], '14.3.0');

    // Fancybox 4.0.31: js/fancybox.umd.js и css/fancybox.css — те же файлы,
    // что на jsdelivr @fancyapps/ui@4.0 (побайтно).
    wp_register_script('ug-fancybox', get_theme_file_uri('js/fancybox.umd.js'), [], '4.0.31', ['in_footer' => true]);
    wp_register_style('ug-fancybox', get_theme_file_uri('css/fancybox.css'), [], '4.0.31');
}

/** Библиотека целиком: JS в подвал, CSS — в late styles перед CSS блоков. */
function ug_enqueue_library(string $handle): void
{
    wp_enqueue_script($handle);
    if (wp_style_is($handle, 'registered')) {
        ug_enqueue_style_handle($handle);
    }
}

/** Зарегистрированный хэндл CSS с той же логикой head/late, что у ug_enqueue_style(). */
function ug_enqueue_style_handle(string $handle): void
{
    if (did_action('ug_head_styles_printed')) {
        wp_enqueue_style($handle);
    } else {
        ug_pending_styles($handle);
    }
}
add_action('wp_enqueue_scripts', 'ug_register_libraries', 1);

add_action('wp_head', function (): void {
    // 404.php не зовёт get_footer(): wp_footer там нет, и CSS темы, который
    // раньше печатался только в wp_footer, на 404 не выводился вовсе. Вёрстку
    // 404 не меняем: в head ug-main тоже не печатаем (решение — за владельцем).
    if (!is_404()) {
        wp_print_styles(array_merge(ug_head_styles(), ug_paint_styles()));
    }
    do_action('ug_head_styles_printed');
}, 101);

add_action('wp_head', function (): void {
    foreach (ug_pending_styles() as $handle) {
        wp_enqueue_style($handle);
    }
}, 102);

/**
 * Якорь каскада: те же ug-main и CSS страницы ещё раз — в конце <body>, перед
 * CSS блоков (late styles на wp_footer:20). Файл тот же, браузер берёт его из
 * кэша, второго запроса нет.
 *
 * Зачем: раньше весь CSS темы стоял <style>-блоком в самом конце <body> и
 * в равных по специфичности правилах побеждал инлайн-<style> контента — UX-
 * блоков и форм из БД (например, .lightbox-form label в блоке 2891 против
 * .footer-form label темы), шапки из theme_mods (#umbrella-menu-button) и
 * шорткодов. Копия в <head> красит первый кадр (CLS), копия здесь сохраняет
 * прежний победитель каждой пары: она идентична и стоит позже, поэтому любое
 * значение из head-копии перекрывается тем же значением отсюда.
 * Убирается, когда инлайн-стили контента переедут в файлы (спека §9, п. 12).
 */
add_action('wp_footer', function (): void {
    if (!did_action('ug_head_styles_printed')) {
        return;
    }
    $styles = wp_styles();
    foreach (ug_head_styles() as $handle) {
        $item = $styles->registered[$handle] ?? null;
        if ($item === null) {
            continue;
        }
        $href = $styles->_css_href((string) $item->src, $item->ver, $handle);
        printf("<link rel='stylesheet' id='%s-tail-css' href='%s' type='text/css' media='all' />\n", esc_attr($handle), esc_url($href));
    }
}, 19);

// Библиотеки, которые встали в очередь на этой странице (ug-swiper,
// ug-swiper-14, ug-fancybox), печатаются раньше JS темы: раньше они стояли <script> в body,
// а JS блоков — в конце body, и open_bussnise.js, отрендеренный выше
// open_business, всё равно видел Swiper.
add_action('wp_footer', function (): void {
    $scripts = wp_scripts();
    $libs = array_values(array_filter(['ug-swiper', 'ug-swiper-14', 'ug-fancybox'], fn(string $h): bool => wp_script_is($h, 'enqueued')));
    if ($libs === []) {
        return;
    }
    foreach ($scripts->queue as $handle) {
        if (str_starts_with($handle, 'ug-') && !in_array($handle, $libs, true) && isset($scripts->registered[$handle])) {
            $deps = &$scripts->registered[$handle]->deps;
            $deps = array_values(array_unique(array_merge($deps, $libs)));
            unset($deps);
        }
    }
}, 19);

// Страховка: что встало в очередь после печати подвала ядром (wp_footer:20),
// печатаем в конце wp_footer — там, где раньше стоял <style> коллектора.
add_action('wp_footer', function (): void {
    if (!did_action('ug_head_styles_printed')) {
        do_action('ug_head_styles_printed');
        wp_print_styles(ug_head_styles());
        foreach (ug_pending_styles() as $handle) {
            wp_enqueue_style($handle);
        }
    }
    print_late_styles();
    print_footer_scripts();
}, 100);

// До Flatsome, на прежнем месте хэндлов Inter-font, fonts-configuration и
// custom-form-style: @font-face Inter и PT Serif (fonts/fonts.css) и ug-base
// (assets/css/base.css — стили, которые Flatsome и custom-css должны
// перекрывать, как и раньше).
add_action('wp_enqueue_scripts', function (): void {
    // Иконки .fa (6 штук) сабсетом Font Awesome 4.7 — замена cdnjs
    // font-awesome.min.css из header.php: WS-3 удаляет ту ссылку, эта уже стоит.
    wp_enqueue_style('ug-icons', get_theme_file_uri('assets/css/icons.css'), [], ug_asset_ver('assets/css/icons.css'));
    wp_enqueue_style('ug-fonts', get_theme_file_uri('fonts/fonts.css'), [], ug_asset_ver('fonts/fonts.css'));
    wp_enqueue_style('ug-base', get_theme_file_uri('assets/css/base.css'), ['ug-fonts'], ug_asset_ver('assets/css/base.css'));
});

// Google Fonts Flatsome (PT Serif + системные стеки из type_texts/type_nav,
// которые Google не отдаёт) — не нужен: PT Serif лежит в fonts/fonts.css.
add_action('after_setup_theme', function (): void {
    remove_action('wp_enqueue_scripts', 'flatsome_google_fonts', 9999);
});

// CSS плагина wp-add-custom-css (wpacc_settings.main_custom_style, 151 Б)
// скопирован в assets/css/base.css. Его <link href="/?display_custom_css=css">
// — блокирующий запрос через полную загрузку WordPress ради тех же правил:
// снимаем, пока текст в настройках совпадает с копией. Правила там — cursor
// и padding-bottom с !important, поэтому место в каскаде им не важно.
// Поправят CSS в админке — хэш не совпадёт, и ссылка плагина вернётся сама.
const UG_WPACC_COPIED_MD5 = 'eb6b7829da2bc638ab58a2ca88ea426c';

add_action('wp_enqueue_scripts', function (): void {
    $css = get_option('wpacc_settings')['main_custom_style'] ?? null;
    if (is_string($css) && md5(str_replace("\r\n", "\n", $css)) === UG_WPACC_COPIED_MD5) {
        wp_dequeue_style('wp-add-custom-css');
    }
}, 10000);

// Основное начертание текста — preload: качается до разбора CSS.
add_action('wp_head', function (): void {
    printf(
        "<link rel='preload' href='%s' as='font' type='font/woff2' crossorigin />\n",
        esc_url(get_theme_file_uri('fonts/Inter-Regular.woff2'))
    );
}, 2);

add_action('wp_enqueue_scripts', function (): void {
    wp_enqueue_script('mailscript', get_theme_file_uri('js/script.js'), [], ug_asset_ver('js/script.js'), true);
});

/**
 * CSS и JS отдельных страниц (были в коллекторе по подстроке permalink):
 * страницы цен /price/… — pages/price.css + pages/price.js, вопросы /faq/
 * (архив и записи faq) — pages/faq.js. Скрипты страниц и общий main.js
 * исполнялись в конце <body> после всех скриптов — ставим их после flatsome-js.
 */
add_action('wp_enqueue_scripts', function (): void {
    ug_enqueue_script('assets/js/main.js', ['jquery', 'flatsome-js']);
    if (is_page() && str_contains((string) get_permalink(get_queried_object_id()), '/price/')) {
        ug_head_style('assets/css/pages/price.css');
        ug_enqueue_script('assets/js/pages/price.js', ['jquery', 'flatsome-js']);
    }
    if (is_post_type_archive('faq') || is_singular('faq')) {
        ug_enqueue_script('assets/js/pages/faq.js', ['jquery', 'flatsome-js']);
    }
}, 100);
