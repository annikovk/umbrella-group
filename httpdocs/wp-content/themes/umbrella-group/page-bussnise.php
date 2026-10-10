<?php
/**
 * Template Name: Page - Open a business
 *
 * Страница «Открытие бизнеса под ключ» (14148): тот же page.php, но шапка
 * в варианте business (header.php, template-parts/analytics.php).
 *
 * @package flatsome
 */

set_query_var('ug_header_variant', 'business');
require get_theme_file_path('page.php');
