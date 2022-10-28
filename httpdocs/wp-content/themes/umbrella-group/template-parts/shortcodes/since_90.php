<?php

function since_shortcode($atts){
        $html = <<<EOTHTML
         [section id='since' class='since'  padding="0px"]
            <div class="since_screen pd">
                    <div class="row">
                        <div class="col">
                            <div class="since_wrap">
                            <h2>С 1990 г. помогаем российскому бизнесу родиться, жить, расти и развиваться</h2>
                            <div class="since_percent">
                                <span>82 %</span>
                                <p>Организаций<br> окупились за 1 год* </p>
                            </div>
                            </div>
                            <p class="remark_text">* По статистике клиентов ГК Umbrella Group за 1990–2021 годы, получавших услугу, либо комплекс услуг регистрации организации, получения лицензии и бухгалтерского обслуживания.</p>
                        </div>
                    </div>

            </div>
         [/section]
        EOTHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/since_90.css']);
        return $html;
    }

add_shortcode('since_90', 'since_shortcode');