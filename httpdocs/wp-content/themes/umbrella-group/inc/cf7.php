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

/*
 * Мягкая reCAPTCHA для форм из UG_CF7_RECAPTCHA_SOFT_FORMS (пока только 31425 —
 * форма расчёта на /price/). reCAPTCHA v3 занижает оценку живым людям (13.11:
 * 0.10 у Наташи из Яндекс Браузера, дважды подряд, и browser-error) — CF7
 * считает заявку спамом, письмо не уходит, человек видит ошибку, заявка
 * теряется. Здесь: если спамом заявку сочла ТОЛЬКО reCAPTCHA и токен был
 * (оценка есть или Google вернул ошибку браузера), спам снимаем — письмо уходит
 * с пометкой в теме «[проверьте: возможно спам]» и оценкой в тексте, человек
 * видит обычное «Спасибо…». Пустой токен (бот без JS), проверка nonce/UA CF7,
 * Akismet, стоп-слова — как раньше, спам.
 */
const UG_CF7_RECAPTCHA_SOFT_FORMS = [31425];

/** Пометка для писем текущей отправки: null — reCAPTCHA не спорила. */
function ug_cf7_recaptcha_soft_note(?string $set = null): ?string
{
    static $note = null;
    if ($set !== null) {
        $note = $set;
    }
    return $note;
}

add_filter('wpcf7_spam', static function ($spam, $submission) {
    if (!$spam || !$submission instanceof WPCF7_Submission) {
        return $spam;
    }
    $form = $submission->get_contact_form();
    if (!$form || !in_array((int) $form->id(), (array) apply_filters('ug_cf7_recaptcha_soft_forms', UG_CF7_RECAPTCHA_SOFT_FORMS), true)) {
        return $spam;
    }
    if (trim((string) ($_POST['_wpcf7_recaptcha_response'] ?? '')) === '') {
        return $spam;
    }
    $log = (array) $submission->get_spam_log();
    if ($log === [] || array_filter($log, static fn($e) => ($e['agent'] ?? '') !== 'recaptcha') !== []) {
        return $spam;
    }
    $service = class_exists('WPCF7_RECAPTCHA') ? WPCF7_RECAPTCHA::get_instance() : null;
    $score = $service ? $service->get_last_score() : null;
    ug_cf7_recaptcha_soft_note(sprintf(
        'reCAPTCHA: %s при пороге %s — CF7 счёл бы заявку спамом, проверьте её.',
        $score === null ? 'оценки нет (ошибка в браузере)' : 'оценка ' . number_format((float) $score, 2, '.', ''),
        $service ? number_format((float) $service->get_threshold(), 2, '.', '') : '?'
    ));
    return false;
}, 100, 2);

add_filter('wpcf7_mail_components', static function ($components) {
    $note = ug_cf7_recaptcha_soft_note();
    if ($note === null || !is_array($components)) {
        return $components;
    }
    $components['subject'] = '[проверьте: возможно спам] ' . ($components['subject'] ?? '');
    $components['body'] = ($components['body'] ?? '') . "\n\n" . $note;
    return $components;
}, 10, 1);
