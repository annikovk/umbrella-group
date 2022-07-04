<?php
function akciya_block_shortcode($atts)
{

    if (isset($atts['id'])) {
        $post = get_post($atts['id']);
    } else {
        return "Пожалуйста, укажите ID записи акции";
    }
    if (isset($atts['type']) && ($atts['type']=='full' || $atts['type']=='half'|| $atts['type']=="prices")) {
        $type = $atts['type'];
    } else {
        return "Пожалуйста, укажите тип банера (full|half|prices)";
    }
    if (isset($atts['thumbnail'])) {
        $image_attributes = wp_get_attachment_image_src($atts['thumbnail'], 'full');
        $thumbnail = '<img src="' . $image_attributes[0] . '"  />';
    } else {
        $thumbnail = get_the_post_thumbnail($post->ID);
    }
    if (isset($atts['title'])) {
        $title = $atts['title'];
    } else {
        $title = $post->post_title;
    }
    if (isset($atts['excerpt'])) {
        $excerpt = $atts['excerpt'];
    } else {
        $excerpt = get_the_excerpt($post->ID);
    }
    if (isset($atts['icon']) && wp_get_attachment_image_src($atts['icon'], 'full') != false) {
        $icon = wp_get_attachment_image_src($atts['icon'], 'full');
        $icon = '<img src="' . $icon[0] . '"  />';
    } else {
        $icon_id = esc_attr(get_post_meta($post->ID, 'akcii_block_icon', true));
        if ($icon_id == "") {
            $icon_id = 10183;
        }
        $icon = wp_get_attachment_image_src($icon_id, 'full');
        $icon = '<img src="' . $icon[0] . '"  />';
    }
    if (isset($atts['button_text'])) {
        $button_text = $atts['button_text'];
    } else {
        $button_text = "Узнать подробнее";
    }
    $date = "До " . date("d.m.Y", strtotime(date('m', strtotime('+1 month')) . '/01/' . date('Y') . ' 00:00:00'));
    $button = do_shortcode('[button text="' . $button_text . '" color="white" style="outline" link="' . urldecode(get_permalink($post->ID)) . '"]');

    $block = <<<EOV
    <div class="akciya-block $type" >
        <div class="background">
            $thumbnail
        </div>  
        <div class="inner-block $type">
            <div class="inner-icon">$icon</div>
            <div class="inner-label">Акция!</div>
            <div class="inner-text">
                <div class="title">
                    $title
                </div>
                <div class="excerpt">
                    $excerpt
                </div>
                <div class="date-and-button">
                    <span class="date">$date</span> $button
                </div>
            </div>
        </div>
    </div>
    EOV;
    if (isset($atts['new'])) {
        $block = <<<EOHTML
            <div class="red_border_stock card-shadow-common">
                  <div class="position-card-legend_stock">
                    <div class="main-text-common red_color_stock">АКЦИЯ</div>
                  </div>
                      <div class="title-h3-text-common text-pad">$title</div>
                      <div class="main-text-common text-pad desktop_mode_stock">$excerpt</div>
                      <div class="stock-parametric">
                        <div class="main-text-common main-text-accent-common desktop_mode_stock">$date</div>
                        $button
                      </div>
                  </div>
        EOHTML;


    }
    umbrella_add_custom_css_files(['/assets/css/blocks/block-akciya.css']);
    return $block;
}

add_shortcode('akciya_block', 'akciya_block_shortcode');