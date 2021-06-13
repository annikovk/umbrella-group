<?php
function umbrella_get_about_subheader()
{
    ?>
    <div class="about-subheader hide-for-medium">
        <div id="wide-nav" class=" wide-nav hide-for-medium">
            <div class="flex-row container">
                <div class="flex-col flex-left">
                    <ul class="nav header-nav header-bottom-nav nav-left  nav-line-bottom nav-spacing-xlarge nav-uppercase">

                            <?php umbrella_get_about_menu_items(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function umbrella_get_about_menu_items(){
    $about_pages = array(get_page(8));
    $args = array(
        'child_of' => '8',
    );
    $childrens = get_pages($args);
    foreach ($childrens as $child) {
        array_push($about_pages, $child);
    }
    foreach ($about_pages as $element) {
        if (get_queried_object()->ID == $element->ID) $isactive = 'active'; else $isactive='';
        echo '<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-home ' . $isactive         . '">';
        echo '<a href="' . get_page_link($element) . '">';
            echo $element->post_title;
        echo '</li>';
        echo '</a>';
    }
    ?>
<style>
    .about-subheader {
        width: 100%;
        height: 40px;
        background-color: #f9f9f9;
    }
    .about-subheader ul>li>a {
        color: #1a1a1a;
    }



</style>
<?php
}