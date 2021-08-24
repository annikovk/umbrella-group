<?php
/*echo '<p class="hide-for-medium" style="font-size: 14px; margin-top: 100px;">' . get_the_date('j F Y') . '</p>';*/

if (!empty(get_the_tags())){

    foreach(get_the_tags() as $category) {

        echo '<p  class="hide-for-medium" style="font-size: 14px;"><a href="' . get_category_link($category->term_id) . '">#'.$category->name .'</a> </p>';
    }
} else echo '<p class="hide-for-medium" style="font-size: 14px;"></p>';
