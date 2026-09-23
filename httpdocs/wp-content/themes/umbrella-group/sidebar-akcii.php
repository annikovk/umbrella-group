<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package flatsome
 */
?>
<div id="secondary" class="widget-area <?php flatsome_sidebar_classes(); ?>" role="complementary">
    <?php do_action( 'before_sidebar' ); ?>
    <?php
    echo umbrella_akcii_contact_form();
//    echo get_stylesheet_directory_uri() . '/assets/js/akcii-custom-postmeta.js';
    ?>

</div>
