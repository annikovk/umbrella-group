<?php
/**
 * Сниппет insert-php #10644 «Кнопка скачать памятку PHP» →
 * [ug_download_memo href="/wp-content/uploads/….pdf"]. 36 страниц услуг.
 *
 * Разметка — как у сниппета, включая сломанный onclick (кавычки внутри
 * одинарных): по клику ничего не исполняется, roistat ловит скачивание сам.
 * Из атрибутов, как в плагине (snippet_tags), доходит только href.
 */

declare(strict_types=1);

function ug_snippet_download_memo(array $a): void
{
    echo "<a target='_self' download class='button primary is-outline' onclick='roistat.event.send('pam');'' rel='noopener' href='" . esc_url($a['href'] ?? '') . "'><span>Скачать памятку</span></a>";
}

ug_snippet_shortcode('ug_download_memo', 'ug_snippet_download_memo', ['href']);
