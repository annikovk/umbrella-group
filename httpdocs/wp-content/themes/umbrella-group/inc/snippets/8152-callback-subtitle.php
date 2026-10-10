<?php
/**
 * Сниппет insert-php #8152 «Подзаголовок в форме обратный звонок» → [ug_callback_subtitle].
 * Подзаголовок форм CF7 «Обратный звонок» по разделу из URL.
 */

declare(strict_types=1);

function ug_snippet_callback_subtitle(array $a): void
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    if (strpos($uri, "services/audit") !== false) {
        echo "Наш аудитор позвонит Вам в ближайшее время и проконсультирует! Режим работы: с 09:00 до 18:00 по Новосибирску.";
    } else if (strpos($uri, "services/bukhgalterskie-uslugi/") !== false) {
        echo "Ведущий бухгалтер позвонит Вам в ближайшее время и проконсультирует! Режим работы: с 09:00 до 18:00 по Новосибирску.";
    } else if (strpos($uri, "services/licensing/litsenziya-na-meditsinskuyu-deyatelnost") !== false) {
        echo "Заполните онлайн-форму, и наши специалисты свяжутся с Вами";
    } else if (strpos($uri, "services/licensing/registracija-tovarnogo-znaka") !== false) {
        echo "Заполните онлайн-форму, и наши специалисты свяжутся с Вами";
    } else if (strpos($uri, "services/licensing/") !== false) {
        echo "Специалист по лицензированию позвонит Вам в ближайшее время и проконсультирует! Режим работы: с 09:00 до 18:00 по Новосибирску.";
    } else if (strpos($uri, "services/services-le") !== false) {
        echo "Наш юрист позвонит Вам в ближайшее время и проконсультирует! Режим работы: с 09:00 до 18:00 по Новосибирску.";
    } else {
        echo "Дежурный специалист позвонит Вам в ближайшее время и проконсультирует! Режим работы: с 09:00 до 18:00 по Новосибирску.";
    }
}

ug_snippet_shortcode('ug_callback_subtitle', 'ug_snippet_callback_subtitle');
