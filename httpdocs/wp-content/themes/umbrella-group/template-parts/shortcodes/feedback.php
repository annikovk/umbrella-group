<?php

class feedback
{
    private $css_files = ['/assets/css/blocks/feedback.css', '/assets/css/blocks/cases-and-feedback.css'];
    private $js_files = ['/assets/js/blocks/feedback.js'];
    private $category = "";
    private $titles = [
        "Бухгалтерия" => [
            "title" => "Отзывы клиентов"
        ],
        "Аудит" => [
            "title" => "Отзывы клиентов"
        ],
        "Юридические услуги" => [
            "title" => "Отзывы клиентов"
        ],
        "Лицензирование" => [
            "title" => "Отзывы клиентов"
        ],
        "Регистрация" => [
            "title" => "Отзывы клиентов"
        ],
        "Ликвидация" => [
            "title" => "Отзывы клиентов"
        ],
        "Общий" => [
            "title" => "Отзывы клиентов"
        ],
    ];
    public $atts;
    public $err;
    private $all_metas = [];

    public function fill_attributes()
    {
        if (isset($this->atts['branch'])) {
            $this->category = $this->atts['branch'];
            return true;
        } else {
            $this->category = $this->get_category_by_url();
            return true;
        }
    }

    public function generate_shortcode()
    {
        $title = $this->get_title($this->category);
        $tiles = "";
        $posts = $this->get_feedback_posts($this->category);
        foreach ($posts as $post) {
            $this->concat_metas($post);
        }
        $tabs = $this->get_tabs();
        $tiles = "";
        foreach ($this->all_metas as $meta) {
            $visible = ($this->all_metas[0] == $meta) ? "" : "invisible";
            $tiles .= '[ux_slider draggable="false" hide_nav="true" nav_style="simple" bullet_style="square" class="feedback_' . transliterate($meta) . ' ' . $visible . '"]';
            foreach ($posts as $post) {
                if (in_array($meta, $this->getPostmeta($post))) {
                    $tiles .= $this->get_tile($post);
                }
            }
            $tiles .= "[/ux_slider]";
        }

        $html = <<<EOHTML
        [section id='umbrella-feedback' bg_color="rgb(249, 249, 249)"]
            [row]
                [col  span="12" span__sm="12"]
                    <div class="feedback">
                        <h2 class="title">$title</h2>
                        <div class="content">
                            $tabs
                            <div class="tiles">$tiles</div>
                        </div>
                        <div class="check-all show-for-small"><a href="https://taxlab.ru/about/feedback/ ">Посмотреть все отзывы</a></div>
                    </div>
                    
                [/col]
            [/row]
        [/section]
        EOHTML;
        umbrella_add_custom_css_files($this->css_files);
        umbrella_add_custom_js_files($this->js_files);
        return $html;
    }

    private function get_tile($post): string
    {
        $visible = in_array_r($this->all_metas[0], get_post_meta($post->ID)) ? "" : "invisible";
        $postmeta = $this->getPostmeta($post);
        $metaclases = "";
        foreach ($postmeta as $post_meta) {
            $metaclases .= transliterate($post_meta) . " ";
        }
        $metaclases = "feedback_" . $metaclases;
        $title = get_the_title($post);
        $logo_url = esc_attr(get_post_meta($post->ID, 'feedback_logo_url', true));
        $author = get_post_meta($post->ID, 'feedback_author', true);
        $quote = esc_attr(get_post_meta($post->ID, 'feedback_quote', true));
        $proof = esc_attr(get_post_meta($post->ID, 'feedback_proof', true));
        $proof_title = esc_attr(get_post_meta($post->ID, 'feedback_proof_title', true));
        if (strlen($proof) > 0 && strlen($proof_title) > 0) {
            $proof_lightbox_id = $post->ID . "-proof-lightbox";
            $proof_lightbox = "[lightbox id={$proof_lightbox_id}] <img src='{$proof}'> [/lightbox]";
            $proof = <<<EOHTML
                <div class='proof hide-for-small'>
                    <a href='#{$proof_lightbox_id}'>
                        <img src='{$proof}' alt='{$proof_title}'>
                    </a>
                    <div class='title'>{$proof_title}</div>
                </div> 
                <div class='proof show-for-small'>
                    <a href='#{$proof_lightbox_id}'>
                        Читать отзыв на бланке
                    </a>
                </div> 
                $proof_lightbox   
            EOHTML;

        }
        $feedback_url_text = esc_attr(get_post_meta($post->ID, 'feedback_url_text', true));
        $feedback_url = esc_attr(get_post_meta($post->ID, 'feedback_url', true));
        $feedback = "";
        if (strlen($feedback_url_text) > 0 && strlen($feedback_url) > 0) {
            if (str_contains($feedback_url, "flamp")) {
                $icon = "<img width='20px' src='https://flamp.ru/static/assets/brand-logo/svg/f.svg' alt='flamp logo'>";
                $target = "target='_blank'";
                $feedback = "<div class='link'>$icon <a $target href='{$feedback_url}'>{$feedback_url_text}</a></div> ";
            }
        }
        $quote_symbol = '<div class="quote_symbol"></div>';
        $html = <<<EOHTML
        [row_inner padding="0px 0 0px 0" style="collapse" v_align="top" ]
        [col_inner  span="12" span__sm="12"]
            <div class="$metaclases tile $visible">
                $proof
                <div class="text">
                    <div class="title"> $title </div>
                    <div class="quote"> $quote_symbol $quote <div class="hide-for-small">$feedback</div> </div>
                    <div class="show-for-small">$feedback</div>
                </div>
                <div class="company">
                    <div class="logo"><img alt="$author" src="$logo_url"></div>
                    <div class="author">$author</div>
                </div>
            </div>
        [/col_inner]
        [/row_inner]
        EOHTML;

        return $html;
    }

    private function get_feedback_posts(string $category): array
    {
        if ($category == "Общий") {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'feedback',
                'meta_key' => 'feedback_common',
                'meta_query' => array(
                    array(
                        'key' => 'feedback_common',
                        'value' => 'Да',
                        'compare' => '=',
                    )
                )
            );
        } else {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'feedback',
                'meta_key' => 'feedback_branch',
                'meta_query' => array(
                    array(
                        'key' => 'feedback_branch',
                        'value' => $category,
                        'compare' => '=',
                    )
                )
            );
        }
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
        }
        return "Общий";
    }

    private function get_title(string $category): string
    {
        $title = $this->titles[$category]["title"];
        $html = <<<EOHTML
                <div class="feedback_title">
                    $title
                </div>
            EOHTML;
        return $html;
    }

    private function concat_metas($post)
    {
        $postmeta = $this->getPostmeta($post);
        foreach ($postmeta as $post_meta) {
            if (!in_array($post_meta, $this->all_metas)) {
                array_push($this->all_metas, $post_meta);
            }
        }
    }

    private function get_tabs(): string
    {
        if (sizeof($this->all_metas) < 2) {
            return "";
        }
        sort($this->all_metas);
        $tabs = '<ul class="tabs">';
        foreach ($this->all_metas as $meta) {
            $selected = $meta == $this->all_metas[0] ? "selected" : "";
            $metaclasses = "feedback_" . transliterate($meta);

            $tabs .= "<li class='$metaclasses $selected'>$meta</li>";
        }
        $tabs .= '</ul>';
        return $tabs;
    }

    /**
     * @param $post
     * @return mixed
     */
    private function getPostmeta($post): mixed
    {
        if ($this->category == "Общий") {
            $key = 'feedback_branch';
        } else {
            $key = 'feedback_category';
        }
        $postmeta = get_post_meta($post->ID, $key, false);
        return $postmeta;
    }


}

function feedback_block_shortcode($atts)
{
    $shortcode = new feedback();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    if (isset($_GET['expvar'])) {
        $expVar1 = $_GET['expvar'];
    } else {
        $expVar1 = "0";
    }
    if ($expVar1 == "1") {
        return $shortcode->generate_shortcode();
    } else {
        return "";
    }
}

add_shortcode('feedback', 'feedback_block_shortcode');