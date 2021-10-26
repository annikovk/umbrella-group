<?php

class umbrella_faq
{
    private $css_file = ['/assets/css/blocks/faq.css'];
    private $js_file = ['/assets/js/blocks/faq.js'];
    private $category = "";
    public $atts;
    public $err;

    public function fill_attributes()
    {
        if (isset($this->atts['category'])) {
            $this->category = $this->atts['category'];
            return true;
        } else {
            $this->category = $this->get_category_by_url();
            return true;
        }
    }

    public function generate_shortcode()
    {
        $posts = $this->get_5_faq_posts($this->category);
        if ($this->atts['main']!=""){
            $accordion = $this->get_main_block();
        } else {
            $accordion = $this->get_accordions($posts);
        }
        umbrella_add_custom_css_files($this->css_file);
        umbrella_add_custom_js_files($this->js_file);

        return $accordion;
    }

    private function get_main_block(): string
    {
        $tabs_content['pre'] = '[tabgroup style="line-bottom" type="vertical" nav_style="normal" class="faq-tabs"]';
        $tabs_content['mid1'] = '';
        $tabs_content['mid2'] = "[/tab]";
        $tabs_content['post'] = "[/tabgroup]";

        $shortcode = $tabs_content['pre'];
        $categories = $this->get_meta_values('faq_branch', 'faq');
        foreach ($categories as $cat) {
            $shortcode .= '[tab title="'.$cat.'"]';
            $posts = $this->get_5_faq_posts($cat);
            $shortcode .= $this->get_accordions($posts);
            $shortcode .= $tabs_content['mid2'];
        }
        $shortcode .= $tabs_content['post'];
        return do_shortcode($shortcode);
    }

    // function to grab all possible meta values of the chosen meta key.
    private function get_meta_values($meta_key, $post_type = 'faq')
    {

        $posts = get_posts(
            array(
                'post_type' => $post_type,
                'meta_key' => $meta_key,
                'posts_per_page' => -1,
            )
        );

        $meta_values = array();
        foreach ($posts as $post) {
            if (!in_array(get_post_meta($post->ID, $meta_key, true),$meta_values)) {
                $meta_values[] = get_post_meta($post->ID, $meta_key, true);
            }
        }

        return $meta_values;

    }

    private function get_accordions($posts): string
    {
        $accordion_elements = "";
        if ($this->atts['title'] != "") {
            $title = '<h2>' . $this->atts['title'] . '</h2>';
            $accordion_elements .= $title;
        }
        $accordion_elements .= '<div class="faq-tabs">[accordion]';
        foreach ($posts as $post) {
            $accordion_elements .= <<<EOHTML
                        [accordion-item title="$post->post_title" subtitle=""]
                            [row_inner]
                                [col_inner span__sm="12" align="left"]
                                    $post->post_content
                                [/col_inner]
                            [/row_inner]    
                        [/accordion-item]
            EOHTML;
        }
        $accordion_elements .= '[/accordion]</div>[gap][button text="Смотреть все вопросы" letter_case="lowercase" color="white" style="outline" link="/faq/"][button text="Задать вопрос" link="#faq-form" class=""]';
        $accordion_elements .= <<<EOHTML
                [lightbox id="faq-form" width="400px" padding="0px"]
                    [contact-form-7 id="11210" title="Форма Задать вопрос FAQ"]
                [/lightbox]
        EOHTML;
        return do_shortcode($accordion_elements);
    }

    private function get_5_faq_posts(string $category): array
    {
        $args = array(
            'numberposts' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'faq',
            'meta_key' => 'faq_branch',
            'meta_query' => array(
                array(
                    'key' => 'faq_branch',
                    'value' => $category,
                    'compare' => '=',
                )
            )
        );
        return get_posts($args);
    }

    private function get_category_by_url(): string
    {
        if (strpos($_SERVER['REQUEST_URI'], "services/licensing") !== false) {
            return "Лицензирование";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/register-elimination") !== false) {
            return "Регистрация и ликвидация";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/services-le") !== false) {
            return "Юридические услуги";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/audit") !== false) {
            return "Аудит";
        } else if (strpos($_SERVER['REQUEST_URI'], "services/bukhgalterskie-uslugi") !== false) {
            return "Бухгалтерия";
        } else if (strpos($_SERVER['REQUEST_URI'], "faq") !== false) {
            return "Аудит";
        }
        return "";
    }
}

function faq_block_shortcode($atts)
{
    $shortcode = new umbrella_faq();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('faq_block', 'faq_block_shortcode');