<?php
/**
 * Сниппет insert-php #1870 «Отзывы» → [ug_feedback_page]: страница /feedback/ (1266).
 *
 * includes/pages/feedback-page.php объявляет функции при подключении, поэтому
 * require (не _once) — как у сниппета: шорткод стоит на странице один раз.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_feedback_page(array $a): void
{
    require get_theme_file_path() . '/includes/pages/feedback-page.php';
}

ug_snippet_shortcode('ug_feedback_page', 'ug_snippet_feedback_page');
