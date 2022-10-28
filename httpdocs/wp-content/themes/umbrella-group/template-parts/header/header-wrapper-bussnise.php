<?php

require get_theme_file_path() . '/template-parts/header/ab-tests-header-modifier.php';
umbrella_draw_header();

function umbrella_draw_header(): void
{
    get_template_part('template-parts/header/header', 'bus-top');
    get_template_part('template-parts/header/header', 'bus-main');
    get_template_part('template-parts/header/header', 'bus-bottom');
    echo '<div class="header-bg-container fill">';
    do_action('flatsome_header_background');
    echo '</div>';
    do_action('flatsome_header_wrapper');

}

?>