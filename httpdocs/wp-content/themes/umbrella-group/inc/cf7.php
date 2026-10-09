<?php
/**
 * Contact Form 7: порог reCAPTCHA, обёртка вывода ответа формы, валидация tel.
 *
 * Перенесено из functions.php дословно (WS-0, спека 1 §5): строки 3-15, 138-142,
 * 1057-1064. Владелец дальнейших правок — WS-1 (custom_filter_wpcf7_is_tel удаляется).
 */

// increase recaptcha score
// please be aware that KEYS for recaptcha are written in 2 places: header.php and /themes/umbrella/js/recaptcha-wpcf7.js

add_filter( 'wpcf7_recaptcha_threshold',

    function( $threshold ) {
        $threshold = 0.7; // decrease threshold to 0.3

        return $threshold;
    },

    10, 1
);

add_filter('wpcf7_form_response_output', function($output, $class, $content, $form) {
    return '<div class="custom-cf7-alert ' . esc_attr($class) . '">' 
           . $content . 
           '</div>';
}, 10, 4);

function custom_filter_wpcf7_is_tel($result, $tel)

{
    $result = preg_match('/^\(?\+?([0-9]{1,4})?\)?[-\. ]?(\d{10})$/', $tel);
    return $result;
}

add_filter('wpcf7_validate_tel', 'custom_filter_wpcf7_is_tel', 10, 2);
