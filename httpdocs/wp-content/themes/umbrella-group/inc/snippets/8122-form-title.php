<?php
/**
 * Сниппет insert-php #8122 «Услуги | Услуга | Заголовок контактной формы» → [ug_form_title].
 * Заголовок формы CF7 (формы 2886, 27947): мета страницы umbrella_form_title,
 * иначе — по разделу из URL.
 */

declare(strict_types=1);

function ug_snippet_form_title(array $a): void
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $header = get_post_meta(get_the_ID(), 'umbrella_form_title', true);
    if (strpos($uri, "services/register-elimination/uvedomlenie-rkn") !== false) {
        echo "Бесплатная консультация по подаче уведомления в Роскомнадзор";
    } else if (strpos($uri, "services/services-le/lichnyj-fond") !== false) {
        echo "Бесплатная консультация по регистрации личного фонда";
    } else if (!empty($header)) {
        echo $header;
    } else if (strpos($uri, "services/licensing") !== false) {
        echo "Бесплатная консультация по лицензированию";
    } else if (strpos($uri, "services/register-elimination") !== false) {
        echo "Бесплатная консультация по регистрации";
    } else if (strpos($uri, "services/services-le") !== false) {
        echo "Бесплатная консультация по юридическим услугам";
    }
}

ug_snippet_shortcode('ug_form_title', 'ug_snippet_form_title');
