<?php
/**
 * Сниппет insert-php #30355 «Услуги | Плитки услуг внутри категории "Налоги"» →
 * [ug_service_tiles_tax] (страница 30352 «Налоговое право»). Разметка — общая
 * с [ug_service_tiles_ip], см. 29221-service-tiles-ip.php.
 */

declare(strict_types=1);

function ug_snippet_service_tiles_tax(array $a): void
{
    ug_snippet_service_tiles_by_url(array(
        'https://taxlab.ru/services/services-le/ocenka-nalogovyh-riskov/',
        'https://taxlab.ru/services/services-le/razreshenie-nalogovyih-sporov/',
        'https://taxlab.ru/services/services-le/strukturirovanie-biznesa/',
        'https://taxlab.ru/services/services-le/soprovozhdenie-nalogovoy-proverki/',
        'https://taxlab.ru/services/audit/nalogovyiy/'
    ));
}

ug_snippet_shortcode('ug_service_tiles_tax', 'ug_snippet_service_tiles_tax');
