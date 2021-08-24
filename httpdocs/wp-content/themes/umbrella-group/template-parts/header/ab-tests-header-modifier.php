<?php


class AB_tests_header_modifier
{
    private $theme_mods;

    function __construct()
    {
        $this->theme_mods = [
            "umbrella_ab_test4_variant2" => [
                "topbar_elements_left" => ["nav-top"],
                "topbar_bg" => "#ffffff",
                'logo_max_width' => 360,
                "logo_width" => 420,
                "header_elements_left" => ["ratings"],
                "header_elements_right" => ["contact"],
                "header_height" => 130,
                "header_height_mobile" => "70px",
                "header_elements_bottom_left" => ["nav"],
                "nav_menu_locations" => [
                    "top_bar_nav" => wp_get_nav_menu_object('top-bar-menu-ab-test-4-variant-2')->term_id,
                    "primary" => wp_get_nav_menu_object('main-menu-ab-test4-variant2')->term_id,
                    "primary_mobile" => wp_get_nav_menu_object('mobile-ab-test4-variant2')->term_id,
                ],
                "mobile_sidebar" => [
                    "logo-mobile_ab_test4_variant2", "nav", "contact-mobile_ab_test4_variant2"
                ]
            ],
            "umbrella_ab_test4_variant1" => [
                "topbar_elements_left" => [],
                "topbar_bg" => "#f9f9f9",
                'logo_max_width' => 200,
                "logo_width" => 200,
                "header_elements_left" => ["nav-top"],
                "header_elements_right" => ["contact"],
                "header_height" => 90,
                "header_height_mobile" => "70px",
                "header_elements_bottom_left" => ["nav"],
                "nav_menu_locations" => [
                    "top_bar_nav" => wp_get_nav_menu_object('top-bar-menu')->term_id,
                    "primary" => wp_get_nav_menu_object('main-menu')->term_id,
                    "primary_mobile" => wp_get_nav_menu_object('mobile')->term_id,
                ],
                "mobile_sidebar" => [
                    "logo-mobile", "nav", "contact-mobile_ab_test4_variant1"
                ]
            ]
        ];

    }

    public function override_mobile($ab_test_variant)
    {
        $this->override($ab_test_variant, 'nav_menu_locations');
        $this->override($ab_test_variant, 'mobile_sidebar');
    }

    public function override($ab_test_variant, $theme_mod = '')
    {
        set_theme_mod('topbar_show', false);
        foreach ($areas = ['topbar_elements_center', 'topbar_elements_left', 'topbar_elements_right'] as $area) {
            if (isset($this->theme_mods[$ab_test_variant][$area])) {
                if (is_array($this->theme_mods[$ab_test_variant][$area])){
                    if (count($this->theme_mods[$ab_test_variant][$area])>0) {
                        set_theme_mod('topbar_show', true);
                    }
                }
            }
        }
        if ($theme_mod == "") {
            foreach ($this->theme_mods[$ab_test_variant] as $mod => $value) {
                if ($value != get_theme_mod($mod)) {
                    set_theme_mod($mod, $value);
                }
            }
            $this->override_styles($ab_test_variant);
        } else {
            if ($this->theme_mods[$ab_test_variant][$theme_mod] != get_theme_mod($theme_mod)) {
                set_theme_mod($theme_mod, $this->new_theme_mods[$theme_mod]);
            }
        }


    }

    public function override_styles($ab_test_variant)
    {
        $header_height_mobile = get_theme_mod('header_height_mobile', 70);
        $header_height = get_theme_mod('header_height', 90) . 'px';
        $logo_width = get_theme_mod('logo_width', 200) . 'px';
        $topbar_bg = get_theme_mod('topbar_bg') . '!important';
        $styles = <<<EOCSS
                    .header-top{background-color: $topbar_bg; }
                    .nav>li>a, .nav-dropdown>li>a, .nav-column>li>a {color: #1a1a1a;}
                    .header-main{height: $header_height}
                    #logo img{max-height: $header_height}
                    #logo {
                        width: $logo_width
                    }
                    @media screen and (max-width: 849px) {
                        .header-main{height: $header_height_mobile}
                        #logo {
                            width: 200px;
                        }
                    }
                EOCSS;
        if (get_theme_mod('logo_max_width')) $styles .= '#logo a{max-width:' . get_theme_mod('logo_max_width') . 'px;}';
        $cssfile = "/assets/css/blocks/$ab_test_variant.css";
        if (file_exists(get_theme_file_path() . $cssfile)) {
            umbrella_add_custom_css_files([$cssfile]);
        }
        umbrella_add_custom_css($styles);

    }


}
