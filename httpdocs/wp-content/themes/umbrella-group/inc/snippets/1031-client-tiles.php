<?php
/**
 * Сниппет insert-php #1031 «Наши клиенты | плитки клиентов» → [ug_client_tiles].
 * Табы-фильтры по отраслям и плитки всех клиентов; страница /about/clients/ (1000).
 */

declare(strict_types=1);

function ug_snippet_client_tiles(array $a): void
{
    $args = array(
        'taxonomy' => 'client_industry',
        'orderby' => 'name',
        'order'   => 'ASC'
    );

    $categories = get_categories($args);

    $args = array(
        'numberposts' => '99',
        'post_type' => 'client',
    );

    $posts = get_posts($args);
    umbrella_draw_filter_tabs($categories, $posts, 'clients');

    echo '<div id="tiles">';
    echo '[section]';
    echo '[row v_align="equal" class="clients-row" h_align="left"  ]';

    umbrella_draw_tiles($posts, 'clients');

    echo '[/row]';
    echo '[/section]';
    echo '</div>';
    ?>
<style>
    @media screen and (max-width: 849px) {
        .toolbar {
            display: none;
        }
    }
</style>
<?php
}

ug_snippet_shortcode('ug_client_tiles', 'ug_snippet_client_tiles');
