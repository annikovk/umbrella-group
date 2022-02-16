<?php

class umbrella_feedback
{
    public $err = "error";
    public $atts;

    public function fill_attributes(): bool
    {
        return true;
    }

    public function generate_shortcode()
    {
        $clients = $this->get_clients();
        if (strlen($clients) == 0) {
            return null;
        }
        if (in_array(get_queried_object_id(), array(1215, 1213, 32, 5679, 1222, 6149, 10961, 52, 137, 6075, 1230, 1203, 1236, 3370, 135, 4647, 146, 1224, 2))) {
            return "";
        }
        $html = <<<EOHTML
            [section bg_color="rgb(249, 249, 249)"]
                [row]
                    [col span__sm="12" padding="20px 0px 0px 0px"]
                        <h2>Клиенты о нас</h2>
                        <p class="feedback-subheader">Несколько историй от людей, которые доверили нам свой бизнес.</p>
                        <p class="feedback-subheader-right"><a style="text-decoration: underline;" href="/about/feedback/">Все отзывы</a></p>
                        [gap]
                        [ux_slider bg_color="rgb(255, 255, 255)" draggable="false" hide_nav="true" nav_style="simple" bullet_style="square"]
                            $clients
                        [/ux_slider]
                    [/col]
                [/row]
            [/section]
        EOHTML;
        return do_shortcode($html);
    }

    private function get_clients()
    {
        //show feedback from main on the register-eliminaton and licensing pages
        $shown_category = $this->get_category();
        if ($shown_category == 'register-elimination' || $shown_category == 'licensing') {
            $tag = get_term_by('slug', 'main', 'client_category');
        } else {
            $tag = get_term_by('slug', $shown_category, 'client_category');
        }
        $wp_client_options = get_option('client_shortcode_options');
        if ($wp_client_options !== null &&
            strlen((string)$wp_client_options) > 0 &&
            in_array(get_the_ID(), $wp_client_options['excluded_pages']) ||
            isset($this->atts['ab_test']) &&
            !in_array($this->atts['ab_test'], umbrella_get_ab_test_tags())
        ) {
            return "";
        }
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
        $html = "";
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
                $html .= '[row_inner padding="40px 0 40px 0" style="large" v_align="top" class="feedback-row"]';
                if (!empty($scan)) {
                    $html .= <<<EOHTML
                        [col_inner span="3" margin="0px 0px 0px 0px" class="hide-for-medium"] 
                         $scan 
                        [/col_inner] 
                        [col_inner span="9" span__sm="12" align="left" margin="0px 0px -30px 0px"] 
                            $titleAndIndustry 
                            $logo 
                            $excerpt 
                        [/col_inner]
                    EOHTML;
                } else {
                    $html .= <<<EOHTML
                        [col_inner span="12" span__sm="12" align="left" margin="0px 0px -30px 0px"]
                            $titleAndIndustry 
                            $logo 
                            $excerpt 
                        [/col_inner]
                    EOHTML;
                }
                $html .= "[/row_inner]";

            }
        }
        return $html;
    }

    private function get_category()
    {
        $atts = $this->atts;
        if (!empty($atts['category'])) {
            return $atts['category'];
        } else if (strpos($_SERVER['REQUEST_URI'], "services/licensing") !== false) {
            return "licensing";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/audit/") !== false) {
            return "audit";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/bukhgalterskie-uslugi/") !== false) {
            return "bukhgalterskie-uslugi";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/register-elimination") !== false) {
            return "register-elimination";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/services-le") !== false) {
            return "services-le";
        }
        return "";
    }
}

function umbrella_feedback_block_shortcode($atts)
{
    $shortcode = new umbrella_feedback();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('umbrella_feedback', 'umbrella_feedback_block_shortcode');




