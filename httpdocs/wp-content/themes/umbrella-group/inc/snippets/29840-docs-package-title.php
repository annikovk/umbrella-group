<?php
/**
 * Сниппет insert-php #29840 «пакеты документов заголовок» → [ug_docs_package_title].
 * Заголовок формы расчёта (CF7 9965); текст зависит от адреса страницы.
 */

declare(strict_types=1);

function ug_snippet_docs_package_title(array $a): void
{
    if (strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), "paket-dokumentov-dlya-medicinskoj-organizacii") !== false) {
        echo "Заказать пакет";
    } else {
        echo "Расчёт стоимости услуги";
    }
}

ug_snippet_shortcode('ug_docs_package_title', 'ug_snippet_docs_package_title');
