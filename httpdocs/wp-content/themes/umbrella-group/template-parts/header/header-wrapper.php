<?php
/**
 * Шапка всех страниц — header-site.php (макет 2026-11: строка меню, ряд с
 * логотипом, полоса услуг), своя разметка вместо header-top/main/bottom
 * Flatsome и элементов из theme_mods. С 2026-11-10 и у «Открытия бизнеса под
 * ключ» (page-bussnise.php, 14148): вариант 'business' там остался только для
 * счётчиков (template-parts/analytics.php) и без assets/css/header.css
 * (inc/layout.php); прежние header-main.php / header-bus-bottom.php больше не
 * печатаются.
 */
get_template_part('template-parts/header/header', 'site');
echo '<div class="header-bg-container fill">';
do_action('flatsome_header_background');
echo '</div>';
do_action('flatsome_header_wrapper');
