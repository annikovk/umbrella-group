<?php
/** [form_calc_footer_busnisse] — прежнее имя нижней формы 14148, см. form_business_calculation.php. */

function footer_form_calculate_busnisse_shortcode($atts)
{
    return form_calculate_busnisse_shortcode(['variant' => 'bottom']);
}

add_shortcode('form_calc_footer_busnisse', 'footer_form_calculate_busnisse_shortcode');
