<?php
/**
 * ACF: прогрев кэшей перед циклами шорткодов (WS-5).
 *
 * ACF 5.9 читает всё по одному: определение поля по ключу — два запроса на
 * ключ, каждое значение репитера страницы опций — get_option() на не-autoload
 * опцию (запрос на подполе каждой строки), картинка — get_post() вложения и его
 * мета. На странице 14148 это было ~250 из 338 запросов. Здесь то же самое
 * кладётся в кэши ядра и ACF пачкой: вывод не меняется, меняется число SQL.
 *
 *  - ug_acf_prime_fields()          — определения полей групп (acf_get_fields);
 *  - ug_acf_prime_option_repeater() — значения репитера страницы опций одним SELECT;
 *  - ug_prime_attachments()         — вложения (посты + мета) по ID одним запросом.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/** Определения полей групп — в хранилище ACF: get_field('ключ') дальше без SQL. */
function ug_acf_prime_fields(string ...$groups): void
{
    if (!function_exists('acf_get_fields')) {
        return;
    }
    foreach ($groups as $group) {
        acf_get_fields($group);
    }
}

/**
 * Значения репитера страницы опций ($name — имя поля, «slider_case_bus»)
 * одним запросом: options_<name>, options_<name>_<i>_<подполе> и их ссылки
 * на ключи поля (_options_…) — в кэш опций ядра, как их положил бы get_option().
 * Подполя из $image_subfields — ID вложений: их посты и мета тоже грузятся.
 */
function ug_acf_prime_option_repeater(string $name, array $image_subfields = []): void
{
    global $wpdb;
    $like = $wpdb->esc_like('options_' . $name);
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
        $like . '%',
        '\_' . $like . '%'
    ));
    $alloptions = wp_load_alloptions();
    $images = [];
    foreach ((array) $rows as $row) {
        if (!isset($alloptions[$row->option_name])) {
            wp_cache_add($row->option_name, $row->option_value, 'options');
        }
        foreach ($image_subfields as $sub) {
            if (preg_match('/^options_' . preg_quote($name, '/') . '_\d+_' . preg_quote($sub, '/') . '$/', $row->option_name)) {
                $images[] = (int) $row->option_value;
            }
        }
    }
    ug_prime_attachments($images);
}

/** Посты вложений и их мета одним запросом на тип данных; 0 и дубли отбрасываются. */
function ug_prime_attachments(array $ids): void
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if ($ids !== []) {
        _prime_post_caches($ids, false, true);
    }
}
