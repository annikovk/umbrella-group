<?php
/**
 * Сниппет insert-php #1997 «Боковая панель блога» → [ug_blog_side_panel]:
 * метки записи в текстовом виджете сайдбара блога (опция widget_text).
 */

declare(strict_types=1);

function ug_snippet_blog_side_panel(array $a): void
{
    require get_theme_file_path() . '/includes/blog-side-panel.php';
}

ug_snippet_shortcode('ug_blog_side_panel', 'ug_snippet_blog_side_panel');
