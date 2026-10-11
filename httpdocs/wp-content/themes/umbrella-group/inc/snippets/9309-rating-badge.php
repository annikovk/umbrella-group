<?php
/**
 * Сниппет insert-php #9309 «Иконка рейтинга» → [ug_rating_badge]: плашка
 * рейтинга раздела (umbrella_get_ratings) в UX-блоке 9312, который стоит на
 * /services/ (10) и /services/audit/ (34).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_rating_badge(array $a): void
{
    global $post;
    $bookmark_shadow = <<<EOD
    .laurel-description:after {
        content: '';
        align-self: center;
        position: absolute;
        top:8px;
        height:45px;
        width: 45px;
        right: -22px;
        /*background: #ec1c23;*/
        /*border-top: solid 1px rgba(0, 0, 0, 0.22);*/
        /*border-left: solid 1px rgba(0, 0, 0, 0.22);*/
        box-shadow: inset 7px 7px 15px -9px rgba(0, 0, 0, 0.1);
        transform: rotate(-45deg);
    }
EOD;

    $children = get_pages(array('child_of' => $post->ID));
    $rating = umbrella_get_ratings('');

    $transparent_bg = is_page() && count($children) > 3;

    ?>
<div class="laurel-bookmark">
    <div class="laurel-icon">
    </div>
    <div class="laurel-description">
        <div class="laurel-badge-title"><?php echo $rating['header']; ?></div>
        <div class="laurel-badge-subtitle"><?php echo $rating['subheader']; ?></div>
    </div>
</div>
<style>
    .laurel-badge-title {
        font-weight: bold;
    }
    .laurel-description {
        overflow: hidden;
        z-index: 0;
        height: 64px;
        order: 2;
        align-self: center;
        position: absolute;
        left: 40px;
        padding-left: 58px;
        padding-right: 56px;
        border-top: solid 1px rgba(0, 0, 0, 0.1);
        border-bottom: solid 1px rgba(0, 0, 0, 0.1);
        /*box-shadow: 0 10px 30px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.22);*/
    }
    <?php if ($transparent_bg) {echo $bookmark_shadow;}?>
    .laurel-icon {
        z-index: 1;
        order: 1;
        content: '';
        border-radius: 100%;
        display: flex;
        height: 80px;
        width: 80px;
        background: <?php if ($transparent_bg) {echo '#424242';}?> url(<?php echo $rating['icon']; ?>) no-repeat 0 0;
        background-size: 70px 70px;
        background-position: center;
    }
    .laurel-bookmark {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        max-width: 480px;
        height: 90px;
    }
</style>
<?php
}

ug_snippet_shortcode('ug_rating_badge', 'ug_snippet_rating_badge');
