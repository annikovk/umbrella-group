<?php

$rating1 = umbrella_get_ratings('expert-header');
$rating2 = umbrella_get_ratings('header');

$whatsAppUrl = get_field('__whatsapp_url','option');
$whatsAppIconUrl = get_field('__whatsapp_icon','option');
$whatsAppText = get_field('__whatsapp_text','option');

$ratings_block = <<<EOHTML
        <div class="header-ratings-icons">
                <div class="header-ratings-icon-box hide-for-medium">
                    <div class="header-ratings-icon" style="background-image: url({$rating1['icon']});border-radius:0;  width: 47px;"> </div>
                    <div class="header-ratings-header" style="width: 194px;">{$rating1['header']}</div>
                </div>
                <div class="header-ratings-icon-box whatsapp-box">
                    <a href="{$whatsAppUrl}" class="header-ratings-icon" style="background-image: url({$whatsAppIconUrl});border-radius:0;  width: 48px;"> </a>
                    <a href="{$whatsAppUrl}" class="header-social-text"><b>{$whatsAppText}</b></a>
                </div>
            </div>

EOHTML;

echo $ratings_block;

?>
