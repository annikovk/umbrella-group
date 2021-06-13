<?php
/**
 * Accordion Shortcode
 *
 * Accordion and Accordion Item Shortcode builder.
 *
 * @author UX Themes
 * @package Flatsome/Shortcodes/Accordion
 * @version 3.9.0
 */

/**
 * Output the accordion shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @param string $content Accordion content.
 *
 * @return string.
 */
function ux_accordion($atts, $content = null)
{
    extract(shortcode_atts(array(
        'auto_open' => '',
        'open' => '',
        'title' => '',
        'class' => '',
    ), $atts));

    if ($auto_open) $open = 1;

    $classes = array('accordion');
    if ($class) $classes[] = $class;

    if ($title) $title = '<h3 class="accordion_title">' . $title . '</h3>';
    return $title . '<div class="' . implode(' ', $classes) . '" rel="' . $open . '">' . flatsome_contentfix($content) . '</div>';
}

add_shortcode('accordion', 'ux_accordion');


/**
 * Output the accordion-item shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @param string $content Accordion content.
 *
 * @return string.
 */
// [accordion-item]
function ux_accordion_item($atts, $content = '', $code)
{
    extract(shortcode_atts(array(
        'subtitle' => '',
        'title' => 'Accordion Panel',
        'class' => ''
    ), $atts));
    $subtitle = '<div class="accordion-subtitle">' . $subtitle . '</div>';

    if (strlen($content) > 1) {
        $button = '<button class="toggle">↓</button>';
    } else {
        $button = '';
    }
    return '<div class="accordion-item box-shadow-3-hover" >'
                    .$button .
                    '<a href="#" class="accordion-title  plain" style=" padding-top: 24px; padding-bottom: 24px; ">' . '' .
                        '<div class="accordion-title-title">'
                        . $title .
                        '</div>'
                        . $subtitle  .'</a>'.

                    '<div class="accordion-inner">' . flatsome_contentfix($content) . '</div>'  .
                '</div>';
}

add_shortcode('accordion-item', 'ux_accordion_item');

