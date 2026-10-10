<?php
/**
 * Сниппет insert-php #29843 «пакет документов текст» → [ug_docs_package_text].
 * Подзаголовок формы расчёта (CF7 9965); текст зависит от адреса страницы.
 */

declare(strict_types=1);

function ug_snippet_docs_package_text(array $a): void
{
    if (strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), "paket-dokumentov-dlya-medicinskoj-organizacii") !== false) {
        echo "Оставьте свой номер телефона и специалист отдела лицензирования свяжется с Вами по выбранному пакету документов в ближайшее время! Режим работы с 9:00 до 18:00 по Новосибирску";
    } else {
        echo "Мы позвоним Вам в ближайшее время и рассчитаем стоимость! Режим работы: с 09:00 до 18:00 по Новосибирску";
    }
}

ug_snippet_shortcode('ug_docs_package_text', 'ug_snippet_docs_package_text');
