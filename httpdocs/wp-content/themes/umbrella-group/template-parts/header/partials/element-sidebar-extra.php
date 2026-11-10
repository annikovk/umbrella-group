<?php
/**
 * Мобильная панель (#main-menu, template-parts/overlays/overlay-menu.php):
 * мессенджеры, которые на ≤ 849px убраны из шапки (header-site.php).
 * Стили — assets/css/header-site.css (.ugh-side).
 */
?>
<li class="ugh-side">
    <div class="ugh-side__title">Связаться в мессенджере</div>
    <ul class="ugh-side__msg">
        <?php get_template_part('template-parts/header/partials/element', 'messengers'); ?>
    </ul>
</li>
