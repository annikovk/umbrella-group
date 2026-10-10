<?php
/**
 * Сниппет insert-php #7300 «/learning menu» → [ug_learning_menu]: дочерние
 * страницы текущей (до 5 уровней) списком <li> — страница /learning/ (6824).
 */

declare(strict_types=1);

function ug_snippet_learning_menu(array $a): void
{
    global $post;
    wp_list_pages(array(
        // Только дочерние текущей страницы
        'child_of' => $post->ID,
        'title_li' => false,
        'item_spacing' => 'discard',
        'depth' => 5,
        'sort_column' => 'menu_order, post_date',
        'sort_order' => 'desc'
    ));
}

ug_snippet_shortcode('ug_learning_menu', 'ug_snippet_learning_menu');
