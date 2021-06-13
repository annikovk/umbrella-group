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
    $contact_form = do_shortcode('[contact-form-7 id="9878" title="Форма на боковой панели акции"]',true);
    $contact_form = str_replace("_akcii_form_header_", get_post_meta(get_the_ID(), 'akcii_form_header', true), $contact_form);
    $contact_form = str_replace("_akcii_form_subheader_", get_post_meta(get_the_ID(), 'akcii_form_subheader', true), $contact_form);
    $contact_form = str_replace("_akcii_form_button_text_", get_post_meta(get_the_ID(), 'akcii_form_button_text', true), $contact_form);
    echo $contact_form;
//    echo get_stylesheet_directory_uri() . '/assets/js/akcii-custom-postmeta.js';
    ?>

</div>
