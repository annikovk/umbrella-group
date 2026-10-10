<?php
/**
 * Сниппет insert-php #786 «Услуги | Плитки услуг внутри категории» → [ug_service_tiles].
 *
 * Плитки — опубликованные дочерние страницы ($parent_id, по умолчанию текущая)
 * по menu_order; описание — post_excerpt, иконка — мета umbrella_post_icon.
 * Атрибуты: parent_id, include, exclude (id через запятую). Без include у
 * .services-row верхний отступ 30px. Стоит в 51 записи разделов услуг.
 *
 * <style> печатается при каждом вызове, как у сниппета: перенос в файл —
 * отдельная правка с проверкой каскада (стили сниппета стоят в body после
 * ug-main и перебивают его).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_service_tiles(array $a): void
{
    $parent_id = $a['parent_id'] ?? null;
    if (empty($parent_id)) {
        $parent_id = url_to_postid(get_permalink());
    }
    $include = $a['include'] ?? null;
    $setbg = false;
    if (empty($include)) {
        $include = '';
        $setbg = true;
    }
    // ID страниц, которые не надо выводить плиткой в этой категории (через запятую)
    $exclude = $a['exclude'] ?? null;
    if (empty($exclude)) {
        $exclude = '';
    }

    $args = array(
        'sort_order' => 'ASC',
        'sort_column' => 'menu_order',
        'hierarchical' => 0,
        'exclude' => $exclude,
        'include' => $include,
        'meta_key' => '',
        'meta_value' => '',
        'authors' => '',
        'child_of' => 0,
        'parent' => $parent_id,
        'exclude_tree' => '',
        'number' => '',
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish',
    );
    $pages = get_pages($args);
    if (!empty($pages)) {
        echo '[row v_align="equal" class="services-row" h_align="left"]';
        foreach ($pages as $page) {
            echo '[col span="3" span__sm="12" class="service-item"  depth_hover="3"]';
            echo "<a class='service-a' href=" . get_page_link($page) . "><div class='service-item-block'>";
            $title = '<div class="service-item-title">' . $page->post_title . '</div>';
            echo $title;
            $excerpt = $page->post_excerpt;
            if (!empty($excerpt)) {
                $excerpt = '<div class="service-item-excerpt">' . $page->post_excerpt . '</div>';
            }
            echo $excerpt;
            $icon = get_post_meta($page->ID, 'umbrella_post_icon', true);
            if (!empty($icon)) {
                echo '<div class="service-item-icon">' . '<img src="' . $icon . '" height="20px" width="20px">' . '</div>';
            } else {
                echo '<div class="service-item-icon"><img src="/wp-content/uploads/2019/09/plus-icon.png" height="20px" width="20px"></div>';
            }
            echo "</div></a>";
            echo '[/col]';
        }
        echo '[/row]';
    }
    ug_snippet_service_tiles_style($setbg);
}

/**
 * <style> плиток услуг — общий у [ug_service_tiles], [ug_service_tiles_ip] и
 * [ug_service_tiles_tax] (у сниппетов 786, 29221, 30355 он был одинаковый,
 * кроме верхнего отступа ряда).
 */
function ug_snippet_service_tiles_style(bool $padding_top): void
{
    ?>
<style>
    .service-item > .col-inner {
        background-color: #fff;
        height: 250px;
    }

    .services-row {
        <?php if ($padding_top) echo 'padding-top: 30px;' ?>
		margin-bottom: 40px;
    }

    .service-item-block {
        width: 100%;
        height: 100%;
		border: solid 2px #f1f1f1;
			
    }

    .service-item-block:hover {
        cursor: pointer;
		border: solid 2px #fff;


    }

    .service-item-block:hover > .service-item-title {
        color: #ef1716;
    }

    .service-item-block:hover > .service-item-icon {
        visibility: hidden;
        opacity: 0;
        transition: visibility 0.2s, opacity 0.2s linear;
    }

    .service-item-title {
        font-weight: 600;
        padding-top: 25px;
        font-size: 1em;
        padding-left: 20px;
        padding-right: 40px;
    }
    .service-item-icon {
        position: absolute;
        left: 20px;
        bottom: 20px;
        visibility: visible;
        opacity: 1;
    }

    .service-item-block:hover > .service-item-excerpt {
        visibility: visible;
        opacity: 1;
    }
    .service-item-excerpt {
        visibility: hidden;
        opacity: 0;
        transition: visibility 0.2s, opacity 0.2s linear;
        font-size: 0.8em;
        color: #b2b2b2;
        line-height: 1.3em;
        position: absolute;
        left: 20px;
        bottom: 20px;
        right: 40px;
    }
	.service-a {
		color: #1a1a1a;
	}
</style>
<?php
}

ug_snippet_shortcode('ug_service_tiles', 'ug_snippet_service_tiles');
