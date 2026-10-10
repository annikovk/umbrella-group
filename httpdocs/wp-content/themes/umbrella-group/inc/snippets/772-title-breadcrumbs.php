<?php
/**
 * Сниппет insert-php #772 «Все страницы | Title & Breadcumbs» → [ug_title_breadcrumbs].
 * UX-блок 9502 и два черновика страниц.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_title_breadcrumbs(array $a): void
{
    echo umbrella_draw_header_with_breadcrumbs();
}

ug_snippet_shortcode('ug_title_breadcrumbs', 'ug_snippet_title_breadcrumbs');
