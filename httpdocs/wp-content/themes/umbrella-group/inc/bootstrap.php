<?php
/**
 * Загрузчик темы: явный список require (без glob — порядок важен).
 *
 * Порядок повторяет старый functions.php там, где он влияет на поведение:
 *  - assets.php — до shortcodes.php: на wp_enqueue_scripts:10 шрифты,
 *    form-custom.css и script.js встают в очередь раньше ассетов upsell;
 *  - tiles.php — до шаблонов и шорткодов: в одном файле функции были видны
 *    с первой строки (PHP поднимает объявления);
 *  - cookie-notice.php — последним: output_cookie_notice на wp_footer:10
 *    выводится после umbrella_upsell_template.
 *
 * Владельцы файлов по пакетам спеки 1 (§5): helpers — WS-4; cf7, attribution,
 * cookie-notice, legacy-stubs — WS-1; setup, assets — WS-2;
 * post-types, shortcodes, tiles — WS-6; acf — WS-5; layout — WS-3; snippets — WS-8.
 * Новый файл inc/ добавляется строкой сюда тем пакетом, который его создаёт.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

require __DIR__ . '/helpers.php';
require __DIR__ . '/acf.php';
require __DIR__ . '/cf7.php';
require __DIR__ . '/attribution.php';
require __DIR__ . '/setup.php';
require __DIR__ . '/legacy-stubs.php';
require __DIR__ . '/assets.php';
require __DIR__ . '/perf.php';
require __DIR__ . '/flatsome-legacy.php';
require __DIR__ . '/layout.php';
require __DIR__ . '/tiles.php';
require __DIR__ . '/post-types.php';
require __DIR__ . '/shortcodes.php';
require __DIR__ . '/snippets.php';
require __DIR__ . '/cookie-notice.php';
require __DIR__ . '/schema.php';
require __DIR__ . '/blog.php';
require __DIR__ . '/price.php';
