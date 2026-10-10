<?php
/**
 * Сниппет insert-php #373 «Главная | Карусель отзывов» → [ug_clients_carousel].
 * Клиенты с меткой show-on-main: строки [row_inner] со сканом отзыва и текстом.
 * Вставлен в UX-блок 375 «carousel».
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_clients_carousel(array $a): void
{
    $tag = get_term_by('slug', 'show-on-main', 'client_tags');
    if (!empty($tag)) {
        $args = array(
            'numberposts' => 99,
            'post_type' => 'client',
            'tax_query' => array(
                array(
                    'taxonomy' => $tag->taxonomy,
                    'field' => $tag->taxonomy,
                    'terms' => $tag->term_id,
                ),
            )

        );
        $posts = get_posts($args);
    }

    if (!empty($posts)) {
        foreach ($posts as $post) {
            $logo = get_the_post_thumbnail($post->ID);
            $logo = '<div class="feedback-image">' . $logo . '</div>';
            $industry = get_post_meta($post->ID, 'umbrella_client_personal_industry', true);
            $industry = '<div class="feedback-industry">' . $industry . '</div>';
            $title = $post->post_title;
            $titleAndIndustry = '<div class="feedback-item-header"> ' . $title . $industry . '</div>';
            $excerpt = get_the_excerpt($post->ID);
            $excerpt = '<div class="feedback-excerpt">' . $excerpt . '</div>';
            $scan = get_post_meta($post->ID, 'umbrella_feedback_scan', true);
            $scan = '[ux_image id="' . $scan . '" width="100" height="328px" lightbox="true" depth_hover="3" depth="1" ]';

            echo '[row_inner padding="40px 0 40px 0" style="large" v_align="top" class="feedback-row"]';
            // Если есть скан отзыва — он слева, иначе только текст.
            if (!empty($scan)) {
                echo '
[col_inner span="3" margin="0px 0px 0px 0px" class="hide-for-medium"]
' . $scan . '
[/col_inner]
[col_inner span="9" span__sm="12" align="left" margin="0px 0px -30px 0px"]
' . $titleAndIndustry . $logo . $excerpt . "
[/col_inner]
";
            } else {
                echo '
[col_inner span="12" span__sm="12" align="left" margin="0px 0px -30px 0px"]
' . $titleAndIndustry . $logo . $excerpt . "
[/col_inner]
";
            }
            echo "[/row_inner]";
        }
    }
}

ug_snippet_shortcode('ug_clients_carousel', 'ug_snippet_clients_carousel');
