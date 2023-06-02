<?php

require get_theme_file_path() . '/template-parts/header/ab-tests-header-modifier.php';
umbrella_draw_header();

function umbrella_draw_header(): void
{
    if(!wp_is_mobile()):
        get_template_part('template-parts/header/header', 'top');
    endif;
    get_template_part('template-parts/header/header', 'main');
    get_template_part('template-parts/header/header', 'bottom');
    echo '<div class="header-bg-container fill">';
    do_action('flatsome_header_background');
    echo '</div>';
    do_action('flatsome_header_wrapper');

}

?>