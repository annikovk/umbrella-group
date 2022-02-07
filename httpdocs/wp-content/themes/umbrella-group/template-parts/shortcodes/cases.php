<?php

class cases
{
    private $css_files = ['/assets/css/blocks/cases.css', '/assets/css/blocks/cases-and-feedback.css'];
    private $js_files = ['/assets/js/blocks/cases.js'];
    private $category = "";
    private $titles = [
        "Бухгалтерия" => [
            "title" => "Кейсы: проблемы с бухгалтерией и их решения"
        ],
        "Аудит" => [
            "title" => "Кейсы об аудиторской проверке"
        ],
        "Юридические услуги" => [
            "title" => "Кейсы: юридическая практика"
        ],
        "Лицензирование" => [
            "title" => "Кейсы лицензирования"
        ],
        "Регистрация" => [
            "title" => "Кейсы: истории регистрации наших клиентов"
        ],
        "Ликвидация" => [
            "title" => "Кейсы: истории о закрытии компаний"
        ],
        "Общий" => [
            "title" => "Кейсы: истории о клиентах"
        ],
        "cases" => [
            "title" => ""
        ],
    ];
    public $atts;
    public $err;
    private $all_metas = [];
    private $is_cases;

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
        $this->is_cases = $this->category == "cases";
        $title = $this->get_title($this->category);
        $posts = $this->get_cases_posts($this->category);
        foreach ($posts as $post) {
            $this->concat_metas($post);
        }
        $tabs = $this->get_tabs();
        $tiles = "";
        foreach ($this->all_metas as $meta) {
            $visible = ($this->all_metas[0] == $meta) ? "" : "invisible";
            $posts_tiles = "";
            $posts_count = 0;
            foreach ($posts as $post) {
                if (in_array($meta, $this->getPostmeta($post))) {
                    $posts_tiles .= $this->get_tile($post);
                    $posts_count += 1;
                }
            }
            $tiles .= ($this->is_cases || $posts_count < 2) ? "<div class='cases_" . transliterate($meta) . "'>" : '[ux_slider draggable="false" hide_nav="true" nav_style="simple" bullet_style="square" class="cases_' . transliterate($meta) . ' ' . $visible . '"]';
            $tiles .= $posts_tiles;
            $tiles .= ($this->is_cases || $posts_count < 2) ? "</div>" : "[/ux_slider]";
        }
        $html = <<<EOHTML
        [section id='umbrella-cases' bg_color="rgb(249, 249, 249)"]
            [row]
                [col  span="12" span__sm="12"]
                    <div class="newcases">
                        <h2 class="title">$title</h2>
                        <div class="content">
                            $tabs
                            <div class="tiles">$tiles</div>
                        </div>
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
        $metaclases = "cases_" . $metaclases;
        $title = get_the_title($post);
        $logo_url = esc_attr(get_post_meta($post->ID, 'case_logo_url', true));
        $author = esc_attr(get_post_meta($post->ID, 'case_author', true));
        $industry = esc_attr(get_post_meta($post->ID, 'case_industry', true));
        $team = esc_attr(get_post_meta($post->ID, 'case_team', true));
        $issue = esc_attr(get_post_meta($post->ID, 'case_issue', true));
        $solution = esc_attr(get_post_meta($post->ID, 'case_solution', true));
        $proof = esc_attr(get_post_meta($post->ID, 'case_proof', true));
        $proof_title = esc_attr(get_post_meta($post->ID, 'case_proof_title', true));
        if (strlen($proof) > 0 && strlen($proof_title) > 0) {
            $proof_lightbox_id = $post->ID . "-proof-lightbox";
            $proof_lightbox = "[lightbox id={$proof_lightbox_id}] <img src='{$proof}'> [/lightbox]";
            $proof = " <div class='proof hide-for-small'><a href='#{$proof_lightbox_id}'><img src='{$proof}' alt='{$proof_title}'></a>{$proof_title}</div> $proof_lightbox";

        }
        $feedback_text = esc_attr(get_post_meta($post->ID, 'case_feedback_text', true));
        $feedback_url = esc_attr(get_post_meta($post->ID, 'case_feedback_url', true));
        if (strlen($feedback_text) > 0 && strlen($feedback_url) > 0) {
            if (str_contains($feedback_url, "flamp")) {
                $icon = "<img height='20px' width='20px' src='https://flamp.ru/static/assets/brand-logo/svg/f.svg' alt='flamp-logo'>";
                $target = "target='_blank'";
                $feedback = "<div class='feedback hide-for-small'>$icon <a $target href='{$feedback_url}'>{$feedback_text}</a></div> ";
            } else {
                $icon = "";
                $target = "";
                $feedback_lightbox_id = $post->ID . "-feedback-lightbox";
                $feedback_lightbox = "[lightbox id={$feedback_lightbox_id}] <img src='{$feedback_url}'> [/lightbox]";
                $feedback = "<div class='feedback hide-for-small'><a $target href='#{$feedback_lightbox_id}'>$icon {$feedback_text}</a></div> $feedback_lightbox";
            }


        } else {
            $feedback = "";
        }
        $before = ($this->is_cases) ? "" : '[row_inner style="large" v_align="top"][col_inner span="12" span__sm="12" align="left" margin="0px 0px 0px 0px"] ';
        $after = ($this->is_cases) ? "" : '[/col_inner][/row_inner]';
        $html = <<<EOHTML
        $before
            <div class="$metaclases tile $visible">
                <div class="title"> $title</div>
                <div class="company">
                    <div class="logo"><img height='100px' alt="$author" src="$logo_url"></div>
                    <div class="author">$author</div>
                </div>
                <div class="industry"><img alt="Briefcase-image" style="height:20px;" src="/wp-content/uploads/manual_uploads/Briefcase_Icon_1.png"><strong>Отрасль: </strong>$industry</div>
                <div class="team"><img alt="person-image" style="height:20px;" src="/wp-content/uploads/manual_uploads/User_Icon_1.png"><strong>Сотрудники Umbrella Group: </strong>$team</div>
                <div class="timeline">
                    <div class="issue"> <div class="white-background"></div> <div class="title">Проблема </div><div class="content">$issue</div> <div class="arrow show-for-small">→</div></div>
                    <div class="solution"> <div class="title">Результат </div><div class="content"><div class="text">$solution</div> $proof</div></div>
                </div>
                $feedback
            </div>
        $after
        EOHTML;

        return $html;
    }

    private function get_cases_posts(string $category): array
    {
        if ($category == "Общий") {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'case',
                'meta_key' => 'case_common',
                'meta_query' => array(
                    array(
                        'key' => 'case_common',
                        'value' => 'Да',
                        'compare' => '=',
                    )
                )
            );
        } elseif ($category == "cases") {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'case'
            );
        } else {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'case',
                'meta_key' => 'case_branch',
                'meta_query' => array(
                    array(
                        'key' => 'case_branch',
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
        } else if (strpos($_SERVER['REQUEST_URI'], "/about/cases/") !== false) {
            return "cases";
        }
        return "Общий";
    }

    private function get_title(string $category): string
    {
        $title = $this->titles[$category]["title"];
        $html = <<<EOHTML
                <div class="cases_title">
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
            $metaclasses = transliterate($meta);
            $metaclasses = "cases_" . $metaclasses;
            $tabs .= "<li class='$metaclasses $selected'>$meta</li>";
        }
        $tabs .= '</ul>';
        return $tabs;
    }

    /**
     * @param $post
     * @return mixed
     */
    private function getPostmeta($post): array
    {
        $key = $this->getPostmetaKey();
        $postmeta = get_post_meta($post->ID, $key, false);
        return $postmeta;
    }

    private function getPostmetaKey(): string
    {
        if ($this->category == "Общий" || $this->category == "cases") {
            return 'case_branch';
        } else {
            return 'case_category';
        }
    }

    private function get_posts_by_meta(string $meta): array
    {
        $key = $this->getPostmetaKey();
        $args = array(
            'numberposts' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_type' => 'case',
            'meta_key' => $key,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $meta,
                    'compare' => '=',
                )
            )
        );
        return get_posts($args);
    }


}

function cases_block_shortcode($atts)
{
    $shortcode = new cases();
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

add_shortcode('cases', 'cases_block_shortcode');