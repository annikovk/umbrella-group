<?php

class specialists
{
    private $css_file = ['/assets/css/blocks/specialist.css'];
    private $category = "";
    private $advantages = [
        "Бухгалтерия" => [
            "top" => "ГК Umbrella Group предоставляет услуги бухгалтерского аутсорсинга. <strong>Заниматься бухгалтерией вашей компании будут 3 специалиста.</strong>",
            "bot" => "Это позволит существенно сэкономить на штате и исключить ошибки. Также в нашем штате есть специалисты смежных направлений — аудиторы и юристы. При потребности вы сможете немедленно получить их помощь.",
            "image"=>"/wp-content/uploads/advantages.png"
        ],
        "Аудит" => [
            "top" => "<strong>Наши аттестованные аудиторы — члены СРО аудиторов Ассоциации «Содружество».</strong> Многолетний опыт и профессионализм сотрудников подтверждён сертификатами соответствия.",
            "bot" => "Сертификаты и более 30 лет лидерства на рынке аудиторских услуг позволяют нам грамотно и в кратчайшие сроки провести для вас различные виды аудита. При необходимости подключаются смежные специалисты: бухгалтеры и юристы. Результатом аудиторских услуг ГК Umbrella Group является аудиторское заключение и рекомендации по устранению выявленных недочётов.",
            "image"=>"/wp-content/uploads/advantages.png"
        ],
        "Юридические услуги" => [
            "top" => "Юристы ГК Umbrella Group — <strong>это специалисты с опытом работы более 15 лет.</strong> В штате есть налоговые консультанты, судебные практики и арбитражные управляющие.",
            "bot" => "С уверенностью берёмся за сложные задачи — для нас нет нерешаемых вопросов. В спорных ситуациях подключаем наших смежных специалистов (бухгалтеров и аудиторов) и юридических партнёров из других отраслей. Поэтому мы выигрываем более 90 % дел и занимаем 1 место федерального рейтинга юридических услуг.",
            "image"=>"/wp-content/uploads/advantages.png"
        ],
        "Лицензирование" => [
            "top" => "За годы существования отдела лицензирования ГК Umbrella Group <strong>мы получили более 11 200 лицензий</strong> по разным направлениям деятельности.",
            "bot" => "В штате опытные юристы, досконально знающие процесс лицензирования. Мы следим за всеми изменениями требований, а также на постоянной основе проводим обучения. Помогаем получить лицензию, даже если вам отказывали и возвращали документы. Лицензируем не только в Новосибирске, но и в других городах России.",
            "image" => "/wp-content/uploads/advantages.png"
        ],
        "Регистрация и ликвидация" => [
            "top" => "В нашем штате <strong>команда из 6 юристов</strong> по направлению корпоративного права. Мы готовим документы «под ключ» и сопровождаем вас на всех этапах процедуры.",
            "bot" => "Все работы выполняются в строго оговоренные и указанные договором сроки. Вы можете не переживать, что процесс затянется – мы начинаем заниматься решением вашей проблемы сразу после вашего обращения к нам.",
            "image" => "/wp-content/uploads/advantages.png"
        ],
        "Сделки с недвижимостью" => [
            "top" => "В нашем штате <strong>команда из 6 юристов</strong> по направлению корпоративного права. Мы готовим документы «под ключ» и сопровождаем вас на всех этапах процедуры.",
            "bot" => "Все работы выполняются в строго оговоренные и указанные договором сроки. Вы можете не переживать, что процесс затянется – мы начинаем заниматься решением вашей проблемы сразу после вашего обращения к нам.",
            "image" => "/wp-content/uploads/advantages.png"
        ],
    ];
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
        $tiles .= $this->get_advantages($this->category);
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
        $label = ($main) ? $this->get_main_label($this->category) : $this->get_label($this->category);
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
                        <div class="specialist-button">[button text="Задать вопрос" link="#specialist-form-$post->ID" class="" expand="true"]</div> <div class="specialist-button-description">Задайте вопрос, и $name ответит в течение 1 рабочего дня. Это бесплатно.</div> 
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

    private function get_advantages(string $category): string
    {
        $adv_top = $this->advantages[$category]["top"];
        $adv_bot = $this->advantages[$category]["bot"];
        $adv_img = $this->advantages[$category]['image'];
        $advantages = <<<EOHTML
                <div class="specialists_advantages">
                    
                    <div class="specialists_advantages_top">
                        <div class="specialists_advantages_top_text"><span><img src="/wp-content/uploads/Done_All_Icon_1.png" alt="done-all-icon"> </span> $adv_top</div> 
                        <div class="specialists_advantages_top_img"><img src="$adv_img" alt="advantages-picture"></div>
                    </div>
                    <div class="specialists_advantages_bot">  $adv_bot</div>
                   
                </div>
            EOHTML; 
        return $advantages;
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