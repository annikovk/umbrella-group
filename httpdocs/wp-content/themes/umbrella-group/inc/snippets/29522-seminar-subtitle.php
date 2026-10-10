<?php
/**
 * Сниппет insert-php #29522 «семинар подзаголовок» → [ug_seminar_subtitle].
 * Подзаголовок формы регистрации на семинар (CF7 28760): дата семинара; текст зависит от адреса страницы.
 */

declare(strict_types=1);

function ug_snippet_seminar_subtitle(array $a): void
{
    if (strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), "blog/nalogovaya-perestroyka-seminar-2026/") !== false) {
        echo "Дата семинара: 10 апреля 2026 г.";
    } else {
        echo "НДС 22% и новая налоговая реальность: инструкция по выживанию для бизнеса! Семинар для владельцев бизнеса, бухгалтеров, финансовых директоров и руководителей. 21 ноября 2025 г. с 09:30 до 15:00";
    }
}

ug_snippet_shortcode('ug_seminar_subtitle', 'ug_snippet_seminar_subtitle');
