<?php
/**
 * Contact Form 7: порог reCAPTCHA и обёртка вывода ответа формы.
 *
 * Атрибуция заявок (поля omatraffic/omacid) — inc/attribution.php.
 * custom_filter_wpcf7_is_tel удалён (WS-1): висел на wpcf7_validate_tel, а в формах
 * нет ни одного [tel] — только один [tel*] (свой фильтр wpcf7_validate_tel*);
 * к тому же возвращал int вместо WPCF7_Validation.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

// Ключи reCAPTCHA — в настройках CF7 (wp-admin → Contact → Integration).
add_filter('wpcf7_recaptcha_threshold', static fn($threshold) => 0.7, 10, 1);

add_filter('wpcf7_form_response_output', static function ($output, $class, $content, $form) {
    return '<div class="custom-cf7-alert ' . esc_attr((string) $class) . '">'
           . $content .
           '</div>';
}, 10, 4);
