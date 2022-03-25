<?php

class Service_tiles
{
    public $atts;
    public $err = "Неправильное использование шорткода";

    private $tiles = [
        [
            "parent_id" => 28,
            "title" => "Лицензирование",
            "guarantee_title" => "Гарантия получения лицензии. Без рисков для вашего бизнеса",
            "guarantee_decription" => "С 1990 года получили лицензии для более 2000 компаний. 100% возврат оплаты в случае нашей ошибки",
            "guarantee_icon" => "",
            "span" => 4
        ],
        [
            "parent_id" => 30,
            "title" => "Регистрация !return_caretи ликвидация",
            "guarantee_title" => "Дистанционно, под ключ, в срок",
            "guarantee_decription" => "Без похода в налоговую и к нотариусу. Всё без вашего участия. Можно онлайн! Гарантия срока по договору.",
            "guarantee_icon" => "/wp-content/uploads/Group_7.png",
            "span" => 4
        ],
        [
            "parent_id" => 26,
            "title" => "Бухгалтерские услуги",
            "guarantee_title" => "Минимизируем все риски",
            "guarantee_decription" => "Учёт ведут профессиональные бухгалтеры, которые постоянно повышают свою квалификацию. Гарантия защиты от ошибок.",
            "guarantee_icon" => "",
            "span" => 4
        ],
        [
            "parent_id" => 12792,
            "title" => "Сделки с!return_caretнедвижимостью",
            "guarantee_title" => "Гарантия чистоты проводимой сделки. Без рисков, под ключ.",
            "guarantee_decription" => "Проверяем юридическую чистоту объекта. С вами работает опытный юрист в сфере недвижимости. Гарантия на проведенную сделку в случае возникновения споров.",
            "guarantee_icon" => "",
            "span" => 4
        ],
        [
            "parent_id" => 32,
            "title" => "Юридические услуги",
            "guarantee_title" => "Гарантия и страхование",
            "guarantee_decription" => "<ul><li> Гарантируем сроки по договору.</li><li> Риски застрахованы.</li><li> Бесплатный прогноз ситуации до заключения договора.</li></ul>",
            "guarantee_icon" => "",
            "span" => 4
        ],
        [
            "parent_id" => 34,
            "title" => "Аудиторские !return_caret услуги",
            "guarantee_title" => "Риски застрахованы на сумму более 50 млн рублей",
            "guarantee_decription" => "После проверки — подробный отчёт, отражающий «узкие места» в работе организации и способы их устранения. Постдоговорное обслуживание.",
            "guarantee_icon" => "",
            "span" => 4
        ]
    ];

    function generate_shortcode()
    {
        $html = '[row][col span="12"]<div class="main-services-tile-grid">';
        foreach ($this->tiles as $tile) {
            $html .= $this->get_tile($tile);
        }
        $html .= '</div>[/col][/row]
                <script type="text/javascript" src="/wp-content/themes/umbrella-group/assets/js/blocks/services-tiles.js"></script>
                ';
        umbrella_add_custom_css_files(['/assets/css/blocks/services-tiles.css']);
        return $html;
    }

    function get_tile($tile)
    {
        $title = str_replace("!return_caret", "<br />", $tile['title']);
        $pages_html = '';
        $parent_id = $tile["parent_id"];
        if (isset($tile["guarantee_icon"]) && strlen($tile["guarantee_icon"]) > 0) {
            $icon = $tile["guarantee_icon"];
        } else {
            $icon = "/wp-content/uploads/bookmark-services.png";
        }

        $parent_url = get_page_link($parent_id);
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'menu_order',
            'hierarchical' => 0,
            'exclude' => '',
            'include' => '',
            'meta_key' => '',
            'meta_value' => '',
            'authors' => '',
            'child_of' => 0,
            'parent' => $parent_id,
            'exclude_tree' => '',
            'number' => '5',
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish',
        );
        $pages = get_pages($args);
        $span = $tile["span"];

        foreach ($pages as $page) {
            $pages_html .= "<li><a href='$page->guid'>$page->post_title</a></li>";
        }
        $html = <<<EOHTML
                            <div class="main-services-tile">
                                <div class="main-services-pages">
                                        <div class='category-block'>
                                            <h3 class="category-title red-on-block-hover"><a href="$parent_url">$title</a></h3>
                                            <div class="arrow-on-hover hide-for-small"></div>
                                            <ul class="children-list">
                                                $pages_html
                                            </ul>
                                            <a class='hide-for-small red-on-block-hover show-all-link' href="$parent_url">показать все</a>
                                            <a class='show-for-small show-more-pages' data-before='↓ показать '>услуги</a>
                                        </div>
                                </div>
                                <div class="main-services-guarantee">
                                    <div class="service-tile-guarantee-icon"><img width="34px" alt="bookmark-icon" src="$icon"></div>
                                    <div class="service-tile-guarantee-title">{$tile['guarantee_title']}</div>
                                    <div class="service-tile-guarantee-description">{$tile['guarantee_decription']}</div>
                                </div>
                            </div>
                    EOHTML;
        return $html;
    }

    private function fill_variable(&$variable, $attribute): bool
    {
        if (strlen($this->atts[$attribute]) > 0) {
            $variable = $this->atts[$attribute];
            return true;
        } else {
            return false;
        }
    }

}

function service_tile_shortcode($atts)
{
    $shortcode = new Service_tiles();
    return $shortcode->generate_shortcode();
}

add_shortcode('service_tiles', 'service_tile_shortcode');