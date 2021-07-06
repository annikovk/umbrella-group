<?php


class AB_tests_header_modifier
{
    private $old_theme_mods = [];
    private $new_theme_mods;


    function __construct()
    {
        $this->new_theme_mods = [
            "topbar_elements_left" => ["nav-top"],
            "topbar_bg" => "#ffffff",
            'logo_max_width' => 360,
            "logo_width" => 420,
            "header_elements_left" => ["ratings"],
            "header_elements_right" => ["contact"],
            "header_height" => 130,
            "header_elements_bottom_left" => ["nav"],
            "nav_menu_locations" => [
                "top_bar_nav" => wp_get_nav_menu_object('top-bar-menu-ab-test-4-variant-2')->term_id,
                "primary" => wp_get_nav_menu_object('main-menu-ab-test4-variant2')->term_id,
                "primary_mobile" => wp_get_nav_menu_object('mobile-ab-test4-variant2')->term_id,
            ],
            "mobile_sidebar" => [
                "logo-mobile","nav","contact-mobile"
            ]
        ];
    }

    public function override_mobile()
    {
        $this->override('nav_menu_locations');
        $this->override('mobile_sidebar');
    }
    public function restore_mobile()
    {
        $this->restore('nav_menu_locations');
        $this->restore('mobile_sidebar');
    }

    public function override($theme_mod = '')
    {
        if ($theme_mod == "") {
            foreach ($this->new_theme_mods as $mod => $value) {
                if ($value != get_theme_mod($mod)){
                    $this->old_theme_mods[$mod] = get_theme_mod($mod);
                    set_theme_mod($mod, $value);
                }
            }
            $this->override_styles();
        } else {
            if ($this->new_theme_mods[$theme_mod] != get_theme_mod($theme_mod)) {
                $this->old_theme_mods[$theme_mod] = get_theme_mod($theme_mod);
                set_theme_mod($theme_mod, $this->new_theme_mods[$theme_mod]);
            }
        }


    }

    public function restore($theme_mod='')
    {
        if ($theme_mod == "") {
            foreach ($this->old_theme_mods as $mod => $value) {
                if (get_theme_mod($mod) != $value) {
                    set_theme_mod($mod, $this->old_theme_mods[$mod]);
                }
            }
        } else {
            if (isset($this->old_theme_mods[$theme_mod])) {
                set_theme_mod($theme_mod, $this->old_theme_mods[$theme_mod]);
            }
        }
    }

    public function override_styles()
    {
        $header_height = get_theme_mod('header_height', 90) . 'px';
        $logo_width = get_theme_mod('logo_width', 200) . 'px';
        $topbar_bg = get_theme_mod('topbar_bg' ) . '!important';


        $styles = <<<EOCSS
                    .header-top{background-color: $topbar_bg; }
                    .nav>li>a, .nav-dropdown>li>a, .nav-column>li>a {color: #1a1a1a;}
                    .header-main{height: $header_height}
                    #logo img{max-height: $header_height}
                    #logo {
                        width: $logo_width
                    }
                EOCSS;
        if (get_theme_mod('logo_max_width')) $styles .= '#logo a{max-width:' . get_theme_mod('logo_max_width') . 'px;}';
        umbrella_add_custom_css_files(['/assets/css/blocks/header_ab_test4_variant2.css']);
        umbrella_add_custom_css($styles);
    }


}
