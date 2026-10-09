<div class="entry-content single-page">
    <?php
    if (!umbrella_akciya_has_own_hero()) {
        echo ' <p class="big">' . get_the_excerpt() . '</p>';
    }
    ?>
    <?php the_content(); ?>
</div><!-- .entry-content2 -->