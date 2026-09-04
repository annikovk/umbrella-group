<?php

$rating1 =  umbrella_get_ratings('expert-header');
$rating2 =  umbrella_get_ratings('header');

$ratings_block = <<<EOHTML
        <style>
        .header-ratings-bus .header-ratings-header {
            font-weight: 400;
            font-size: 16px;
            line-height: 143.5%;
            max-width: 200px;
            color: #1A1A1A;
        }
        .header-ratings-bus .header-ratings-icon-box:first-child .header-ratings-header {
            font-weight: 800;
            max-width: 100%;
            margin-right: 100px;
        }
        </style>
        <div class="header-ratings-bus header-ratings-icons hide-for-medium">
                <div class="header-ratings-icon-box">
                    <div class="header-ratings-header" style="width: 225px;style="font-weight:800><span>Откроем бизнес под ключ</span><br> <span style="font-weight:700">Работаем с 1990 г.</span></div>
                </div>
                <div class="header-ratings-icon-box">
                    <div class="header-ratings-icon " style="background-image: url({$rating2['icon']});"> </div>
                    <div class="header-ratings-header">{$rating2['header']}</div>
                </div>
            </div>

EOHTML;

echo $ratings_block;

?>