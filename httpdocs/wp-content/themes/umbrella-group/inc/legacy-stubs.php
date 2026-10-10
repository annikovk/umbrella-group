<?php
/**
 * Заглушки шорткодов удалённого функционала, спека 1 §3.1.
 *
 * Вебинары 2020-2021 (includes/webinar/, удалены в WS-1): открытый ретранслятор
 * писем, SMS на любой номер, SQL из POST без prepare, session_start на каждом
 * запросе. Шорткоды остались в контенте страниц /learning/ (5809, 6693, 6695,
 * 7214, 7992, 10302 — форма, 7232 — список участников) и без заглушки
 * вывелись бы сырым текстом.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_shortcode('webinar_registration_form', '__return_empty_string');
add_shortcode('webinar_participants_table', '__return_empty_string');
