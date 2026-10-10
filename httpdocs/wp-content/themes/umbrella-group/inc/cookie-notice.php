<?php
/**
 * Плашка согласия на cookie.
 *
 * Разметка печатается всегда (раньше — только без cookie cookieNotice, и это
 * запекалось в кэш страниц: у согласившихся плашка висела, а «Принимаю» не
 * работал). Скрыта стилем assets/css/cookie-notice.css; показывает её
 * инлайн-скрипт в <head> — ставит html.ug-consent-needed, если cookie нет.
 * Клик «Принимаю» — js/script.js.
 *
 * Подключается последним в inc/bootstrap.php: output_cookie_notice на
 * wp_footer:10 идёт после umbrella_upsell_template того же приоритета.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', static function (): void {
    $rel = 'assets/css/cookie-notice.css';
    wp_enqueue_style(
        'ug-cookie-notice',
        get_theme_file_uri($rel),
        [],
        (string) filemtime(get_theme_file_path($rel))
    );
});

// До первой отрисовки: иначе плашка мигнёт или не появится до загрузки script.js.
add_action('wp_head', static function (): void {
    echo "<script>if(!/(?:^|; )cookieNotice=/.test(document.cookie))document.documentElement.classList.add('ug-consent-needed')</script>\n";
}, 1);

add_action('wp_footer', 'output_cookie_notice');

function output_cookie_notice(): void
{ ?>
    <div class="cookie-notice">
        <div class="cookie-notice__text">Мы используем cookies, чтобы сайт работал лучше. Продолжая пользоваться сайтом, вы соглашаетесь с <a href="/politika-cookie/">использованием cookies</a></div>
        <div class="cookie-notice__buttons">
            <a href="#" class="cookie-notice__confirm">Принимаю</a>
        </div>
    </div>
<?php }
