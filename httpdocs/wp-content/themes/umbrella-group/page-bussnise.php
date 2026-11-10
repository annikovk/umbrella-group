<?php
/**
 * Template Name: Page - Open a business
 *
 * Страница «Открытие бизнеса под ключ» (14148): тот же page.php, вариант
 * business — свои счётчики (template-parts/analytics.php) и без
 * assets/css/header.css; шапка — общая header-site.php.
 *
 * @package flatsome
 */

set_query_var('ug_header_variant', 'business');
require get_theme_file_path('page.php');
