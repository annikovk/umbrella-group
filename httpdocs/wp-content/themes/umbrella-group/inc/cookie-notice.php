<?php
/**
 * Плашка согласия на cookie и getCookie() для неё.
 *
 * Перенесено из functions.php:126-137, 1099-1111 дословно (WS-0). Подключается
 * последним в inc/bootstrap.php: output_cookie_notice на wp_footer:10 должен
 * идти после umbrella_upsell_template и umbrella_filter_ab_tests того же
 * приоритета — как в старом functions.php. Переписывает WS-1.
 */

add_action('wp_footer', function() {
    ?>
    <script>
    var getCookie = function(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\/+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    };
    </script>
    <?php
}, 5);

add_action('wp_footer', 'output_cookie_notice');

function output_cookie_notice() {
    if( isset( $_COOKIE['cookieNotice'] ) ) {
        return;
    } ?>
    <div class="cookie-notice">
        <div class="cookie-notice__text">Мы используем cookies, чтобы сайт работал лучше. Продолжая пользоваться сайтом, вы соглашаетесь с <a href="/politika-cookie/">использованием cookies</a></div>
        <div class="cookie-notice__buttons">
            <a href="#" class="cookie-notice__confirm">Принимаю</a>
        </div>
    </div>
<?php }
