<?php
/**
 * Сниппет insert-php #1961 «Плитки записей блога» → [ug_blog_tiles numberposts=2 category="…"].
 * Последние записи блога плитками; атрибуты include, category (slug), numberposts.
 * Стоит в 46 записях (обычно numberposts=2 внизу страниц услуг).
 */

declare(strict_types=1);

function ug_snippet_blog_tiles(array $a): void
{
    $include = empty($a['include']) ? '' : $a['include'];
    $category = empty($a['category']) ? '' : $a['category'];
    $numberposts = empty($a['numberposts']) ? '' : $a['numberposts'];
    $args = array(
        'exclude' => '',
        'include' => $include,
        'numberposts' => $numberposts,
        'category_name' => $category,
    );
    $pages = get_posts($args);
    umbrella_draw_tiles($pages, 'blogposts');
}

ug_snippet_shortcode('ug_blog_tiles', 'ug_snippet_blog_tiles');
