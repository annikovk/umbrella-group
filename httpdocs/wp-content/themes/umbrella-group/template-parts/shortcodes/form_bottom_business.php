<?php

function footer_form_calculate_busnisse_shortcode($atts)
{
    $html = <<<EOTHTML
         [section id='form_calculate_busnisse' class='form_calculate_busnisse'  padding="0px"]
            
            
                <div class="row">
                    <div class="col">
                    <h2 class="title_form_calculate_busnisse_bottom">Минимизируйте <br>риски при открытии бизнеса</h2>
                    <p class="sub_title_form_calculate_busnisse_bottom">Начните вместе с ГК Umbrella Group </p>
            <div class="form_calculate_busnisse_screen">
                        <div class="form_busnisse_wrap">
                        <div class="form_busnisse_wrap_inner">
                            <h2>Расчёт стоимости<br> открытия бизнеса</h2>
                            <div class="desc_form_calc">
                                Расскажите, чем вы хотите заниматься. И получите детальный<br> расчёт открытия бизнеса с планом работ: сроки и стоимость<br> по каждому этапу + пакетное предложение со скидкой.
                            </div>
                            <div class="bonus_desc_form">
                            <img src="/wp-content/uploads/image-110.svg">
                                <div class="list_form_bonus">
                                    <p><span>Бонус</span> при оформлении заявки через сайт:</p>
                                    <ul>
                                        <li>Бесплатная регистрация договора долгосрочной аренды.</li>
                                        <li>3 часа юридических консультаций.</li>
                                        <li>Пакет кадровых документов.</li>
                                        <li>Бесплатная консультация бухгалтера и специалиста по кадрам.</li>
                                    </ul>
                                </div>
                            </div>
                            [contact-form-7 id="14223" title="Форма Расчёт стоимости открытия бизнеса"]
                        </div>
                        </div>
                    </div>
                </div>
            </div>
         [/section]
         
        EOTHTML;
    umbrella_add_custom_css_files(['/assets/css/blocks/form_business_calculation.css']);
    umbrella_add_custom_js_files(['/assets/js/blocks/form_business_calculation.js']);
    return $html;
}

add_shortcode('form_calc_footer_busnisse', 'footer_form_calculate_busnisse_shortcode');