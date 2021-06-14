<?php
function print_breadcrumbs_shortcode($atts)
{
    return get_breadcrumbs();

}

add_shortcode('print_breadcrumbs', 'print_breadcrumbs_shortcode');

function umbrella_draw_header_with_breadcrumbs()
{
    if (get_theme_mod('default_title', 0) && (!is_front_page() || is_paged())) {
        $breadcrumbs = get_breadcrumbs();
        $title = umbrella_get_the_title();
        $html=<<<EOHTML
        <header class="entry-header">
            <div class="page-header-wrapper">
                <div class="page-title light simple-title">
                    <div class="page-title-inner container align-bottom">
                        $breadcrumbs
                        <div class="title-wrapper flex-col text-left medium-text-center">
                            <h1 class="entry-title mb-0">$title</h1>
                        </div>
                    </div><!-- flex-row -->
                </div><!-- .page-title -->
            </div>
        </header><!-- .entry-header -->
        EOHTML;
        return $html;
     }
}

function get_breadcrumbs()
{

    $delimiter = "<span style='color:#ef1716'>›</span>";
    $name = 'Главная'; //text for the 'Home' link
    $home = get_bloginfo('url');
    $currentBefore = '';
    $currentAfter = '';
    $breadcrumb = '<div id="breadcrumb" style="font-size: 0.7em;">';
    if (!is_home() && !is_front_page() || is_paged()) {

        global $post;

        $breadcrumb .= '<a href="' . $home . '">' . $name . '</a> ' . $delimiter . ' ';

        if (is_post_type_archive('akcii')){
            $breadcrumb .= get_post_type_object(get_post_type())->label;
        }
        elseif (is_category()) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            $breadcrumb .= '<a href="'.get_permalink( get_option( 'page_for_posts' ) ).'">'.'Блог</a> '.$delimiter.' ';
            if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ')) ;
            $breadcrumb .= $currentBefore . '';
            $breadcrumb .= umbrella_get_the_title();
            $breadcrumb .= '' . $currentAfter;

        } elseif (is_day()) {
            $breadcrumb .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            $breadcrumb .= '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
            $breadcrumb .= $currentBefore . get_the_time('d') . $currentAfter;

        } elseif (is_month()) {
            $breadcrumb .= '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
            $breadcrumb .= $currentBefore . get_the_time('F') . $currentAfter;

        } elseif (is_year()) {
            $breadcrumb .= $currentBefore . get_the_time('Y') . $currentAfter;

        } elseif (is_single() && !is_attachment()) {
            $cat = get_the_category();
            $cat = $cat[0];
            //$breadcrumb .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            $breadcrumb .= umbrella_get_post_type($delimiter);
            $breadcrumb .= $currentBefore;
            $breadcrumb .= get_the_title();
            $breadcrumb .= $currentAfter;

        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            $breadcrumb .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
            $breadcrumb .= '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
            $breadcrumb .= $currentBefore;
            $breadcrumb .= get_the_title();
            $breadcrumb .= $currentAfter;

        } elseif (is_page() && !$post->post_parent) {
            $breadcrumb .= $currentBefore;
            $breadcrumb .= get_the_title();
            $breadcrumb .= $currentAfter;

        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) $breadcrumb .= $crumb . ' ' . $delimiter . ' ';
            $breadcrumb .= $currentBefore;
            $breadcrumb .= get_the_title();
            $breadcrumb .= $currentAfter;

        } elseif (is_search()) {
            $breadcrumb .= $currentBefore . "Search results for '" . get_search_query() . "'" . $currentAfter;

        } elseif (is_tag()) {
            $breadcrumb .= $currentBefore . "Записи с тэгом '";
            single_tag_title();
            $breadcrumb .= "'" . $currentAfter;

        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            $breadcrumb .= $currentBefore . 'Articles posted by ' . $userdata->display_name . $currentAfter;

        } elseif (is_404()) {
            $breadcrumb .= $currentBefore . 'Error 404' . $currentAfter;
        }

        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) $breadcrumb .= ' (';
            $breadcrumb .= __('Page') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) $breadcrumb .= ')';
        }
    } elseif (is_home()){
        $breadcrumb .= '<a href="' . $home . '">' . $name . '</a> ' . $delimiter . ' ';
        $breadcrumb .= get_the_title( get_option('page_for_posts', true) );
    }
    $breadcrumb .= '</div>';
    return $breadcrumb;
}

function umbrella_get_the_title()
{
    if (is_category()) {
        return ''.single_cat_title('',false).'';
    } elseif (is_tag()){
        return '#'.single_tag_title("", false);
    } elseif (is_home()){
        return get_the_title( get_option('page_for_posts', true) );
    } elseif (is_post_type_archive() && get_post_type_object(get_post_type())->name == "akcii") {
        return get_post_type_object(get_post_type())->label;
    } else {
        return get_the_title();
    }

}

function umbrella_get_post_type($delimiter) {
    //print_r(get_post_type_object(get_post_type()));
    $post_label = (get_post_type_object(get_post_type())->label);
    $post_name = (get_post_type_object(get_post_type())->name);
    switch ($post_name) {
        case "cases":
            $post_link=get_the_permalink(8).'cases/';
            return '<a href="' . get_the_permalink(8) . '"> ' . get_the_title(8) . '</a> ' . $delimiter . ' <a href="'.$post_link.'">'.$post_label.'</a> ' . $delimiter . ' ';
        case "post":
            $post_link='/blog/';
            return '<a href="' .$post_link.'">Блог</a> ' . $delimiter . ' ';
        case "akcii":
            $post_link='/akcii/';
            return '<a href="' .$post_link.'">Акции</a> ' . $delimiter . ' ';
    }
     //return $post_name . ' ' . $delimiter . ' ';

}
