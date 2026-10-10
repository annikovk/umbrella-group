<?php
/**
 * Сниппет insert-php #2906 «testtttt» → [ug_child_pages]: список дочерних
 * страниц страницы 10 (/services/). Стоит только в UX-блоке 9951 «Черновик»,
 * который никуда не вставлен.
 */

declare(strict_types=1);

function ug_snippet_child_pages(array $a): void
{
    $parent = 10;

    $children = wp_list_pages("title_li=&child_of=" . $parent . "&echo=0");

    if ($children) {
        ?>

    <ul >
        <?php
            // у текущей страницы будет класс current_page_item
            echo $children;
        ?>
    </ul>

<?php
    }
}

ug_snippet_shortcode('ug_child_pages', 'ug_snippet_child_pages');
