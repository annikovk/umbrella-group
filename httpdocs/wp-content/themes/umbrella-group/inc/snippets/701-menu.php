<?php
/**
 * Сниппет insert-php #701 «Footer | Отобразить меню по имени» → [ug_menu name="…" class="…"].
 * Подвалы темы (umbrella_footer, foooter_business_page) и UX-блок 6860.
 */

declare(strict_types=1);

function ug_snippet_menu(array $a): void
{
    // Без name сниппет падал в warning на неопределённой $name — меню ядро
    // тогда выбирает само (первое непустое); null даёт то же самое.
    wp_nav_menu(array('menu' => $a['name'] ?? null, 'menu_class' => $a['class'] ?? 'menu', 'depth' => 1));
}

ug_snippet_shortcode('ug_menu', 'ug_snippet_menu');
