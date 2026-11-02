<?php
/**
 * Сниппет insert-php #29221 «Услуги | Плитки услуг внутри категории
 * "Интеллектуальная собственность"» → [ug_service_tiles_ip] (страница 29220).
 *
 * Плитки услуг по жёсткому списку адресов (url_to_postid): адреса — прода,
 * на стенде с другим хостом url_to_postid() даёт 0 и плиток нет — как и у
 * сниппета. ug_snippet_service_tiles_by_url() — общая с [ug_service_tiles_tax].
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * @param string[] $service_urls
 */
function ug_snippet_service_tiles_by_url(array $service_urls): void
{
    $pages = array();

    // Получаем страницы по их URL
    foreach ($service_urls as $url) {
        $page_id = url_to_postid($url);
        if ($page_id) {
            $page = get_post($page_id);
            if ($page && $page->post_status == 'publish') {
                $pages[] = $page;
            }
        }
    }

    // Выводим плитки услуг
    if (!empty($pages)) {
        echo '[row v_align="equal" class="services-row" h_align="left"]';

        foreach ($pages as $page) {
            echo '[col span="3" span__sm="12" class="service-item" depth_hover="3"]';
            echo "<a class='service-a' href='" . get_permalink($page->ID) . "'><div class='service-item-block'>";

            // Заголовок
            $title = '<div class="service-item-title">' . $page->post_title . '</div>';
            echo $title;

            // Краткое описание
            $excerpt = $page->post_excerpt;
            if (!empty($excerpt)) {
                $excerpt = '<div class="service-item-excerpt">' . $excerpt . '</div>';
                echo $excerpt;
            }

            // Иконка
            $icon = get_post_meta($page->ID, 'umbrella_post_icon', true);
            if (!empty($icon)) {
                echo '<div class="service-item-icon"><img src="' . $icon . '" height="20px" width="20px"></div>';
            } else {
                echo '<div class="service-item-icon"><img src="/wp-content/uploads/2019/09/plus-icon.png" height="20px" width="20px"></div>';
            }

            echo "</div></a>";
            echo '[/col]';
        }

        echo '[/row]';
    }
    ug_snippet_service_tiles_style(false);
}

function ug_snippet_service_tiles_ip(array $a): void
{
    ug_snippet_service_tiles_by_url(array(
        'https://taxlab.ru/services/intellektualnoe-pravo/obsluzhivane-tovarnyh-znakov/',
        'https://taxlab.ru/services/intellektualnoe-pravo/registracija-tovarnogo-znaka/',
        'https://taxlab.ru/services/intellektualnoe-pravo/registratsiya-litsenzionnogo-dogovora/',
        'https://taxlab.ru/services/services-le/sporyi-v-sfere-intellektualnyih-prav/'
    ));
}

ug_snippet_shortcode('ug_service_tiles_ip', 'ug_snippet_service_tiles_ip');
