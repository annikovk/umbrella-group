<?php
/**
 * Шапка. $args['variant'] — из header.php: default — header-site.php (макет
 * 2026-11: строка меню, ряд с логотипом, полоса услуг); 'business' у
 * page-bussnise.php — прежние верхняя полоса, основной ряд и своя нижняя
 * полоса Flatsome.
 *
 * Один DOM для всех устройств: верхнюю полосу раньше не печатали мобильным
 * (wp_is_mobile), теперь её на ≤ 849px прячет класс hide-for-medium Flatsome
 * (header_inner_class('top') при пустом header_mobile_elements_top) — и
 * страница больше не зависит от User-Agent.
 */
$ug_variant = $args['variant'] ?? 'default';

if ($ug_variant === 'business') {
    get_template_part('template-parts/header/header', 'top');
    get_template_part('template-parts/header/header', 'main', ['variant' => $ug_variant]);
    get_template_part('template-parts/header/header', 'bus-bottom');
} else {
    // Шапка по макету 2026-11: своя разметка (header-site.php)
    // вместо header-top/main/bottom Flatsome и элементов из theme_mods.
    get_template_part('template-parts/header/header', 'site');
}
echo '<div class="header-bg-container fill">';
do_action('flatsome_header_background');
echo '</div>';
do_action('flatsome_header_wrapper');
