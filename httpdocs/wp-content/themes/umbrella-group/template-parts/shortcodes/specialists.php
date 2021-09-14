<?php

class specialists
{
    private $css_file = ['/assets/css/blocks/specialist.css'];
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
        $tiles = "";
        $posts = $this->get_specialists_posts($this->category);
        foreach ($posts as $post) {
            $tiles .= $this->get_tile($post);
        }
        umbrella_add_custom_css_files($this->css_file);
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
        $education = get_post_meta($post->ID, 'specialist_education', true);
        $description = get_post_meta($post->ID, 'specialist_description', true);
        $name = get_post_meta($post->ID, 'specialist_name', true);
        $full_name = $name . " " . get_the_title($post->ID);
        $experience = get_post_meta($post->ID, 'specialist_experience', true);
        $specialization = get_post_meta($post->ID, 'specialist_specialization', true);
        $contact_form = do_shortcode('[contact-form-7 id="11227" title="Форма Задать вопрос специалисту"]');
        $contact_form = str_replace("{Имя отчество}", $name, $contact_form);
        $lightbox = <<<EOHTML
                [lightbox id="specialist-form-$post->ID" width="400px" padding="0px"]
                    $contact_form
                [/lightbox]
        EOHTML;
        $lightbox = do_shortcode($lightbox);
        $label = $this->get_label($this->category);
        if ($main) $label = "Ведущий " . $label;
        if ($main) {
            $html = <<<EOHTML
            <div class="specialist-tile main">
                
                <div class="specialist-photo">$photo</div>
                <div class="specialist-text">
                    <h3 class="specialist-fio"> $full_name</h3>
                    <div class="specialist-description">
                        <p><strong>$experience опыта</strong></p>
                        <p><strong>$description</strong></p>
                        <p><strong>Образование:</strong> $education</p>
                        <p><strong>Специализация:</strong> $specialization</p>
                    </div>
                    <div class="specialist-contact">
                        <div class="specialist-button">[button text="Задать вопрос" link="#specialist-form-$post->ID" class="" expand="true"]</div> <div class="specialist-button-description">Задайте вопрос и $name ответит в течение 1 рабочего дня. Это бесплатно.</div> 
                    </div>
                </div>
                <div class="label">$label</div>
            </div>
            $lightbox
        EOHTML;
        } else {
            $html = <<<EOHTML
            <div class="specialist-tile">
               
                <div class="specialist-photo">$photo</div>
                <div class="specialist-text">
                 
                    <h3 class="specialist-fio"> $full_name</h3>
                    <div class="specialist-description">
                        <p><strong>$experience опыта</strong></p>
                        <p><strong>Специализация:</strong> $specialization</p>
                    </div>
                </div>
                <div class="label">$label</div>
            </div>
        EOHTML;
        }

        return $html;
    }


    private function get_specialists_posts(string $category): array
    {
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
        return get_posts($args);
    }

    private function get_label($category): string
    {
        if ($category == "Бухгалтерия") return "Бухгалтер";
        if ($category == "Аудит") return "Аудитор";
        if ($category == "Юридические услуги") return "Юрист";
        if ($category == "Лицензирование") return "Специалист";
        if ($category == "Регистрация и ликвидация") return "Специалист";
        return "";
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
        return "";
    }
}

function specialist_block_shortcode($atts)
{
    $shortcode = new specialists();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('specialists', 'specialist_block_shortcode');