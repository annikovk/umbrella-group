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
 * reCAPTCHA без потерь заявок.
 *
 * 1. Нет токена или Google его не подтвердил — для ВСЕХ форм. reCAPTCHA не
 *    загрузилась у человека (23.11: Яндекс Браузер на десктопе — форма
 *    «крутилась» и не отправлялась) или Google не ответил / вернул ошибку
 *    (browser-error и т.п.). Браузер через ~7 с отправляет форму с пустым
 *    токеном (заглушка grecaptcha в inc/perf.php), здесь такую заявку спамом
 *    не считаем.
 * 2. Настоящая низкая оценка — только для форм из UG_CF7_RECAPTCHA_SOFT_FORMS
 *    (31425 — расчёт на /price/; 13.11 у Наташи 0.10 в Яндекс Браузере дважды
 *    подряд). У остальных форм низкая оценка — спам, как раньше.
 *
 * В обоих случаях письмо уходит с пометкой в теме «[проверьте: возможно спам]»
 * и строкой о reCAPTCHA в тексте, в Flamingo заявка сохраняется с той же
 * пометкой в заголовке, человек видит обычное «Спасибо…». Если спамом заявку
 * счёл ещё кто-то кроме reCAPTCHA (проверки CF7, Akismet, стоп-слова), — как
 * раньше, спам.
 *
 * Формы, у которых ID на проде и локально разный, добавляются фильтром
 * ug_cf7_recaptcha_soft_forms по заголовку: «Чек-листы для клиник
 * (конференция)» — inc/leadmagnet.php.
 */
const UG_CF7_RECAPTCHA_SOFT_FORMS = [31425];

const UG_CF7_SPAM_MARK = '[проверьте: возможно спам] ';

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
    $log = (array) $submission->get_spam_log();
    if ($log === [] || array_filter($log, static fn($e) => ($e['agent'] ?? '') !== 'recaptcha') !== []) {
        return $spam;
    }
    $service = class_exists('WPCF7_RECAPTCHA') ? WPCF7_RECAPTCHA::get_instance() : null;
    $threshold = $service ? number_format((float) $service->get_threshold(), 2, '.', '') : '?';
    $token = trim((string) wp_unslash($_POST['_wpcf7_recaptcha_response'] ?? ''));
    // Ответ siteverify CF7 кладёт в submission, только если Google ответил HTTP 200.
    $pushed = $submission->pull('recaptcha');
    $body = is_array($pushed) && is_array($pushed['response'] ?? null) ? $pushed['response'] : null;

    if ($token === '') {
        $status = 'токена нет — reCAPTCHA не загрузилась или не ответила в браузере';
    } elseif ($body === null) {
        $status = 'Google не ответил на проверку токена';
    } elseif (empty($body['success']) || !isset($body['score'])) {
        $codes = implode(', ', array_map('strval', (array) ($body['error-codes'] ?? [])));
        $status = 'Google не подтвердил токен' . ($codes !== '' ? ' (' . $codes . ')' : '');
    } else {
        // Настоящая оценка ниже порога: мягко только для форм из списка.
        $form = $submission->get_contact_form();
        $soft = (array) apply_filters('ug_cf7_recaptcha_soft_forms', UG_CF7_RECAPTCHA_SOFT_FORMS);
        if (!$form || !in_array((int) $form->id(), $soft, true)) {
            return $spam;
        }
        $status = 'оценка ' . number_format((float) $body['score'], 2, '.', '') . ' при пороге ' . $threshold;
    }

    ug_cf7_recaptcha_soft_note('reCAPTCHA: ' . $status . ' — CF7 счёл бы заявку спамом, проверьте её.');
    return false;
}, 100, 2);

add_filter('wpcf7_mail_components', static function ($components) {
    $note = ug_cf7_recaptcha_soft_note();
    if ($note === null || !is_array($components)) {
        return $components;
    }
    $components['subject'] = UG_CF7_SPAM_MARK . ($components['subject'] ?? '');
    $components['body'] = ($components['body'] ?? '') . "\n\n" . $note;
    return $components;
}, 10, 1);

// Та же пометка — в заголовке заявки в Flamingo, строка о reCAPTCHA — в её данных.
add_filter('wpcf7_flamingo_inbound_message_parameters', static function ($args) {
    $note = ug_cf7_recaptcha_soft_note();
    if ($note === null || !is_array($args)) {
        return $args;
    }
    $args['subject'] = UG_CF7_SPAM_MARK . ($args['subject'] ?? '');
    $args['meta'] = (array) ($args['meta'] ?? []);
    $args['meta']['ug_recaptcha'] = $note;
    return $args;
}, 10, 1);
