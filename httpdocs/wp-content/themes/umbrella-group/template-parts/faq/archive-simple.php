<?php if (have_posts()) :
    // Create IDS
    $ids = array();
    $tags_ids = array();
    $args = array_merge($wp_query->query_vars, ['posts_per_page' => 100]);
    query_posts($args);
endif;
while (have_posts()) : the_post();
    array_push($ids, get_the_ID());
    if (!empty(get_the_tags())) {
        foreach (get_the_tags() as $tag) {
            $tag = $tag->term_id;
            if (empty ($tags_ids[$tag])) {
                $tags_ids[$tag] = 1;
            } else {
                $tags_ids[$tag] += 1;

            }
        }
    }
endwhile;
$posts = get_faq_by_ids($ids);
umbrella_draw_faq_tiles($posts);
function get_faq_by_ids($ids)
{
    $args = array(
        'numberposts' => sizeof($ids),
        'orderby' => 'date',
        'order' => 'DESC',
        'post__in' => $ids,
        'post_type' => 'faq',
    );
    return get_posts($args);
}
function umbrella_draw_faq_tiles($posts)
{
    $metas = [];
    $accordion_element = [];
    foreach ($posts as $post) {
        if (!in_array(get_post_meta($post->ID, 'faq_branch', true), $metas)) {
            array_push($metas, get_post_meta($post->ID, 'faq_branch', true));
        }
        $excerpt = get_the_content($post->ID);
        if (empty($excerpt)) {
            $excerpt = "<span style='color: red'> Нужно добавить ответ</span>";
        }
        $question = <<<EOHTML
                    [accordion-item title="$post->post_title" subtitle=""]
                        [row_inner]
                            [col_inner span__sm="12" align="left"]
                                $post->post_content
                            [/col_inner]
                        [/row_inner]    
                    [/accordion-item]
                    EOHTML;
        $category = get_post_meta($post->ID, 'faq_branch', true);
        if  ($accordion_element[$category] ?? null) {
            $accordion_element[$category] .= $question ;
        } else {
            $accordion_element[$category] = $question ;
        }
    }
    $i=1;
    $hrefs="";
    $accordion_elements="";
    foreach ($metas as $meta) {
        if ($metas[0]==$meta) { $active = 'class="active"';} else {$active='';}
        $hrefs .= <<<EOHTML
            <li $active><a href="#$i">$meta</a></li>
        EOHTML;
        $accordion_elements .= <<<EOHTML
                [section label="$meta" padding="0px"]
                    [scroll_to title="$meta" link="$i" bullet="false"]
                    <h2>$meta</h2>
                    [accordion]
                        $accordion_element[$meta]
                    [/accordion]
                    [gap]
                    [button text="Задать вопрос" link="#contacts-contact-form-lightbox" class="accordion-"]
                [/section]
                [gap]
            EOHTML;

        $i++;

    }

    $template = <<<EOHTML
            
        [row style="collapse"]
            [col span="2" span__sm="12" align="left" class="price-navigation"]
                <ul id="side-menu">
                   $hrefs
                </ul>
            [/col]
            [col span="10" span__sm="12" padding="0px 0px 0px 0px" margin="0px 0px 0px 0px" class="price-content"]
                $accordion_elements
            [/col]
        [/row]
        [block id="contacts-contact-form-lightbox"]
    EOHTML;
    echo do_shortcode($template);
    umbrella_add_custom_css_files(['/assets/css/pages/faq.css']);
}
