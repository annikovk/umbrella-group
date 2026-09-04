<?php

$rating1 = umbrella_get_ratings('expert-header');
$rating2 = umbrella_get_ratings('header');

$whatsAppUrl = get_field('__whatsapp_url','option');
$whatsAppIconUrl = get_field('__whatsapp_icon','option');
$whatsAppText = get_field('__whatsapp_text','option');

$ratings_block = <<<EOHTML
        <div class="header-ratings-icons">
                <div class="header-ratings-icon-box hide-for-medium">
                    <div class="header-ratings-icon" style="background-image: url(https://taxlab.ru/wp-content/uploads/2019/10/laurel-1.svg);border-radius: 100%;width: 50px;background-color: #424242;height: 65px;height: 50px;"> </div>
                    <div class="header-ratings-header" style="width: 316px;">1 место в отрасли «Корпоративное право» <span style="font-weight: 500;">рейтинг «Делового квартала», Новосибирск, 2025</span></div>
                </div>
                <div class="header-ratings-icon-box whatsapp-box">
                    <a href="https://max.ru/u/f9LHodD0cOJIOXNE7YL84YaWLr0Fmn7wgpMChu5575DDdYM9wCgFx0Dmzuw" class="header-ratings-icon" style="background-image: url(/wp-content/uploads/max-icon.png);border-radius:0;  width: 32px;"> </a>

                    <a href="https://max.ru/u/f9LHodD0cOJIOXNE7YL84YaWLr0Fmn7wgpMChu5575DDdYM9wCgFx0Dmzuw" class="header-social-text"><b>{$whatsAppText}</b></a>
<!--                <a href="{$whatsAppUrl}" class="header-social-text"><b>{$whatsAppText}</b></a>-->
                </div>
                <div class="header-ratings-icon-box whatsapp-box">
                    <a href="https://t.me/umbrella_club_nsk" class="header-ratings-icon" style="background-image: url(/wp-content/themes/umbrella-group/assets/img/tg-img.png);border-radius:0;  width: 28px;"> </a>

                    <a href="https://t.me/umbrella_club_nsk" class="header-social-text"><b>{$whatsAppText}</b></a>
<!--                <a href="{$whatsAppUrl}" class="header-social-text"><b>{$whatsAppText}</b></a>-->
                </div>
            </div>

EOHTML;

echo $ratings_block;

?>