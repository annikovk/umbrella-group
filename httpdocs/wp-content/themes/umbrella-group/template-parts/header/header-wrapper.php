<?php

require get_theme_file_path() . '/template-parts/header/ab-tests-header-modifier.php';
change_top_header_according_to_ab_tests();

function change_top_header_according_to_ab_tests(){
    $header = new AB_tests_header_modifier();
    if (in_array("umbrella_ab_test4_variant2",umbrella_get_ab_test_tags())) {
        $header->override("umbrella_ab_test4_variant2");
        umbrella_draw_header();
    } else {
        $header->override("umbrella_ab_test4_variant1");
        umbrella_draw_header();
    }
}

function umbrella_draw_header(): void
{
    get_template_part('template-parts/header/header', 'top');
    get_template_part('template-parts/header/header', 'main');
    get_template_part('template-parts/header/header', 'bottom');
    echo '<div class="header-bg-container fill">';
    do_action('flatsome_header_background');
    echo '</div>';
    do_action('flatsome_header_wrapper');

}

?>