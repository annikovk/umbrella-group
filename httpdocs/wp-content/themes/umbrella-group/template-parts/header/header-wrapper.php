<?php
/**
 * Шапка: верхняя полоса, основная, нижняя. $args['variant'] — из header.php
 * ('business' у page-bussnise.php: свой основной ряд и своя нижняя полоса).
 *
 * Один DOM для всех устройств: верхнюю полосу раньше не печатали мобильным
 * (wp_is_mobile), теперь её на ≤ 849px прячет класс hide-for-medium Flatsome
 * (header_inner_class('top') при пустом header_mobile_elements_top) — и
 * страница больше не зависит от User-Agent.
 */
$ug_variant = $args['variant'] ?? 'default';

get_template_part('template-parts/header/header', 'top');
get_template_part('template-parts/header/header', 'main', ['variant' => $ug_variant]);
get_template_part('template-parts/header/header', $ug_variant === 'business' ? 'bus-bottom' : 'bottom');
echo '<div class="header-bg-container fill">';
do_action('flatsome_header_background');
echo '</div>';
do_action('flatsome_header_wrapper');
