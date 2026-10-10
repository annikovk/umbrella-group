<?php
/**
 * Сниппет insert-php #5645 «SEO logo» → [ug_seo_logo]: подпись подрядчика
 * «INTELSIB — продвижение сайта» в нижней полосе подвала (UX-блок 421
 * absolute-footer); ссылка — только на главной.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_seo_logo(array $a): void
{
    ?>
		
<?php if((string) ($_SERVER['REQUEST_URI'] ?? '')=='/'):?><div class="intelsib-logo"><a href="https://nsk.intelsib.ru/price/"><span style="max-width:80px; margin-right:4px;display: inline-block;">INTELSIB</span></a><span style="font-size:12px;vertical-align: text-top;color: #666666;"> — продвижение сайта</span></div>
<?php else:?><div class="intelsib-logo"><span style="max-width:80px; margin-right:4px;display: inline-block;">INTELSIB</span><span style="font-size:12px;vertical-align: text-top;color: #666666;"> — продвижение сайта</span></div>
<?php endif;
}

ug_snippet_shortcode('ug_seo_logo', 'ug_snippet_seo_logo');
