<?php
/**
 * Сниппет insert-php #29516 «семинар» → [ug_seminar_title].
 * Заголовок формы регистрации на семинар (CF7 28760); текст зависит от адреса страницы.
 */

declare(strict_types=1);

function ug_snippet_seminar_title(array $a): void
{
    if (strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), "blog/nalogovaya-perestroyka-seminar-2026/") !== false) {
        echo "Налоговая перестройка: сохраняем бизнес, команду и прибыль";
    } else {
        echo "Регистрация на семинар";
    }
}

ug_snippet_shortcode('ug_seminar_title', 'ug_snippet_seminar_title');
