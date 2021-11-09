<?php

$rating1 =  umbrella_get_ratings('expert-header');
$rating2 =  umbrella_get_ratings('header');

$ratings_block = <<<EOHTML
        <div class="header-ratings-icons hide-for-medium">
                <div class="header-ratings-icon-box">
                    <div class="header-ratings-icon " style="background-image: url({$rating2['icon']});"> </div>
                    <div class="header-ratings-header">{$rating2['header']}</div>
                </div>
                <div class="header-ratings-icon-box">
                    <div class="header-ratings-icon" style="background-image: url({$rating1['icon']});border-radius:0;"> </div>
                    <div class="header-ratings-header" style="width: 194px;">{$rating1['header']}</div>
                </div>
            </div>

EOHTML;

echo $ratings_block;

?>
