<?php
do_action('flatsome_before_blog');


?>

<?php if(!is_single() && flatsome_option('blog_featured') == 'top'){ get_template_part('template-parts/posts/featured-posts'); } ?>

<div class="row row-large <?php if(flatsome_option('blog_layout_divider')) echo 'row-divided ';?>">

    <div class="large-8 col">
        <?php if(!is_single() && flatsome_option('blog_featured') == 'content'){ get_template_part('template-parts/posts/featured-posts'); } ?>
        <?php
        if(is_single()){
            get_template_part( 'template-parts/akcii/single');
            comments_template();
        } elseif(flatsome_option('blog_style_archive') && (is_archive() || is_search())){

            get_template_part( 'template-parts/posts/archive', flatsome_option('blog_style_archive') );
        } else {
            get_template_part( 'template-parts/posts/archive', flatsome_option('blog_style') );
        }
        ?>
    </div> <!-- .large-9 -->

    <div class="post-sidebar large-4 col ">
        <?php get_sidebar('akcii'); ?>
    </div><!-- .post-sidebar -->

    <?php akcii_get_similar_posts() ?>

</div><!-- .row -->

<?php
do_action('flatsome_after_blog');
?>
<?php

function akcii_get_similar_posts(){

    ?>
    <div class="large-12 col">
        <h2 class="feedback-subheader">Другие акции</h2>

        <p class="feedback-subheader-right">
            <a style="text-decoration: underline;" href="/akcii/"> Посмотреть все</a></p>
        <?php
        if (!isset($include)) {$include='';}
        $args = array(
            'order'   => 'DESC',
            'orderby' => 'date',
            'include' => $include,
            'exclude' => get_post()->ID,
            'numberposts' => 2,
            'category' => '',
            'post_type' => 'akcii',
        );

        $pages = get_posts($args);
        umbrella_draw_tiles($pages, 'blogposts');
        ?>
    </div>
    <?php
}
?>
