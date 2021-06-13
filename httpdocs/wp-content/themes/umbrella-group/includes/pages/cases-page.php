<?php get_header(); ?>

<div id="content" class="content-area page-wrapper" role="main">
    <div class="row row-main">
        <div class="large-12 col">
            <div class="col-inner">
                <?php umbrella_draw_header_with_breadcrumbs();?>
                <?php if ( have_posts() ) :
                    // Create IDS
                    $ids = array();
                    while ( have_posts() ) : the_post();
                        array_push($ids, get_the_ID());
                    endwhile; // end of the loop.
                    $posts = get_post_by_ids($ids);
                    //print_r(get_category(get_query_var('cat')));
                    $childrens =  get_childrens_of_category(get_category(get_query_var('cat')));
                    echo draw_tabs_for_categories($childrens, $posts);
                    umbrella_draw_tiles($posts, 'blogposts');
                    flatsome_posts_pagination();

                else : get_template_part( 'template-parts/posts/content','none');
                endif; ?>

            </div><!-- .col-inner -->
        </div><!-- .large-12 -->
    </div><!-- .row -->
</div>

<?php
do_action( 'flatsome_after_page' );
?>

<?php get_footer(); ?>

<?php

function get_post_by_ids($ids){
    $args = array(
        'numberposts'   => sizeof($ids),
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post__in' => $ids
    );
    return get_posts($args);
}

function get_childrens_of_category($category) {
    if (is_category()) {

        $thiscat = $category;
        $catid = $thiscat->cat_ID;


        $categories = get_categories(
            array(
                'parent' => $catid,
                'depth' => 1,
                'orderby' => 'id',
                'order' => 'DESC',
                //'exclude' => $catid,
                'hide_empty' => '0'
            ));
        return $categories;
    } else return "error: no category defined.";
}

function draw_tabs_for_categories($tabs, $posts){



    echo '<div class="toolbar mb2 mt2">';
    echo '<button class="btn-filter fil-cat" href="" name="all" data-rel="all" autofocus>Все записи '.sizeof($posts).'</button>';
    foreach ($tabs as $tab){
        echo '<button class="btn-filter fil-cat" name="'.$tab->slug.'"data-rel="' . $tab->slug . '">'.$tab->name. ' ' .$tab->count.'</button>';
    }
    echo '</div>';


}


?>


<script>
    jQuery(function() {
        var selectedClass = "";
        jQuery(".fil-cat").click(function(){
            selectedClass = jQuery(this).attr("data-rel");
            jQuery(this).focus();
            // jQuery("#portfolio").fadeTo(100, 0.1);
            jQuery("#archive-tiles>div>div").not("."+selectedClass).fadeOut().removeClass('scale-anm');
            setTimeout(function() {
                jQuery("."+selectedClass).fadeIn().addClass('scale-anm');
                //jQuery("#portfolio").fadeTo(300, 1);
            }, 300);

        });
    });
</script>

<style>
    body{
        max-width: 900px;
        float: none;
        margin: auto;
    }

    .tile {
        -webkit-transform: scale(0);
        transform: scale(0);
        -webkit-transition: all 350ms ease;
        transition: all 350ms ease;

    }
    .tile:hover {

    }

    .scale-anm {
        transform: scale(1);
    }

    .tile img {
        max-width: 100%;
        width: 100%;
        height: auto;
        margin-bottom: 1rem;

    }

    .btn {
        cursor: pointer;
        display: inline-block;
        line-height: normal;
        padding: .5rem 1rem;
        height: auto;

        vertical-align: middle;
        -webkit-appearance: none;
        color: #555;
        background-color: rgba(0, 0, 0, 0);
    }

    .btn:hover {


    }

    .btn:focus {
        background-color: black;
        color: beige;
    }

    ::-moz-focus-inner {
        border: 0;
        padding: 0;
    }



</style>
