<?php


$args = array(
    'taxonomy' => 'client_industry',
    'orderby' => 'name',
    'order' => 'ASC'
);

$categories = get_categories($args);
$posts = get_feedback_posts();
umbrella_draw_tiles($posts, 'feedback');


function get_feedback_posts()
{
    $args = array(
        'numberposts' => '99',
        'post_type' => 'client',
    );
    $posts = get_posts($args);
    $result = array();
    $feedback_categories = array();

    foreach ($posts as $post) {
        if (!empty(get_the_excerpt($post->ID))) {
            array_push($result, $post);
            $posts_categries = get_the_terms($post->ID, 'client_industry');
            //print_r($posts_categries).'<br><br>';
            //print_r($posts_categries);

            foreach ($posts_categries as $posts_categry){
//                if (!first_value_matches($feedback_categories,$posts_categry->slug)) {
                  if (!$feedback_categories[$posts_categry->slug]["slug"]==$posts_categry->slug){
                    $category =  array("slug"=>$posts_categry->slug,"name"=>$posts_categry->name,"count"=>1);
                    $feedback_categories[$posts_categry->slug] = $category;
                } else {
                    $feedback_categories[$posts_categry->slug]['count']++;
                }
            }
        };
    }
    umbrella_draw_filter_tabs($feedback_categories, $result,'feedback');
    return $result;
}
//function first_value_matches ($array, $string) {
//    if ($array[$string]["slug"]==$string) return true;
//    return false;
//}

?>

<style>

    .scale-anm {
        transform: scale(1);
    }
.btn.tabs {
    font-size: 14px;
    cursor: pointer;
    display: inline-block;
    line-height: normal;
    padding: .5rem 1rem;
    height: auto;
    font-size: ;
    vertical-align: middle;
    -webkit-appearance: none;
    color: #555;
    background-color: rgba(0, 0, 0, 0);
}

    .btn {

    }

    .btn:hover {
        background-color: black;
        color: white;

    }

    .btn:focus {
        background-color: black;
        color: white;
    }

    ::-moz-focus-inner {
        border: 0;
        padding: 0;
    }


</style>

