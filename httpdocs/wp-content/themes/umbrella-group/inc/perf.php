<?php
/**
 * Скорость первого экрана на телефоне (замер Lighthouse mobile, 2026-11-02).
 *
 * 1. ugLater() — очередь сторонних загрузчиков (счётчики, пиксели, квиз):
 *    они стартуют по первому действию посетителя (скролл, касание, клавиша,
 *    мышь) или через 2 с после load (страховка — 8 с от начала разбора), а
 *    не в момент разбора <head>, где делят канал с CSS и LCP-картинкой.
 *    Сами счётчики не меняются: dataLayer/gtag/ym и их очереди объявляются
 *    сразу, события копятся и уходят, когда загрузится скрипт.
 * 2. Скрипты из настроек Flatsome (theme_mods html_scripts_*: GTM-WW2PFC,
 *    пиксели VK, Roistat, qoopler) — те же, но IIFE-загрузчики обёрнуты в
 *    ugLater() на выводе. В базе ничего не меняется; в админке/кастомайзере
 *    фильтр не работает, чтобы обёртка не сохранилась в настройки.
 * 3. reCAPTCHA v3 (CF7) — api.js (~400 КБ) грузится при первом касании,
 *    фокусе или клавише, а если форму отправят раньше — по первому
 *    grecaptcha.execute(): до загрузки его вызовы ждут в очереди (заглушка).
 * 4. ug_hero_sources() — webp-варианты фона первого экрана, если они лежат
 *    рядом с оригиналом: <имя>-ug-m.webp (кроп по центру под телефон, ≤549px)
 *    и <имя>-ug.webp (полный размер). Нет файлов — остаётся оригинал.
 *
 * 5. jQuery и jquery-migrate — в подвал на страницах разделов из
 *    UG_JQUERY_FOOTER_SECTIONS: в <head> они блокируют первую отрисовку
 *    (~36 КБ). Проверено 2026-11-02 на всех 13 страницах раздела (стенд и
 *    HTML прода): ни один встроенный скрипт до скриптов подвала jQuery не
 *    зовёт. Если шорткод/сниппет начнёт печатать в body инлайн с jQuery(…) —
 *    раздел отсюда убрать. Скрипт в <head>, зависящий от jquery, сам вернёт
 *    его в <head> (группы зависимостей в WP_Scripts).
 *
 * Откат: убрать require этого файла из inc/bootstrap.php (п. 1–3), ug_hero_*
 * вызываются из su_welcome_screen.php и без webp-файлов отдают оригинал.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/** Фронт, не админка, не кастомайзер, не REST/AJAX — только там переписываем вывод. */
function ug_perf_frontend(): bool
{
    return !is_admin()
        && !wp_doing_ajax()
        && !(defined('REST_REQUEST') && REST_REQUEST)
        && !(function_exists('is_customize_preview') && is_customize_preview());
}

/** Обёртка вокруг тела скрипта: без ugLater (не напечатался) — выполнить сразу. */
function ug_later_js(string $body): string
{
    return "(window.ugLater||function(f){f()})(function(){\n" . $body . "\n});";
}

// ── 1. очередь отложенных загрузчиков ───────────────────────────────────────
add_action('wp_head', function (): void {
    if (!ug_perf_frontend()) {
        return;
    }
    ?>
<script>
(function (w) {
    var q = [], done = 0, opt = {passive: true},
        ev = ['pointerdown', 'touchstart', 'keydown', 'scroll', 'wheel', 'mousemove'];
    function call(f) { try { f(); } catch (e) { console.error(e); } }
    function run() {
        if (done) return;
        done = 1;
        ev.forEach(function (e) { w.removeEventListener(e, run, opt); });
        q.splice(0).forEach(call);
    }
    ev.forEach(function (e) { w.addEventListener(e, run, opt); });
    w.addEventListener('load', function () { setTimeout(run, 2000); });
    setTimeout(run, 8000);
    w.ugLater = function (f) { done ? call(f) : q.push(f); };
})(window);
</script>
<?php
}, 0);

// ── 2. скрипты из настроек Flatsome ─────────────────────────────────────────
function ug_defer_inline_loaders($html)
{
    if (!is_string($html) || $html === '' || !ug_perf_frontend()) {
        return $html;
    }
    return (string) preg_replace_callback('~<script\b([^>]*)>(.*?)</script>~is', static function (array $m): string {
        $attrs = $m[1];
        $body = trim($m[2]);
        // только встроенные IIFE-загрузчики: внешние src и скрипты с глобальными
        // объявлениями (getCookie / jivo_onLoadCallback для Jivo) — как есть
        if (stripos($attrs, 'src=') !== false || !preg_match('~^(?:\(\s*function\b|!\s*function\b)~', $body)) {
            return $m[0];
        }
        return '<script' . $attrs . '>' . ug_later_js($body) . '</script>';
    }, $html);
}

foreach (['html_scripts_header', 'html_scripts_footer', 'html_scripts_before_body'] as $ug_mod) {
    add_filter('theme_mod_' . $ug_mod, 'ug_defer_inline_loaders', 20);
}
unset($ug_mod);

// ── 3. reCAPTCHA v3 по первому взаимодействию ───────────────────────────────
add_action('wp_enqueue_scripts', function (): void {
    $scripts = wp_scripts();
    $item = $scripts->registered['google-recaptcha'] ?? null;
    if ($item === null || !is_string($item->src) || $item->src === '' || !ug_perf_frontend()) {
        return;
    }
    $src = $item->src;
    // хэндл остаётся зависимостью wpcf7-recaptcha, но вместо <script src> —
    // заглушка, которая сама подгрузит api.js
    $item->src = false;
    $shim = <<<'JS'
(function (w, d, src) {
    var q = [], state = 0, ev = ['pointerdown', 'touchstart', 'keydown', 'focusin'];
    // api.js дописывает настоящие ready/execute в этот же объект (проверено),
    // поэтому сверяем с сохранённой ссылкой, а не с stub.execute
    var stubExecute = function (k, o) {
        load();
        return new Promise(function (res, rej) { q.push(['e', k, o, res, rej]); });
    };
    var stub = {ready: function (cb) { q.push(['r', cb]); }, execute: stubExecute};
    function flush() {
        var g = w.grecaptcha;
        if (!g || g.execute === stubExecute) { console.error('ug: grecaptcha не загрузилась'); return; }
        state = 2;
        q.splice(0).forEach(function (it) {
            if (it[0] === 'r') g.ready(it[1]);
            else g.ready(function () { g.execute(it[1], it[2]).then(it[3], it[4]); });
        });
    }
    function load() {
        if (state) return;
        state = 1;
        ev.forEach(function (e) { w.removeEventListener(e, load, true); });
        w.ugRecaptchaLoaded = flush;
        var s = d.createElement('script');
        s.async = true;
        s.src = src + (src.indexOf('?') < 0 ? '?' : '&') + 'onload=ugRecaptchaLoaded';
        d.head.appendChild(s);
    }
    w.grecaptcha = stub;
    ev.forEach(function (e) { w.addEventListener(e, load, {capture: true, passive: true}); });
})(window, document, %s);
JS;
    wp_add_inline_script('google-recaptcha', sprintf($shim, wp_json_encode($src, JSON_UNESCAPED_SLASHES)));
}, 30);

// ── 4. webp фона первого экрана ─────────────────────────────────────────────
/**
 * Источники картинки первого экрана по ID вложения.
 *
 * @return array{src:string,width:int,height:int,mobile:?string,desktop:?string}|null
 */
function ug_hero_sources(int $attachment_id): ?array
{
    $src = wp_get_attachment_image_src($attachment_id, 'full');
    if (!$src) {
        return null;
    }
    $out = ['src' => (string) $src[0], 'width' => (int) $src[1], 'height' => (int) $src[2], 'mobile' => null, 'desktop' => null];
    $file = get_attached_file($attachment_id);
    if (is_string($file) && $file !== '') {
        $base = (string) preg_replace('~\.[a-z0-9]+$~i', '', $file);
        $url_base = (string) preg_replace('~\.[a-z0-9]+$~i', '', $out['src']);
        foreach (['mobile' => '-ug-m.webp', 'desktop' => '-ug.webp'] as $key => $suffix) {
            if (is_file($base . $suffix)) {
                $out[$key] = $url_base . $suffix;
            }
        }
    }
    return $out;
}

/** <picture> (или <img>, если webp нет) для фона первого экрана — LCP. */
function ug_hero_picture(int $attachment_id): string
{
    $s = ug_hero_sources($attachment_id);
    if ($s === null) {
        return '<img src="" loading="eager" fetchpriority="high" decoding="async" />';
    }
    $size = ($s['width'] && $s['height']) ? ' width="' . $s['width'] . '" height="' . $s['height'] . '"' : '';
    $img = '<img src="' . esc_url($s['src']) . '"' . $size . ' loading="eager" fetchpriority="high" decoding="async" />';
    if ($s['mobile'] === null && $s['desktop'] === null) {
        return $img;
    }
    $sources = '';
    if ($s['mobile'] !== null) {
        $sources .= '<source type="image/webp" media="(max-width: 549px)" srcset="' . esc_url($s['mobile']) . '">';
    }
    if ($s['desktop'] !== null) {
        $sources .= '<source type="image/webp" srcset="' . esc_url($s['desktop']) . '">';
    }
    // display:contents — img остаётся прямым потомком для .su_welcome_banner_background img
    return '<picture style="display:contents">' . $sources . $img . '</picture>';
}

/** Preload под тот же выбор, что сделает <picture>. */
function ug_hero_preload(int $attachment_id): string
{
    $s = ug_hero_sources($attachment_id);
    if ($s === null) {
        return '';
    }
    $link = static fn(string $href, string $extra): string =>
        '<link rel="preload" as="image" href="' . esc_url($href) . '"' . $extra . ' fetchpriority="high">' . "\n";
    if ($s['mobile'] === null && $s['desktop'] === null) {
        return $link($s['src'], '');
    }
    $out = '';
    if ($s['mobile'] !== null) {
        $out .= $link($s['mobile'], ' type="image/webp" media="(max-width: 549px)"');
    }
    $out .= $link($s['desktop'] ?? $s['src'], ($s['desktop'] !== null ? ' type="image/webp"' : '') . ($s['mobile'] !== null ? ' media="(min-width: 550px)"' : ''));
    return $out;
}

// ── 5. jQuery в подвал на страницах выбранных разделов ──────────────────────
const UG_JQUERY_FOOTER_SECTIONS = ['bukhgalterskie-uslugi'];

add_action('wp_enqueue_scripts', function (): void {
    if (!ug_perf_frontend() || !is_page() || !in_array(ug_current_service_section(), UG_JQUERY_FOOTER_SECTIONS, true)) {
        return;
    }
    $scripts = wp_scripts();
    foreach (['jquery', 'jquery-core', 'jquery-migrate'] as $handle) {
        if (isset($scripts->registered[$handle])) {
            $scripts->add_data($handle, 'group', 1);
        }
    }
}, 1000);
