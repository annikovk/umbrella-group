<?php
/**
 * Плитки записей/отзывов/клиентов и табы-фильтры: umbrella_draw_tiles() и
 * спутники. Контракт спеки §2.4: umbrella_draw_tiles и umbrella_draw_filter_tabs
 * зовут шаблоны и PHP-сниппеты insert-php из БД — сигнатуры не менять.
 *
 * Перенесено из functions.php:266-893, 904-1055 (WS-0), в WS-6:
 *  - CSS (было три <style> внутри PHP) — в assets/css/blocks/tiles-clients.css,
 *    tiles-feedback.css, filter-tabs.css; <link> печатается там же, где стоял
 *    <style>, поэтому порядок каскада тот же. Файлов три, а не один: клиентский
 *    и отзывный CSS задают разные значения одним селекторам (.feedback-image img,
 *    button.accordion, .feedback-excerpt …), и на страницах, где был только один
 *    из них, общий файл поменял бы вёрстку;
 *  - ветка 'cases' удалена — вызывающие передают только blogposts/clients/feedback;
 *  - копии массива месяцев → ug_ru_date() из inc/helpers.php.
 */

/**
 * <link> на CSS плиток прямо в месте вывода (один раз за запрос).
 * $name — файл assets/css/blocks/<name>.css, хэндл ug-blocks-<name>.
 */
function ug_tiles_print_style(string $name): void
{
    $rel    = 'assets/css/blocks/' . $name . '.css';
    $handle = 'ug-blocks-' . $name;
    if (!wp_style_is($handle, 'registered')) {
        wp_register_style($handle, get_theme_file_uri($rel), [], (string) filemtime(get_theme_file_path($rel)));
    }
    wp_print_styles($handle);
}

function umbrella_draw_tiles($posts, $type)
{
    if ($type == 'blogposts') {
        echo '<div style="padding-top:48px" id="tiles">';
        $tile = "";
        //echo count($posts);

        foreach ($posts as $post) {
            $title = $post->post_title;
            $title = '<div class="cases-item-header"> ' . $title . '</div>';

//            print_r($post);
            if ($post->post_type == "cases") {
                $tag = get_the_terms($post->ID, 'cases_tags');
                if (!empty($tag)) {
                    $tag = '<a href="' . get_term_link($tag[0], 'cases_tags') . '" >#' . $tag[0]->name . '</a>';
                }
                if (!empty(get_the_terms($post->ID, 'cases_industry'))) {
                    $category_classes = get_the_terms($post->ID, 'cases_industry');
                } else {
                    $category_classes = array('');
                }

                $category_classes_string = ' ';
                foreach ($category_classes as $category_class) {
                    $category_classes_string = $category_classes_string . ' ' . $category_class->slug;

                }
            } elseif ($post->post_type == "post") {
                $tag = get_the_tags($post->ID);
                if (!empty($tag)) {
                    $tag = '<a href="' . get_term_link($tag[0], 'cases_tags') . '" >#' . $tag[0]->name . '</a>';
                }
                if (!empty(get_the_tags($post->ID))) {
                    $category_classes = get_the_tags($post->ID);

                } else {
                    $category_classes = array('');
                }

                $category_classes_string = ' ';
                foreach ($category_classes as $category_class) {
                    if (!empty($category_class)){
                        $category_classes_string = $category_classes_string . ' ' . $category_class->slug;
                    }
                }
            }

            $date = ug_ru_date("j F", $post->post_date);

            if (!isset($tag)){ $tag = "";}
            $date_and_tag = '<div class="case-item-date-and-tag">' . $date . '&emsp;&emsp;&emsp;&emsp;' . $tag . '</div>';


            $excerpt = get_the_excerpt($post->ID);
            if (empty($excerpt)) {
                $excerpt = "<span style='color: red'> Нужно добавить краткое описание записи</span>";
            }
            if (strlen($excerpt) > 150) {
                $excerpt = implode(' ', array_slice(explode(' ', $excerpt), 0, 10)) . '...';
            }
            $excerpt = '<div class="cases-excerpt">' . $excerpt . '</div>';

            $thumbnail = get_the_post_thumbnail($post->ID);
            $thumbnail = '<div class="cases-item-image">' . $thumbnail . '</div>';

            // $permalink = get_post_permalink($post->ID);

            $permalink = urldecode(get_permalink($post->ID));

            if (!isset($category_classes_string)){ $category_classes_string = "";}
            $tile = $tile . '[col_inner span="6" span__sm="12" align="left" margin="0px 0px 8px 0px" class="all' . $category_classes_string . '"]<div class="cases-tile" ><a href="' . $permalink . '">' . $thumbnail . $title . $excerpt . $date_and_tag . '</a></div>[/col_inner]';


        }

        $tiles = '[row_inner padding="0px 0 0px 0" style="normal" v_align="top" ]' . $tile . '[/row_inner]';
        echo do_shortcode($tiles);
        echo '</div>';
        ug_enqueue_style('/assets/css/blocks/cases-tiles.css');
    } elseif ($type == 'clients') {
        foreach ($posts as $post) {


            if (!empty(get_the_terms($post->ID, 'client_industry'))) {
                $category_classes = get_the_terms($post->ID, 'client_industry');
            } else {
                $category_classes = array('');
            }
            $category_classes_string = 'all ';
            foreach ($category_classes as $category_class) {
                $category_classes_string = $category_classes_string . ' ' . $category_class->slug;

            }
            echo '<div class="col client-item ' . $category_classes_string . ' medium-3 small-6 large-3">';
            echo '<div class="col-inner" style="border: solid 2px #f1f1f1;">';
            if (!empty(get_the_excerpt($post->ID))) {
                $isFeedback = true;
            } else {
                $isFeedback = false;
            }
            if ($isFeedback) {
                $block_id = uniqid('', false);
                echo '<a class="client-item-block" href="#' . $block_id . '">';
                //echo "<div class='client-item-block' onclick='window.location=`" . get_permalink($post->ID) . "`;'>";
                echo '<div class="feedback-exists-icon"><img src="/wp-content/uploads/2019/09/icon-people.png" height="20px" width="20px"></div>';
                echo '<div class="feedback-exists-text">Читать отзыв клиента →</div>';
                $feedback_tile = make_feedback_tile($post, false);
                //$feedback_tile_buttons = '<button class="mfp-close lightbox-close-button" >x</button>';
                $feedback_tile_buttons = '<button onclick="eventFire();" class="lightbox-want-the-same-button" >Хочу также</button>';
                $lightbox = '[lightbox id="' . $block_id . '" width="1200px" padding="0px 60px 60px 20px"] ' . $feedback_tile . $feedback_tile_buttons . ' [/lightbox]';
            } else {
                //echo "<div class='client-item-block' onclick='window.location=`" . get_post_meta($post->ID, 'umbrella_link_to_client_website', true) . "`;'>";
                echo "<a class='client-item-block' href='" . esc_url((string) get_post_meta($post->ID, 'umbrella_link_to_client_website', true)) . "'>";
            }
            $thumbnail = get_the_post_thumbnail($post->ID);
            echo '<div  class="client-item-image"><span class="client-item-image-helper"></span>' . $thumbnail . '</div>';
            echo '</a>';
            $title = '<div class="client-item-title">' . $post->post_title . '</div>';
            $title = $title . '<div class="client-item-industry">' . esc_html((string) get_post_meta($post->ID, 'umbrella_client_personal_industry', true)) . '</div>';
            echo $title;
            echo '</div>';
            echo '</div>';
            echo $lightbox;
        }
        ?>
        <script>
            function eventFire() {
                jQuery("button.mfp-close").click();
                jQuery("#open-leave-request-contact-form-lightbox").click();
            }
        </script>
        <?php ug_tiles_print_style('tiles-clients'); ?>
        <?php
    } elseif ($type == 'feedback') {
        // Рисуем отдельный отзыв во всплывающем окне, если на входе послан только один отзыв
        if (!empty($posts[1])) {
            echo '<div id="tiles">';
            foreach ($posts as $post) {
                $tile = make_feedback_tile($post);
                echo do_shortcode($tile);
            }
            echo '</div>';
        }
        ?>
        <?php ug_tiles_print_style('tiles-feedback'); ?>
        <?php
    } else {
        echo "posts type to draw were defined incorrectly";
    }


}

function make_feedback_tile($post, $border = true)
{
    //print_r($post);

    $scan = get_post_meta($post->ID, 'umbrella_feedback_scan', true);
    $scan = '[ux_image id="' . $scan . '" width="100" height="312px" lightbox="true" depth_hover="3" depth="1" ]';

    $title = $post->post_title;

    $industry = get_post_meta($post->ID, 'umbrella_client_personal_industry', true);
    $industry = '<div class="feedback-industry">' . $industry . '</div>';
    $titleAndIndustry = '<div class="feedback-item-header"> ' . $title . $industry . '</div>';

    $content = $post->post_content;
    if (!empty($content)) {
        $content = '<div class="feedback-item-content">' . $content . '</div>';
        $content = '<button class="accordion"></button>' . $content;
    }

    $excerpt = get_the_excerpt($post->ID);
    if (empty($excerpt)) {
        $excerpt = "<span style='color: red'> Нужно добавить отрывок отзыва</span>";
    }
    $excerpt = '<div class="feedback-excerpt">' . $excerpt . '</div>';

    $thumbnail = get_the_post_thumbnail($post->ID);
    $thumbnail = '<div class="feedback-image">' . $thumbnail . '</div>';

    $category_classes = get_the_terms($post->ID, 'client_industry');
    $category_classes_string = ' ';

    if (!empty($category_classes)) {
        foreach ($category_classes as $category_class) {
            $category_classes_string = $category_classes_string . ' ' . $category_class->slug;

        }
    }

    $date = ug_ru_date("j F", $post->post_date);

    $permalink = get_post_permalink($post->ID);


    if (!empty($scan)) {
        $tile = '[col_inner span="3" padding="40px 40px 40px 44px" margin="0px 0px 0px 0px" class="hide-for-medium"]' . $scan . '[/col_inner]' . '[col_inner span="9" span__sm="12" align="left" ]' . $titleAndIndustry . $thumbnail . $excerpt . $content . '[/col_inner]';
    } else {
        $tile = '[col_inner span="12" span__sm="12" align="left" margin="0px 0px -30px 0px"]' . $titleAndIndustry . $thumbnail . $excerpt . $content . '[/col_inner]';
    }
    if ($border) {
        return '<div style="border: solid 2px #f9f9f9;">[row_inner padding="40px 20px 40px 20px" style="collapse" v_align="top" class="feedback-row all ' . $category_classes_string . '"]' . $tile . '[/row_inner] </div>';
    } else {
        return '<div style="margin-top: 40px;">[row_inner padding="40px 0 40px 0" style="collapse" v_align="top" class="feedback-row all ' . $category_classes_string . '"]' . $tile . '[/row_inner] </div>';
    }

}

function umbrella_draw_filter_tab($slug, $name, $count)
{
    if ($slug == 'all') {
        $autofocus = 'autofocus';
    } else $autofocus = '';
    return '<button class="btn-filter" name="' . $slug . '"data-rel="' . $slug . '" ' . $autofocus . '> ' . $name . ' <span class="size-of-filter-tab"> ' . $count . '</span></button>';
}

function in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function transliterate($textcyr = null, $textlat = null)
{
    $cyr = array(
        'я', 'ж', 'ч', 'щ', 'ш', 'ю', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь',
        'Я', 'Ж', 'Ч', 'Щ', 'Ш', 'Ю', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', ' ');
    $lat = array(
        'ya', 'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x',
        'Ya', 'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', '_');
    if ($textcyr) return str_replace($cyr, $lat, $textcyr);
    else if ($textlat) return str_replace($lat, $cyr, $textlat);
    else return null;
}

function umbrella_draw_filter_tabs($tabs, $posts, $type = 'blogposts')
{


    echo '<div class="toolbar">';


    if ($type == 'clients') {
        echo umbrella_draw_filter_tab('all', 'Все клиенты', sizeof($posts));
        foreach ($tabs as $tab) {
            echo umbrella_draw_filter_tab($tab->slug, $tab->name, $tab->count);
        }
    }
    if ($type == 'feedback') {
        echo umbrella_draw_filter_tab('all', 'Все отзывы', sizeof($posts));
        foreach ($tabs as $tab) {

            echo umbrella_draw_filter_tab($tab['slug'], $tab['name'], $tab['count']);
        }
    }
    if ($type == 'blogposts') {
        echo umbrella_draw_filter_tab('all', 'Все записи', sizeof($posts));
        foreach ($tabs as $id => $count) {
            $tab = get_tag($id);
            echo umbrella_draw_filter_tab($tab->slug, $tab->name, $count);
        }
    }
    echo '</div>';

    ?>
    <script>
        jQuery(function () {
            var selectedClass = "";
            jQuery(".btn-filter").click(function () {
                selectedClass = jQuery(this).attr("data-rel");
                jQuery(this).focus();
                <?php  if ($type == 'clients') {
                echo 'jQuery("#tiles .clients-row>div").not("." + selectedClass).fadeOut().removeClass(\'scale-anm\');';
            } else {
                echo 'jQuery("#tiles>div>div").not("." + selectedClass).fadeOut().removeClass(\'scale-anm\');';
            }?>

                setTimeout(function () {
                    jQuery("." + selectedClass).fadeIn().addClass('scale-anm');
                    //jQuery("#portfolio").fadeTo(300, 1);
                }, 300);

            });
        });
    </script>
    <?php ug_tiles_print_style('filter-tabs'); ?>
    <?php
}


/**
 * Записи по списку ID, свежие первыми (архивы плитками, template-parts/posts/archive-2-col.php).
 * Бывшие get_post_by_ids() и get_akcii_by_ids() из двух копий шаблона.
 */
function ug_get_posts_by_ids(array $ids, string $post_type = 'post'): array
{
    return get_posts(array(
        'numberposts' => count($ids),
        'orderby' => 'date',
        'order' => 'DESC',
        'post__in' => $ids,
        'post_type' => $post_type,
    ));
}

/**
 * Плитки архива акций /akcii/ (бывш. template-parts/akcii/archive-2-col.php).
 *
 * Срок «До 01.ММ.ГГГГ» — первое число следующего месяца (ug_akcii_deadline()).
 * Ветка «+12 месяцев» для акции 26853 в старом коде не срабатывала никогда:
 * сравнивался объект WP_Post с числом ($post == 26853 → false и PHP Notice),
 * поэтому её нет и здесь — вывод тот же.
 */
function umbrella_draw_akcii_tiles($posts)
{
    echo '<div style="padding-top:48px" id="tiles">';
    $tile = "";

    foreach ($posts as $post) {
        $date = "До " . ug_akcii_deadline();

        $date_and_tag = '<div class="case-item-date-and-tag">' . $date . '</div>';


        $excerpt = get_the_excerpt($post->ID);
        if (empty($excerpt)) {
            $excerpt = "<span style='color: red'> Нужно добавить краткое описание записи</span>";
        }
        if (strlen($excerpt) > 150) {
            $excerpt = implode(' ', array_slice(explode(' ', $excerpt), 0, 10)) . '...';
        }
        $excerpt = '<div class="cases-excerpt">' . $excerpt . '</div>';
        $title_and_excerpt = '<div class="cases-item-header"> ' . $post->post_title . '</div>' . $excerpt ;
        $thumbnail = get_the_post_thumbnail($post->ID);
        $thumbnail = '<div class="cases-item-image">' . $thumbnail . '</div>';

        $permalink = urldecode(get_permalink($post->ID));

        $tile = $tile . '[col_inner span="6" span__sm="12" align="left" margin="0px 0px 8px 0px" class="all"]<div class="cases-tile tile-akcii" ><a href="' . $permalink . '">' . $thumbnail . $title_and_excerpt  . $date_and_tag . '</a></div>[/col_inner]';
    }

    $tiles = '[row_inner padding="0px 0 0px 0" style="normal" v_align="top" ]' . $tile . '[/row_inner]';
    echo do_shortcode($tiles);
    echo '</div>';
    ug_enqueue_style('/assets/css/blocks/cases-tiles.css');
}

/**
 * Две свежие записи под записью («Читать еще») или две другие акции под акцией
 * (template-parts/posts/layout-right-sidebar.php). Бывшие get_similar_posts()
 * и akcii_get_similar_posts() из двух копий шаблона.
 */
function ug_draw_similar_posts(string $post_type = 'post'): void
{
    $is_akcii = $post_type === 'akcii';
    $args = array(
        'order'   => 'DESC',
        'orderby' => 'date',
        'exclude' => $is_akcii ? get_post()->ID : '',
        'include' => '',
        'numberposts' => 2,
        'category' => '',
        'post_type' => $post_type,
    );
    ?>
<div class="large-12 col">
    <?php if ($is_akcii) : ?>
    <h2 class="feedback-subheader">Другие акции</h2>

    <p class="feedback-subheader-right">
        <a style="text-decoration: underline;" href="/akcii/"> Посмотреть все</a></p>
    <?php else : ?>
    <p class="h2 for-h2 feedback-subheader">Читать еще</p>

    <p class="feedback-subheader-right">
        <a style="text-decoration: underline;" href="/about/cases/"> Посмотреть все</a></p>
    <?php endif; ?>
    <?php
    umbrella_draw_tiles(get_posts($args), 'blogposts');
    ?>
</div>
<?php
}
