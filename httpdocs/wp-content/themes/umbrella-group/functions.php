<?php
//OMA INTELSIB CODE
$search_systems = array('google', 'yandex', 'mail.ru', 'rambler', 'bing');
$important_source = false;
$source_array = array(
    'medium' => null,
    'source' => null,
    'campaign' => null,
    'term' => null,
    'content' => null
);
if (isset($_GET['utm_medium'])) {
    $important_source = true;
    $source_array['medium'] = $_GET['utm_medium'];
    $source_array['source'] = isset($_GET['utm_source']) ? $_GET['utm_source'] : null;
    $source_array['campaign'] = isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : null;
    $source_array['term'] = isset($_GET['utm_term']) ? $_GET['utm_term'] : null;
    $source_array['content'] = isset($_GET['utm_content']) ? $_GET['utm_content'] : null;
} else if (isset($_GET['yclid'])) {
    $important_source = true;
    $source_array['medium'] = 'cpc';
    $source_array['source'] = 'yandex';
} else if (isset($_GET['gclid'])) {
    $important_source = true;
    $source_array['medium'] = 'cpc';
    $source_array['source'] = 'google';
} else if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'yabs.yandex')) {
    $important_source = true;
    $source_array['medium'] = 'cpc';
    $source_array['source'] = 'yandex';
} else if (!empty($_SERVER['HTTP_REFERER'])) {
    preg_match('/(?:https?:\/\/)?(.*?)[:\/$]/', $_SERVER['HTTP_REFERER'], $referrer);
    $referrer = preg_match('/\.(.*\.\w*)/', $referrer[1], $new_referrer) ? $new_referrer[1] : $referrer[1];
    preg_match('/(?:https?:\/\/)?(.*?)(:|\/|$)/', $_SERVER['HTTP_HOST'], $host);
    $host = preg_match('/\.(.*\.\w*)/', $host[1], $new_host) ? $new_host[1] : $host[1];
    if ($host != $referrer) {
        $important_source = true;
        $source_array['source'] = $referrer;
        //Если реферал содержит маркет, проставляем канал контекст
        if (strpos($referrer, 'market.yandex') > -1) {
            $source_array['medium'] = 'cpc';
        } else {
            //Если реферал - не поисковая система проставляем канал рефералы
            $source_array['medium'] = 'referral';
            foreach ($search_systems as $search_system) {
                if (strpos($referrer, $search_system) > -1) {
                    $organic = true;
                    $source_array['medium'] = 'organic';
                    $source_array['source'] = $search_system;
                    break;
                }
            }
        }
    } else {
        $source_array['source'] = 'none';
        $source_array['medium'] = 'direct';
    }
} else {
    $source_array['source'] = 'none';
    $source_array['medium'] = 'direct';
}

if (!$important_source && isset($_COOKIE['source_cookie'])) {
    $source_cookie = unserialize(stripslashes($_COOKIE['source_cookie']));
    if ($source_cookie && !empty($source_cookie['medium'])) {
        $source_array = $source_cookie;
    }
}

setcookie("source_cookie", serialize($source_array), time() + 3600 * 24 * 30, "/");
$html = "Источник - $source_array[source], Канал - $source_array[medium]";
$html .= $source_array['campaign'] ? ", Кампания - $source_array[campaign]" : null;
$html .= $source_array['term'] ? ", Объявление - $source_array[term]" : null;
$html .= $source_array['content'] ? ", Ключевое слово - $source_array[content]" : null;
$_SESSION['traffic_source'] = $html;

add_filter('wpcf7_form_elements', 'do_shortcode');

function omatraffic_cf7_func()
{

    return $GLOBALS['html'];
}

function omacid_cf7_func()
{
    $cid = "";
    if (isset($_COOKIE['_gaexp'])) {
        $cid = $_COOKIE['_gaexp'];
    }
    return $cid;
}

add_shortcode('omatraffic', 'omatraffic_cf7_func');
add_shortcode('omacid', 'omacid_cf7_func');

add_filter('wpcf7_mail_components', 'do_shortcode_mail', 10, 3);
function do_shortcode_mail($components, $contactForm, $mailComponent)
{
    if (isset($components['body'])) {
        $components['body'] = do_shortcode($components['body']);
    }

    return $components;
}

//END OMA INTELSIB CODE

add_filter('use_block_editor_for_post', '__return_false', 10);
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
// Add custom Theme Functions here

// Webinar registration form
include_once(get_theme_file_path() . '/includes/webinar/init.php');
//Sending report
include_once(get_theme_file_path() . '/includes/webinar/report.php');
// Webinar registration form

function remove_redirect_guess_404_permalink($redirect_url)
{
    if (is_404() && !isset($_GET['p']))
        return false;
    return $redirect_url;
}

add_filter('redirect_canonical', 'remove_redirect_guess_404_permalink');

function add_custom_fonts()
{
    wp_enqueue_style('Inter-font', '/wp-content/themes/umbrella-group/fonts/inter.css');
    wp_enqueue_style('fonts-configuration', '/wp-content/themes/umbrella-group/fonts/fonts-configuration.css');
}

add_action('wp_enqueue_scripts', 'add_custom_fonts');

require get_theme_file_path() . '/includes/custom_post_types/client.php';
require get_theme_file_path() . '/includes/custom_post_types/akcii.php';
require get_theme_file_path() . '/includes/draw_header_with_breadcrumbs.php';
require get_theme_file_path() . '/includes/about_subheader.php';
require get_theme_file_path() . '/template-parts/shortcodes/akcii.php';
require get_theme_file_path() . '/template-parts/shortcodes/su_welcome_screen.php';
require get_theme_file_path() . '/template-parts/shortcodes/service-tiles.php';
require get_theme_file_path() . '/template-parts/shortcodes/su_welcome_screen_conditions.php';
// Hook : to get content with s3elected AB tests variants only
require get_theme_file_path() . '/umbrella_filter_ab_tests.php';
add_filter('the_content', 'umbrella_filter_ab_tests');
require get_theme_file_path() . '/css/css_collector.php';
function get_umbrella_customizations(){
    $customizations = new umbrella_customization();
    echo $customizations->get_umbrella_custom_css().$customizations->get_umbrella_custom_scripts();
}
add_action('wp_footer','get_umbrella_customizations', 100);

//** *Enable upload for webp image files.*/
function webp_upload_mimes($existing_mimes) {
    $existing_mimes['webp'] = 'image/webp';
    return $existing_mimes;
}
add_filter('mime_types', 'webp_upload_mimes');

function umbrella_draw_tiles($posts, $type)
{
    if ($type == 'blogposts') {
        $ru_month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

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

            $date = str_replace($en_month, $ru_month, date("j F", strtotime($post->post_date)));

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


            $tile = $tile . '[col_inner span="6" span__sm="12" align="left" margin="0px 0px 8px 0px" class="all' . $category_classes_string . '"]<div class="cases-tile" ><a href="' . $permalink . '">' . $thumbnail . $title . $excerpt . $date_and_tag . '</a></div>[/col_inner]';


        }

        $tiles = '[row_inner padding="0px 0 0px 0" style="normal" v_align="top" ]' . $tile . '[/row_inner]';
        echo do_shortcode($tiles);
        echo '</div>';
        umbrella_add_custom_css_files(['/assets/css/blocks/cases-tiles.css']);
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
                echo "<a class='client-item-block' href='" . get_post_meta($post->ID, 'umbrella_link_to_client_website', true) . "'>";
            }
            $thumbnail = get_the_post_thumbnail($post->ID);
            echo '<div  class="client-item-image"><span class="client-item-image-helper"></span>' . $thumbnail . '</div>';
            echo '</a>';
            $title = '<div class="client-item-title">' . $post->post_title . '</div>';
            $title = $title . '<div class="client-item-industry">' . get_post_meta($post->ID, 'umbrella_client_personal_industry', true) . '</div>';
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
        <style>
            .lightbox-want-the-same-button {
                position: absolute;
                bottom: 0;
                top: unset;
                height: 56px;
                background-color: #1a1a1a;
                font-weight: 300;
                color: white;
                margin: 0;
                left: 0;

            }

            .lightbox-want-the-same-button:hover {
                background-color: #ef1716;
            }

            .mfp-content .lightbox-close-button {
                position: absolute;
                bottom: 0;
                top: unset;
                width: 56px;
                height: 56px;
                background-color: #1a1a1a;
                opacity: unset;
                mix-blend-mode: unset;
            }

            .mfp-content .lightbox-close-button:hover {
                background-color: #ef1716;
            }

            .client-item {
                padding-bottom: 100px;
            }

            .client-item > .col-inner {
                background-color: #fff;
                height: 250px;
            }

            @media screen and (max-width: 849px) {
                .client-item {
                    padding-bottom: 150px;
                }

                .client-item > .col-inner {
                    background-color: #fff;
                    max-height: 150px;
                }
            }


            a.client-item-block {
                display: inline-block;
                width: 100%;
                height: 100%;
            }

            .client-item-block:hover {
                cursor: pointer;
            }

            .client-item-block:hover > .client-item-title {
                color: #ef1716;
            }

            .client-item-block:hover > .feedback-exists-icon {
                visibility: hidden;
                opacity: 0;
                transition: visibility 0.2s, opacity 0.2s linear;
            }

            .client-item-block:hover > .feedback-exists-text {
                visibility: visible;
                opacity: 1;
                z-index: 1;
            }

            .feedback-industry {
                font-weight: 300;
                font-size: 0.65em;
                color: #b2b2b2;
            }

            .feedback-excerpt {
                font-size: 1.35em;
                line-height: 1.5em;
                position: relative;
                width: 90%;
                font-weight: 300;
                padding-top: 1em;
                padding-bottom: 1em;
            }

            .feedback-item-content {
                /*max-height: 0px;*/
                overflow: hidden;
                transition: max-height 0.2s ease-in-out;
            }

            .feedback-item-header {
                width: 86.55%;
                display: table-cell;
                font-size: 1.35em;
                font-weight: 600;
                line-height: 1.45;
            }

            .feedback-image img {
                max-height: 64px;
                height: 64px;
                width: auto;
            }

            @media screen and (max-width: 849px) {
                .feedback-image img {
                    display: none;
                }

                .feedback-excerpt {
                    font-size: 14px;
                }

                .feedback-item-content {
                    display: none;
                }
            }


            .feedback-image {
                -webkit-filter: grayscale(100%);
                filter: grayscale(100%);
                position: absolute;
                right: 0;
                top: 40px;
            }

            button.accordion {

                display: none;
            }

            .feedback-exists-icon {
                position: absolute;
                left: 20px;
                bottom: 20px;
                visibility: visible;
                opacity: 1;
            }

            .feedback-exists-text {
                visibility: hidden;
                opacity: 0;
                transition: visibility 0.2s, opacity 0.2s linear;
                text-align: center;
                color: #fff;
                height: 52px;
                padding-top: 0.8em;
                padding-left: 10px;
                background-color: #ec1c24;
                width: 100%;
                position: absolute;
                left: 0;
                bottom: 0;
            }

            @media screen and (max-width: 849px) {
                .feedback-exists-text {
                    font-size: 14px;
                }
            }


            .client-item-image {
                position: absolute;
                display: inline-block;
                text-align: center;
                width: 100%;
                height: 100%;
                padding: 40px 40px 40px 40px;
            }

            @media screen and (max-width: 849px) {
                .client-item-image {
                    padding: unset;
                }
            }


            .client-item-image-helper {
                display: inline-block;
                height: 100%;
                vertical-align: middle;
            }

            .client-item-title {

                font-weight: 600;
                padding-top: 10px;
                font-size: 1em;
            }

            .client-item-industry {
                font-size: 0.8em;
                color: #b2b2b2;
            }

        </style>
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
        <style>
            /** feedback **/
            button.accordion {
                position: absolute;
                right: -20px;
                bottom: -20px;
                color: white;
                background-color: #1a1a1a;
                width: 56px;
                height: 56px;
                transition: background-color 0.2s linear;
            }

            button.accordion:hover {
                background-color: #ec1c23;
            }

            button.accordion:after {
                content: "↓";
            }

            button.accordion.is-open {
                background-color: #1a1a1a;
            }

            button.accordion.is-open:after {
                content: "↑";
            }

            .feedback-subheader {
                display: table-cell;
                width: 100%;
            }

            .feedback-subheader-right {
                display: table-cell;
                white-space: nowrap;
                text-align: right;
                width: 20%
            }

            p.feedback-subheader-right a:hover:after {
                color: #ec1c23;
                opacity: 1;
                margin-left: 0px;
                -webkit-transition: all 0.1s;
                transition: all 0.1s;
            }

            p.feedback-subheader-right a:after {
                content: " →";
                color: #ec1c23;
                opacity: 0;
                margin-left: -30px;
                -webkit-transition: all 0.1s;
                transition: all 0.1s;
            }

            p.feedback-subheader-right a:hover {
                margin-left: -30px;
                -webkit-transition: all 0.1s;
                transition: all 0.1s;
            }

            p.feedback-subheader-right a {
                margin-left: 0px;
                -webkit-transition: all 0.1s;
                transition: all 0.1s;
            }

            .feedback-image img {
                max-height: 64px;
                height: 64px;
                width: auto;
            }

            @media screen and (max-width: 849px) {
                .feedback-image img {
                    display: none;
                }
            }

            .feedback-image {
                -webkit-filter: grayscale(100%);
                filter: grayscale(100%);
                position: absolute;
                right: 40px;
                top: 40px;
            }

            .feedback-industry {
                font-weight: 300;
                font-size: 0.65em;
                color: #b2b2b2;
            }

            .feedback-excerpt {
                font-size: 1.35em;
                line-height: 1.5em;
                position: relative;
                width: 90%;
                font-weight: 300;
                padding-top: 1em;
                padding-bottom: 1em;
            }

            .feedback-item-content {
                max-height: 0px;
                padding-right: 112px;

                overflow: hidden;

                transition: max-height 0.2s ease-in-out;
            }

            .feedback-item-header {
                width: 86.55%;
                display: table-cell;
                font-size: 1.35em;
                font-weight: 600;
                line-height: 1.45;
            }


        </style>
        <script>
            var accordions = document.getElementsByClassName("accordion");

            for (var i = 0; i < accordions.length; i++) {
                accordions[i].onclick = function () {
                    this.classList.toggle('is-open');

                    var content = this.nextElementSibling;
                    if (content.style.maxHeight) {
                        // accordion is currently open, so close it
                        content.style.maxHeight = null;

                    } else {
                        // accordion is currently closed, so open it
                        content.style.maxHeight = content.scrollHeight + "px";
                    }
                }
            }
        </script><?php
    } elseif ($type == 'cases') {
        $ru_month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        echo '<div id="cases-tiles">';
        $tile = "";
        foreach ($posts as $post) {
            $title = $post->post_title;
            $title = '<div class="cases-item-header"> ' . $title . '</div>';

            $tag = get_the_terms($post->ID, 'cases_tags');
            if (!empty($tag)) {
                $tag = '<a href="' . get_term_link($tag[0], 'cases_tags') . '" >#' . $tag[0]->name . '</a>';
            }
            $date = str_replace($en_month, $ru_month, date("j F", strtotime($post->post_date)));

            $date_and_tag = '<div class="case-item-date-and-tag">' . $date . '&emsp;&emsp;&emsp;&emsp;' . $tag . '</div>';

            $excerpt = get_the_excerpt($post->ID);
            if (empty($excerpt)) {
                $excerpt = "<span style='color: red'> Нужно добавить краткое описание кейса</span>";
            }
            $excerpt = '<div class="cases-excerpt">' . $excerpt . '</div>';

            $thumbnail = get_the_post_thumbnail($post->ID);
            $thumbnail = '<div class="cases-item-image">' . $thumbnail . '</div>';

            if (!empty(get_the_terms($post->ID, 'cases_industry'))) {
                $category_classes = get_the_terms($post->ID, 'cases_industry');
            } else {
                $category_classes = array('');
            }

            $category_classes_string = ' ';
            foreach ($category_classes as $category_class) {
                $category_classes_string = $category_classes_string . ' ' . $category_class->slug;

            }
            $permalink = get_post_permalink($post->ID);

            $tile = $tile . '[col_inner span="6" span__sm="12" align="left" margin="0px 0px 42px 0px" class="all' . $category_classes_string . '"]<div class="cases-tile" onclick="window.location=\'' . $permalink . '\'" >' . $thumbnail . $title . $excerpt . $date_and_tag . '</div>[/col_inner]';


        }
        $tiles = '[row_inner padding="0px 0 0px 0" style="large" v_align="top" ]' . $tile . '[/row_inner]';
        echo do_shortcode($tiles);
        echo '</div>';


        ?>
        <style>
            /** cases **/

            .cases-tile {
                height: 281px;
                cursor: pointer;
            }

            .cases-tile:hover .cases-item-header {
                color: #ec1d23;
            }

            .cases-tile:hover .case-item-date-and-tag {
                opacity: 0;
                bottom: 20%;
                -webkit-transition: all 0.3s; /* Safari prior 6.1 */
                transition: all 0.3s;

            }

            .cases-tile:hover .cases-excerpt {
                opacity: 1;
                bottom: 10%;
                -webkit-transition: all 0.3s; /* Safari prior 6.1 */
                transition: all 0.3s;

            }

            .cases-item-image img {
                height: 100%;
                vertical-align: top;

            }

            .cases-item-image {
                position: absolute;
                height: 100%;
                width: 100%;
                text-align: right;
                right: 0;

            }

            .cases-item-image::after {
                display: block;
                position: absolute;
                background-image: linear-gradient(to left, rgba(255, 255, 255, 0) 0, #fff 50%);
                top: 0;
                height: 100%;
                width: 100%;
                content: '';

            }

            .case-item-date-and-tag {
                font-size: 0.8em;
                position: absolute;
                bottom: 10%;
                font-weight: 300;
                color: #b2b2b2;
                -webkit-transition: all 0.3s; /* Safari prior 6.1 */
                transition: all 0.3s;
            }

            .cases-excerpt {
                opacity: 0;
                bottom: 0%;
                font-size: 0.8em;
                line-height: 1.5em;
                position: absolute;
                width: 60%;

                -webkit-transition: all 0.3s; /* Safari prior 6.1 */
                transition: all 0.3s;

            }

            .cases-item-header {
                max-width: 60%;
                position: relative;
                font-size: 18px;
                font-weight: 600;
                line-height: 1.45;

            }


        </style>
        <?php
    } else {
        echo "posts type to draw were defined incorrectly";
    }


}

function umbrella_date_of_post($id)
{
    $ru_month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    echo get_the_date('j F Y', $id);
    $date = str_replace($en_month, $ru_month, date("j F Y", strtotime(get_the_date($id))));
    return $date;
}

function make_feedback_tile($post, $border = true)
{
    //print_r($post);

    $ru_month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
    $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

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

    $date = str_replace($en_month, $ru_month, date("j F", strtotime($post->post_date)));

    $permalink = get_post_permalink($post->ID);


    if (!empty($scan)) {
        $tile = '[col_inner span="3" padding="40px 40px 40px 44px" margin="0px 0px 0px 0px" class="hide-for-medium"]' . $scan . '[/col_inner]' . '[col_inner span="9" span__sm="12" align="left" ]' . $titleAndIndustry . $thumbnail . $excerpt . $content . '[/col_inner]';
    } else {
        $tile = '[col_inner span="12" span__sm="12" align="left" margin="0px 0px -30px 0px"]' . $titleAndIndustry . $thumbnail . $excerpt . $content . '[/col_inner]';
    }
    if ($border) {
        return '<div style="border: solid 2px #f9f9f9; margin-top: 40px;">[row_inner padding="40px 0 40px 0" style="collapse" v_align="top" class="feedback-row all ' . $category_classes_string . '"]' . $tile . '[/row_inner] </div>';
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
    <style>
        .size-of-filter-tab {
            color: #b2b2b2;
        }

        .btn-filter:hover .size-of-filter-tab {
            color: #fff;
        }
    </style>
    <?php
}

?>
