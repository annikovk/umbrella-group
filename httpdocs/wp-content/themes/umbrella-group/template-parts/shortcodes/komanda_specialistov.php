<?php

class komanda_specialistov
{
    private $css_file = ['/assets/css/blocks/komanda_specialistov.css'];
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
        $posts = $this->get_specialists_posts($this->category);
        $categories = $this->get_categories_of_posts($posts);

        $tabs = $this->get_tabs($categories);
        $tiles = $this->get_tiles($posts);

        $html = <<<EOHTML
        [section id='trouble_2022'  padding="0px"]
            [row]
                [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                    $tabs
                    $tiles
                [/col]
           [/row]
        [/section]    
        EOHTML;

        umbrella_add_custom_css_files($this->css_file);
        return $html;
    }


    private function get_tiles($posts): string
    {
        $tiles = "";
        foreach ($posts as $post) {
            $tiles .= $this->get_tile($post);
        }
        return $tiles;
    }

    private function get_tile($post): string
    {
        if (get_post_meta($post->ID, 'specialist_main', true) == "Да") $main = true; else $main = false;
        if (get_the_post_thumbnail_url($post->ID) == "") {
            $image_attributes = wp_get_attachment_image_src(11223, 'full');
            $photo = '<img src="' . $image_attributes[0] . '"  />';
        } else {
            $photo = get_the_post_thumbnail($post->ID);
        }
        $category = get_post_meta($post->ID, "specialist_branch", true);
        $description = get_post_meta($post->ID, 'specialist_description', true);
        $name = get_post_meta($post->ID, 'specialist_name', true);
        $full_name = $name . " " . get_the_title($post->ID);
        $experience = get_post_meta($post->ID, 'specialist_experience', true);
        $specialization = get_post_meta($post->ID, 'specialist_specialization', true);
        $achievements = $this->get_achievement(get_post_meta($post->ID, 'specialist_achievements', true));
        $filter_class = $this->get_filter_class($category);

        $label = ($main) ? $this->get_main_label($category) : $this->get_label($category);
        if ($main) {
            $html = <<<EOHTML
            <div class="specialist-tile main $filter_class">
                
                <div class="specialist-photo">$photo</div>
                <div class="specialist-text">
                    <h3 class="specialist-fio"> $full_name</h3>
                    <div class="specialist-description">
                        <p><strong>$experience опыта</strong></p>
                        <p><strong>$description</strong></p>
                        <p><strong>$achievements</strong></p>
                        <p><strong>Специализация:</strong> $specialization</p>
                    </div>
                </div>
                <div class="label">$label</div>
            </div>
        EOHTML;
        } else {
            $html = <<<EOHTML
            <div class="specialist-tile $filter_class">
                <div class="specialist-photo">$photo</div>
                <div class="specialist-text">
                    <h3 class="specialist-fio"> $full_name</h3>
                    <div class="specialist-description">
                        <p><strong>$experience опыта</strong></p>
                        <p>$achievements</p>
                        <p><strong>Специализация:</strong> $specialization</p>
                    </div>
                </div>
                <div class="label">$label</div>
            </div>
        EOHTML;
        }

        return $html;
    }

    private function get_tabs($categories): string
    {
        $html = "<ul class='tabs'>";
        foreach ($categories as $category) {
            $filter_class = $this->get_filter_class($category);
            $html .= "<li class='$filter_class'>$category</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    private function get_specialists_posts(string $category): array
    {
        if ($category == "") {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'date',
                'order' => 'ASC',
                'post_type' => 'specialist',
                'meta_key' => 'specialist_branch',
            );
        } else {
            $args = array(
                'numberposts' => 5,
                'orderby' => 'date',
                'order' => 'ASC',
                'post_type' => 'specialist',
                'meta_key' => 'specialist_branch',
                'meta_query' => array(
                    array(
                        'key' => 'specialist_branch',
                        'value' => $category,
                        'compare' => '=',
                    )
                )
            );
        }
        return get_posts($args);
    }

    private function get_categories_of_posts(array $posts): array
    {
        $categories = array();
        foreach ($posts as $post) {
            $post_categories = get_post_meta($post->ID, "specialist_branch");
            foreach ($post_categories as $category) {
                if (!in_array($category, $categories)) {
                    array_push($categories, $category);
                }
            }
        }
        return $categories;
    }

    private function get_main_label($category): string
    {
        if ($category == "Аудит" || $category == "Юридические услуги") return "Директор направления";
        return "Руководитель отдела";
    }

    private function get_label($category): string
    {
        if ($category == "Бухгалтерия") return "Бухгалтер";
        if ($category == "Аудит") return "Аудитор";
        if ($category == "Юридические услуги") return "Юрист";
        if ($category == "Лицензирование") return "Специалист";
        if ($category == "Регистрация и ликвидация") return "Специалист";
        if ($category == "Сделки с недвижимостью") return "Специалист";
        return "";
    }

    private function get_achievement($achievement): string
    {
        $ru_month = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
        $en_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $year = date("Y") - 1;
        $monthyear = str_replace($en_month, $ru_month, date("F Y"));
        $achievement = str_replace("{текущий минус 1}", $year, $achievement);
        $achievement = str_replace("{месяц год}", $monthyear, $achievement);
        return $achievement;
    }

    private function get_filter_class($category)
    {
        $category = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $category);
        $category = str_replace(" ", "-", $category);
        return $category;
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
        } else if (strpos($_SERVER['REQUEST_URI'], "services/sdelki-s-nedvizhimostyu") !== false) {
            return "Сделки с недвижимостью";
        }
        return "";
    }

}

function komanda_specialistov_block_shortcode($atts)
{
    $shortcode = new komanda_specialistov();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('komanda_specialistov', 'komanda_specialistov_block_shortcode');