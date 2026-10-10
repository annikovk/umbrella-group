<?php
/**
 * WS-8 спеки 1: PHP-сниппеты insert-php → шорткоды темы.
 *
 * 25 используемых сниппетов wbcr-snippets (код в БД, eval из шорткода
 * [wbcr_php_snippet id=N …]) перенесены в тему: inc/snippets/<id>-<slug>.php,
 * шорткод [ug_<slug> …] с тем же выводом. Скрипт меняет ссылки в контенте:
 *   [wbcr_php_snippet id="786" include='50,52'] → [ug_service_tiles include='50,52']
 * — имя и id меняются на шорткод темы, прочие атрибуты остаются как были.
 * Затем 6 сниппетов без вызовов (57, 528, 1121, 28410, 29846, 30354) — в черновик.
 *
 * Где ищет (всё, кроме ревизий, корзины и автосохранений):
 *  - r17Y1_posts.post_content — страницы, записи, UX-блоки, акции, формы CF7;
 *  - r17Y1_postmeta — у форм CF7 шаблон живёт ещё и в мете _form (его и
 *    рендерит CF7); сериализованные значения — через unserialize/serialize;
 *  - r17Y1_options — текстовый виджет сайдбара блога (widget_text, serialize);
 *  - r17Y1_aioseo_posts — текстовые колонки (на стенде вхождений 0; если на
 *    проде появились — правятся и они, в том числе JSON-экранированная форма).
 *
 * Запуск (wp-cli, код со STDIN — на сервер файл класть не нужно). ТЕМА С
 * inc/snippets.php ДОЛЖНА БЫТЬ УЖЕ ВЫКАЧЕНА: без шорткодов ug_* скрипт
 * останавливается, ничего не меняя.
 *
 *   стенд: docker exec -i -u www-data taxlab-local-web-1 \
 *            wp --path=/var/www/vhosts/taxlab.ru/httpdocs eval-file - [dry-run] [export=/путь] \
 *            < tools/migrations/02-snippets-to-theme.php
 *   прод:  ssh annikov.com 'sudo -u taxlabru /opt/plesk/php/8.3/bin/php /usr/local/bin/wp \
 *            --path=/var/www/vhosts/taxlab.ru/httpdocs eval-file - [dry-run] [export=/путь]' \
 *            < tools/migrations/02-snippets-to-theme.php
 *
 * Аргументы позиционные (`--dry-run` wp-cli не пропускает в eval-file):
 *   dry-run       — ничего не писать: показать замены и проверить вывод;
 *   export=<dir>  — куда выгрузить исходные значения до правки (по умолчанию
 *                   <tmp>/taxlab-snippets-<дата>); в dry-run тоже.
 *
 * Проверка перед записью. Для каждого разного вызова (строки шорткода)
 * старый и новый шорткод рендерятся одним do_shortcode в контексте каждой
 * записи, где он стоит (глобальный $post и REQUEST_URI = путь её ссылки), а
 * вызовы из форм, виджетов и UX-блоков (у них «своей» страницы нет) — ещё и в
 * контексте каждой опубликованной страницы и записи. Сравнение: побайтно,
 * иначе («≈») — без учёта \r и длины серий пробелов (сниппеты были набраны в
 * CRLF, файлы темы — LF; в HTML это неразличимо). uniqid() (13 hex в якорях
 * плиток клиентов) приравнивается. Не совпало — FAIL, скрипт ничего не пишет
 * (код выхода 1). Сниппет 1870 не рендерится дважды: он подключает
 * includes/pages/feedback-page.php, который объявляет функцию, — и старый, и
 * новый шорткод делают ровно этот require.
 *
 * Запись: posts — прямой UPDATE post_content (без wp_update_post: не трогаются
 * post_modified, r17Y1_aioseo_posts и хуки save_post), затем
 * wp_save_post_revision() — новое состояние появляется в ревизиях; мета и
 * опции — update_post_meta / update_option. Откат — из export=<dir>
 * (posts/<id>.html, meta/<meta_id>.txt, options/<имя>.txt — сырые значения,
 * как в БД) или из ревизии. Идемпотентно: повторный запуск вызовов
 * [wbcr_php_snippet] известных id не находит и ничего не меняет.
 *
 * Вызовы id, которых нет в карте (неиспользуемые сниппеты: 528 стоит в
 * черновике 2 «Главная (Старый вариант)»), остаются как есть и печатаются;
 * плагин их по-прежнему исполняет, пока он установлен.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Запускать через wp eval-file\n");
    exit(1);
}

$ws8_dry = false;
$ws8_export = rtrim(sys_get_temp_dir(), '/') . '/taxlab-snippets-' . gmdate('Ymd-His');
foreach ((array) ($args ?? []) as $ws8_arg) {
    if ($ws8_arg === 'dry-run' || $ws8_arg === '--dry-run') {
        $ws8_dry = true;
    } elseif (strncmp($ws8_arg, 'export=', 7) === 0) {
        $ws8_export = rtrim(substr($ws8_arg, 7), '/');
    } else {
        WP_CLI::error("Неизвестный аргумент: $ws8_arg (есть dry-run и export=<dir>)");
    }
}

/** id сниппета → шорткод темы (inc/snippets/<id>-<slug>.php). */
const WS8_MAP = [
    373 => 'ug_clients_carousel',
    701 => 'ug_menu',
    772 => 'ug_title_breadcrumbs',
    786 => 'ug_service_tiles',
    1031 => 'ug_client_tiles',
    1870 => 'ug_feedback_page',
    1961 => 'ug_blog_tiles',
    1997 => 'ug_blog_side_panel',
    2906 => 'ug_child_pages',
    5645 => 'ug_seo_logo',
    7300 => 'ug_learning_menu',
    8122 => 'ug_form_title',
    8152 => 'ug_callback_subtitle',
    9110 => 'ug_footer_form_subtitle',
    9123 => 'ug_footer_form_title',
    9309 => 'ug_rating_badge',
    10644 => 'ug_download_memo',
    14263 => 'ug_footer_bottom',
    24189 => 'ug_download_program',
    29221 => 'ug_service_tiles_ip',
    29516 => 'ug_seminar_title',
    29522 => 'ug_seminar_subtitle',
    29840 => 'ug_docs_package_title',
    29843 => 'ug_docs_package_text',
    30355 => 'ug_service_tiles_tax',
];

/** Сниппеты без вызовов в опубликованном контенте — в черновик (спека §5, WS-8). */
const WS8_UNUSED = [57, 528, 1121, 28410, 29846, 30354];

/** Не рендерить дважды за процесс (require файла с объявлением функции). */
const WS8_RENDER_ONCE = [1870];

const WS8_RE = '/\[wbcr_php_snippet\s+id=(["\']?)(\d+)\1(\s[^\]]*)?\]/';

foreach (WS8_MAP as $ws8_id => $ws8_tag) {
    if (!shortcode_exists($ws8_tag)) {
        WP_CLI::error("Шорткода [$ws8_tag] нет — сначала выкатить тему с inc/snippets.php. Ничего не изменено.");
    }
}
if (!shortcode_exists('wbcr_php_snippet')) {
    WP_CLI::error('Шорткода [wbcr_php_snippet] нет (insert-php выключен?) — сверять вывод не с чем. Ничего не изменено.');
}

/**
 * Замена в строке. $calls — [старая строка шорткода => новая], $skipped — id без
 * шорткода темы => число вхождений.
 */
function ws8_replace_string(string $s, array &$calls, array &$skipped): string
{
    return (string) preg_replace_callback(WS8_RE, static function (array $m) use (&$calls, &$skipped): string {
        $id = (int) $m[2];
        if (!isset(WS8_MAP[$id])) {
            $skipped[$id] = ($skipped[$id] ?? 0) + 1;
            return $m[0];
        }
        $new = '[' . WS8_MAP[$id] . ($m[3] ?? '') . ']';
        $calls[$m[0]] = $new;
        return $new;
    }, $s);
}

/** То же для значения, которое может быть сериализованным массивом/строкой. */
function ws8_replace_value($value, array &$calls, array &$skipped)
{
    if (is_string($value)) {
        if (is_serialized($value)) {
            $data = unserialize($value, ['allowed_classes' => false]);
            if ($data === false && $value !== serialize(false)) {
                WP_CLI::warning('Не разобрать сериализованное значение — оставлено как есть');
                return $value;
            }
            return serialize(ws8_replace_value($data, $calls, $skipped));
        }
        return ws8_replace_string($value, $calls, $skipped);
    }
    if (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = ws8_replace_value($v, $calls, $skipped);
        }
    }
    return $value;
}

/** Путь ссылки записи — как REQUEST_URI при её просмотре. */
function ws8_uri(?WP_Post $post): string
{
    if (!$post) {
        return '/';
    }
    $link = (string) get_permalink($post);
    $path = (string) wp_parse_url($link, PHP_URL_PATH);
    $query = (string) wp_parse_url($link, PHP_URL_QUERY);
    return ($path === '' ? '/' : $path) . ($query !== '' ? '?' . $query : '');
}

/**
 * do_shortcode в контексте записи. Список уже напечатанных стилей/скриптов
 * обнуляется: <link>, который блок печатает «один раз за запрос»
 * (ug_tiles_print_style), должен попасть в оба сравниваемых рендера.
 */
function ws8_render(string $shortcode, ?WP_Post $post, string $uri): string
{
    wp_styles()->done = [];
    wp_scripts()->done = [];
    $_SERVER['REQUEST_URI'] = $uri;
    $GLOBALS['post'] = $post;
    if ($post) {
        setup_postdata($post);
    }
    $out = do_shortcode($shortcode);
    wp_reset_postdata();
    return $out;
}

/**
 * Для сверки «≈»: то, что меняется от рендера к рендеру при том же коде, и то,
 * чего браузер не различает.
 *  - \r и длина серий пробелов (CRLF сниппетов против LF темы);
 *  - случайные id Flatsome (rand() в 3.13: row-…, col-…, section_…) и uniqid()
 *    (13 hex) → N по порядку появления: ссылки #id на соседний id сравниваются;
 *  - id меню: wp_nav_menu раздаёт id один раз за процесс (второй рендер того же
 *    меню получает «menu-main-menu-1» и <li> без id) — артефакт двух рендеров
 *    в одном процессе, на странице меню рендерится один раз;
 *  - номер формы CF7 на странице (wpcf7-f1301-o3) — счётчик за процесс.
 */
function ws8_loose(string $html): string
{
    $html = str_replace("\r", '', $html);
    $seen = [];
    $html = (string) preg_replace_callback(
        '/\b((?:section|row|col|banner|image|text-box|gap|stack|slider|button|accordion|tabs|panel|ux)[_-])(\d{5,})\b|\b[0-9a-f]{13}\b/',
        static function (array $m) use (&$seen): string {
            $key = $m[0];
            $seen[$key] ??= count($seen);
            return ($m[1] ?? '') !== '' ? $m[1] . 'N' . $seen[$key] : 'U' . $seen[$key];
        },
        $html
    );
    $html = (string) preg_replace('/ id="menu-item-\d+"/', '', $html);
    $html = (string) preg_replace('/(wpcf7-f\d+(?:-p\d+)?-o)\d+/', '$1N', $html);
    $html = (string) preg_replace('/ id="(menu-[a-z-]+?)(?:-\d+)?"/', ' id="$1"', $html);
    return trim((string) preg_replace('/\s+/', ' ', $html));
}

/** 'exact' | 'ws' | 'fail' */
function ws8_compare(string $old, string $new): string
{
    if ($old === $new) {
        return 'exact';
    }
    return ws8_loose($old) === ws8_loose($new) ? 'ws' : 'fail';
}

global $wpdb;

// ---------------------------------------------------------------- сбор
$ws8_like = '%' . $wpdb->esc_like('[wbcr_php_snippet') . '%';
$ws8_items = [];   // [kind, key, label, old, new, calls, skipped, post|null]

$ws8_post_ids = $wpdb->get_col($wpdb->prepare(
    "SELECT ID FROM {$wpdb->posts}
     WHERE post_content LIKE %s
       AND post_type <> 'revision'
       AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
     ORDER BY ID",
    $ws8_like
));
foreach ($ws8_post_ids as $ws8_pid) {
    $p = get_post((int) $ws8_pid);
    $calls = $skipped = [];
    $new = ws8_replace_string($p->post_content, $calls, $skipped);
    $ws8_items[] = ['post', $p->ID, sprintf('#%d %s [%s] %s', $p->ID, $p->post_type, $p->post_status,
        $p->post_name !== '' ? urldecode($p->post_name) : '(без slug)'), $p->post_content, $new, $calls, $skipped, $p];
}

$ws8_meta = $wpdb->get_results($wpdb->prepare(
    "SELECT m.meta_id, m.post_id, m.meta_key, m.meta_value, p.post_type
     FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id
     WHERE m.meta_value LIKE %s
       AND p.post_type <> 'revision'
       AND p.post_status NOT IN ('trash', 'auto-draft', 'inherit')
     ORDER BY m.meta_id",
    $ws8_like
));
foreach ($ws8_meta as $m) {
    $calls = $skipped = [];
    $new = ws8_replace_value($m->meta_value, $calls, $skipped);
    $ws8_items[] = ['meta', (int) $m->meta_id, sprintf('meta #%d %s(#%d %s)', $m->meta_id, $m->meta_key, $m->post_id, $m->post_type),
        $m->meta_value, $new, $calls, $skipped, null, (int) $m->post_id, $m->meta_key];
}

$ws8_opts = $wpdb->get_results($wpdb->prepare(
    "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_value LIKE %s ORDER BY option_name",
    $ws8_like
));
foreach ($ws8_opts as $o) {
    $calls = $skipped = [];
    $new = ws8_replace_value($o->option_value, $calls, $skipped);
    $ws8_items[] = ['option', $o->option_name, 'option ' . $o->option_name, $o->option_value, $new, $calls, $skipped, null];
}

// AIOSEO: SEO-поля записи живут в своей таблице, не только в postmeta.
$ws8_aioseo = $wpdb->prefix . 'aioseo_posts';
if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $ws8_aioseo)) === $ws8_aioseo) {
    $cols = $wpdb->get_col($wpdb->prepare(
        "SELECT COLUMN_NAME FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s AND DATA_TYPE IN ('text','mediumtext','longtext','varchar','tinytext')",
        $ws8_aioseo
    ));
    foreach ($cols as $col) {
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT id, post_id, `$col` AS v FROM `$ws8_aioseo` WHERE `$col` LIKE %s", $ws8_like
        ));
        foreach ($rows as $r) {
            $calls = $skipped = [];
            $new = ws8_replace_string($r->v, $calls, $skipped);
            if (str_contains($new, '[wbcr_php_snippet')) {
                // JSON-колонки хранят кавычки как \" — регулярка такое не берёт.
                WP_CLI::warning("aioseo #{$r->id} $col: остался [wbcr_php_snippet] (экранированный?) — поправить руками");
            }
            $ws8_items[] =['aioseo', (int) $r->id, sprintf('aioseo #%d %s (post #%d)', $r->id, $col, $r->post_id),
                $r->v, $new, $calls, $skipped, null, $col];
        }
    }
}

WP_CLI::log(sprintf('%s: значений с [wbcr_php_snippet] — %d (posts %d, postmeta %d, options %d, aioseo %d)',
    $ws8_dry ? 'DRY-RUN' : 'Миграция', count($ws8_items), count($ws8_post_ids), count($ws8_meta), count($ws8_opts),
    count(array_filter($ws8_items, static fn($i) => $i[0] === 'aioseo'))));

// ---------------------------------------------------------------- экспорт
foreach (['posts', 'meta', 'options', 'aioseo'] as $ws8_sub) {
    if (!is_dir("$ws8_export/$ws8_sub") && !mkdir("$ws8_export/$ws8_sub", 0700, true)) {
        WP_CLI::error("Не создать каталог экспорта $ws8_export/$ws8_sub");
    }
}
WP_CLI::log("Экспорт исходных значений: $ws8_export");
foreach ($ws8_items as $it) {
    $file = match ($it[0]) {
        'post' => "posts/{$it[1]}.html",
        'meta' => "meta/{$it[1]}.txt",
        'option' => 'options/' . sanitize_file_name($it[1]) . '.txt',
        'aioseo' => "aioseo/{$it[1]}-{$it[8]}.txt",
    };
    file_put_contents("$ws8_export/$file", $it[3]);
}

// ---------------------------------------------------------------- сверка вывода
// Контексты, где вызов может рендериться: записи, где он стоит; для вызовов
// вне записей с адресом (формы, виджеты, UX-блоки) — все опубликованные
// страницы и записи.
$ws8_ctx = [];       // старая строка => [ [post|null, uri], … ]
$ws8_global = [];    // старая строка => true, если нужен обход всех страниц
$ws8_newof = [];     // старая строка => новая
foreach ($ws8_items as $it) {
    foreach ($it[5] as $old => $new) {
        $ws8_newof[$old] = $new;
        $p = $it[7];
        $own = $p && in_array($p->post_type, ['page', 'post', 'akcii'], true);
        if ($own) {
            $ws8_ctx[$old][] = [$p, ws8_uri($p)];
        } else {
            $ws8_global[$old] = true;
        }
    }
}
$ws8_all_ctx = null;
$ws8_stats = ['exact' => 0, 'ws' => 0];
$ws8_fails = [];
$ws8_rendered_once = [];
foreach ($ws8_newof as $old => $new) {
    preg_match(WS8_RE, $old, $mm);
    $sid = (int) $mm[2];
    if (in_array($sid, WS8_RENDER_ONCE, true)) {
        WP_CLI::log("  сверка $old → $new: не рендерится (require файла с функцией), код тот же");
        continue;
    }
    $ctxs = $ws8_ctx[$old] ?? [];
    if (!empty($ws8_global[$old])) {
        if ($ws8_all_ctx === null) {
            $ws8_all_ctx = [[null, '/']];
            $ids = get_posts(['post_type' => ['page', 'post'], 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC']);
            foreach ($ids as $cid) {
                $cp = get_post($cid);
                $ws8_all_ctx[] = [$cp, ws8_uri($cp)];
            }
        }
        $ctxs = array_merge($ctxs, $ws8_all_ctx);
    }
    $worst = 'exact';
    foreach ($ctxs as [$cp, $uri]) {
        $o = ws8_render($old, $cp, $uri);
        $n = ws8_render($new, $cp, $uri);
        $r = ws8_compare($o, $n);
        if ($r === 'fail') {
            $ws8_fails[] = "$old → $new на $uri: вывод различается (" . strlen($o) . ' / ' . strlen($n) . ' байт)';
            $worst = 'fail';
            break;
        }
        if ($r === 'ws') {
            $worst = 'ws';
        }
    }
    if ($worst !== 'fail') {
        $ws8_stats[$worst]++;
    }
    WP_CLI::log(sprintf('  сверка %s → %s: контекстов %d — %s', $old, $new, count($ctxs),
        ['exact' => 'совпадает', 'ws' => '≈ (только \r/пробелы)', 'fail' => 'FAIL'][$worst]));
}
if ($ws8_fails) {
    foreach ($ws8_fails as $f) {
        WP_CLI::warning("FAIL: $f");
    }
    WP_CLI::error('Вывод не совпал — ничего не записано.');
}
WP_CLI::log(sprintf('Сверка: вызовов %d, побайтно %d, ≈ %d', count($ws8_newof), $ws8_stats['exact'], $ws8_stats['ws']));

// ---------------------------------------------------------------- запись
$ws8_changed = 0;
$ws8_left = [];
foreach ($ws8_items as $it) {
    [$kind, $key, $label, $old, $new, $calls, $skipped] = $it;
    foreach ($skipped as $sid => $n) {
        $ws8_left[$sid][] = $label;
    }
    if ($new === $old) {
        WP_CLI::log("$label: замен нет" . ($skipped ? ' (id без шорткода темы: ' . implode(', ', array_keys($skipped)) . ')' : ''));
        continue;
    }
    $counts = [];
    preg_match_all(WS8_RE, is_string($old) ? $old : '', $all, PREG_SET_ORDER);
    foreach ($all as $m) {
        if (isset(WS8_MAP[(int) $m[2]])) {
            $counts[(int) $m[2]] = ($counts[(int) $m[2]] ?? 0) + 1;
        }
    }
    if (!$counts) {
        // сериализованное значение: считаем по развёрнутому
        foreach (array_keys($calls) as $c) {
            preg_match(WS8_RE, $c, $m);
            $counts[(int) $m[2]] = ($counts[(int) $m[2]] ?? 0) + 1;
        }
    }
    $desc = implode(', ', array_map(static fn($id, $n) => WS8_MAP[$id] . ($n > 1 ? "×$n" : ''), array_keys($counts), $counts));
    WP_CLI::log("$label: $desc");
    $ws8_changed++;
    if ($ws8_dry) {
        continue;
    }
    switch ($kind) {
        case 'post':
            $wpdb->update($wpdb->posts, ['post_content' => $new], ['ID' => $key]);
            clean_post_cache($key);
            wp_save_post_revision($key);
            break;
        case 'meta':
            $wpdb->update($wpdb->postmeta, ['meta_value' => $new], ['meta_id' => $key]);
            wp_cache_delete($it[8], 'post_meta');
            break;
        case 'option':
            $wpdb->update($wpdb->options, ['option_value' => $new], ['option_name' => $key]);
            wp_cache_delete($key, 'options');
            wp_cache_delete('alloptions', 'options');
            break;
        case 'aioseo':
            $wpdb->update($ws8_aioseo, [$it[8] => $new], ['id' => $key]);
            break;
    }
}

// ---------------------------------------------------------------- неиспользуемые → черновик
$ws8_drafted = 0;
foreach (WS8_UNUSED as $sid) {
    $sp = get_post($sid);
    if (!$sp || $sp->post_type !== 'wbcr-snippets') {
        continue;
    }
    $used = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts}
         WHERE post_status = 'publish' AND post_type <> 'revision'
           AND post_content REGEXP %s",
        '\\[wbcr_php_snippet[[:space:]]+id=["\']?' . $sid . '["\']?[] [:space:]]'
    ));
    if ($used) {
        WP_CLI::warning("Сниппет #$sid стоит в опубликованном контенте ($used) — в черновик не переводится");
        continue;
    }
    if ($sp->post_status === 'draft') {
        continue;
    }
    WP_CLI::log("Сниппет #$sid «{$sp->post_title}» [{$sp->post_status}] → draft");
    if (!$ws8_dry) {
        $wpdb->update($wpdb->posts, ['post_status' => 'draft'], ['ID' => $sid]);
        clean_post_cache($sid);
    }
    $ws8_drafted++;
}

foreach ($ws8_left as $sid => $where) {
    WP_CLI::log("Оставлено [wbcr_php_snippet id=$sid] (нет шорткода темы): " . implode('; ', array_unique($where)));
}

WP_CLI::success(sprintf('%s значений: %d, сниппетов в черновик: %d',
    $ws8_dry ? 'DRY-RUN, ничего не записано. Изменилось бы' : 'Изменено', $ws8_changed, $ws8_drafted));
