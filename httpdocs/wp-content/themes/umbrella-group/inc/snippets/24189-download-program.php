<?php
/**
 * Сниппет insert-php #24189 «Кнопка скачать программу PHP» →
 * [ug_download_program href="/wp-content/uploads/….pdf"]: кнопка «Скачать
 * программу» на страницах семинаров (24162, 28736, 29869). Разметка — как у
 * [ug_download_memo]; из атрибутов доходит только href.
 */

declare(strict_types=1);

function ug_snippet_download_program(array $a): void
{
    echo "<a target='_self' download class='button primary is-outline' onclick='roistat.event.send('pam');'' rel='noopener' href=" . ($a['href'] ?? '') . "><span>Скачать программу</span></a>";
}

ug_snippet_shortcode('ug_download_program', 'ug_snippet_download_program', ['href']);
