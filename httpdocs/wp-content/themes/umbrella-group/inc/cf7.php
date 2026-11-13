<?php
/**
 * Contact Form 7: обёртка вывода ответа формы (порог reCAPTCHA — mu-plugin cf7-threshold-03.php).
 *
 * Атрибуция заявок (поля omatraffic/omacid) — inc/attribution.php.
 * custom_filter_wpcf7_is_tel удалён (WS-1): висел на wpcf7_validate_tel, а в формах
 * нет ни одного [tel] — только один [tel*] (свой фильтр wpcf7_validate_tel*);
 * к тому же возвращал int вместо WPCF7_Validation.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

// Ключи reCAPTCHA — в настройках CF7 (wp-admin → Contact → Integration).
// Порог reCAPTCHA (0.3) задаёт mu-plugin cf7-threshold-03.php; здесь до
// 2026-11-13 стоял свой фильтр 0.7, грузился позже и перебивал его.

add_filter('wpcf7_form_response_output', static function ($output, $class, $content, $form) {
    return '<div class="custom-cf7-alert ' . esc_attr((string) $class) . '">'
           . $content .
           '</div>';
}, 10, 4);
