<?php
/**
 * WS-7 спеки 1: разворачивание A/B-тегов <umbrella_ab_testN_variantM> в контенте.
 *
 * Тема до WS-7 на каждый the_content (приоритет 10, после wpautop) резала
 * невыбранные варианты фильтром umbrella_filter_ab_tests(). Варианты давно
 * зафиксированы ($expVar1 = 1 захардкожен), поэтому выбранные теги можно
 * развернуть в самом контенте один раз, а фильтр, cookie exp_mXyZ… и 9×9
 * preg_replace на рендер — удалить.
 *
 * Запуск (wp-cli, код со STDIN — на сервер файл класть не нужно):
 *
 *   стенд: docker exec -i -u www-data taxlab-local-web-1 \
 *            wp --path=/var/www/vhosts/taxlab.ru/httpdocs eval-file - [dry-run] [export=/путь] \
 *            < tools/migrations/01-ab-flatten.php
 *   прод:  ssh annikov.com 'sudo -u taxlabru /opt/plesk/php/8.3/bin/php /usr/local/bin/wp \
 *            --path=/var/www/vhosts/taxlab.ru/httpdocs eval-file - [dry-run] [export=/путь]' \
 *            < tools/migrations/01-ab-flatten.php
 *
 * Аргументы позиционные (`--dry-run` wp-cli не пропускает в eval-file):
 *   dry-run        — ничего не писать, только показать, что изменится, и проверить;
 *   export=<dir>   — куда выгрузить исходный post_content каждой записи до правки
 *                    (по умолчанию <tmp>/taxlab-ab-flatten-<дата>); в dry-run тоже.
 *
 * Как правится запись. Новый контент — тот же фильтр, применённый к сырому
 * post_content (до wpautop). Перед записью скрипт сверяет результат цепочки
 * the_content до раскрытия шорткодов (приоритеты ≤ 10: do_blocks, wptexturize,
 * wpautop, shortcode_unautop, insert-php, flatsome_contentfix…):
 *     цепочка(старый контент) + старый фильтр  ==  цепочка(новый контент) без фильтра.
 * Если wpautop на развёрнутом контенте расставляет <p>/<br> иначе — теги
 * разворачиваются по одному с подбором замены (см. ws7_flatten). Точное
 * совпадение предпочтительно, иначе («≈» в отчёте) допускается только то,
 * чего браузер не различает (ws7_ws): длина серий пробелов, пустой
 * комментарий <!-- -->, который скрипт ставит невидимым разделителем, и
 * автозакрытие <p> перед блоком. Если для какого-то тега замены нет, запись
 * НЕ меняется и печатается как FAIL (код выхода 1).
 *
 * На стенде (WS-7) так развёрнуто 32 записи: 28 опубликованных + 4 черновика;
 * HTML всех 301 URL × 2 UA совпал после нормализации (отличия — только
 * <!-- --> и явный </p> там, где раньше <p> закрывался перед <div>),
 * скриншоты 28 страниц × 1440/390m — 0.0000 %.
 *
 * Запись — прямым UPDATE post_content (без wp_update_post): не трогаются
 * post_modified, AIOSEO-таблица r17Y1_aioseo_posts и хуки save_post; затем
 * wp_save_post_revision() — новое состояние появляется в ревизиях, откат —
 * из ревизии или `wp post update <id> <export>/<id>.html`.
 *
 * Идемпотентно: записей с тегами нет — скрипт ничего не делает. Ревизии с
 * тегами остаются как история (восстановление старой ревизии вернёт теги —
 * после удаления фильтра из темы их надо будет развернуть повторным запуском).
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Запускать через wp eval-file\n");
    exit(1);
}

$ws7_dry = false;
$ws7_export = rtrim(sys_get_temp_dir(), '/') . '/taxlab-ab-flatten-' . gmdate('Ymd-His');
foreach ((array) ($args ?? []) as $ws7_arg) {
    if ($ws7_arg === 'dry-run' || $ws7_arg === '--dry-run') {
        $ws7_dry = true;
    } elseif (strncmp($ws7_arg, 'export=', 7) === 0) {
        $ws7_export = rtrim(substr($ws7_arg, 7), '/');
    } else {
        WP_CLI::error("Неизвестный аргумент: $ws7_arg (есть dry-run и export=<dir>)");
    }
}

/**
 * Выбранные варианты — ровно то, что возвращала umbrella_get_ab_test_tags()
 * темы при $expVar1 = 1: индекс массива = номер теста − 1.
 */
const WS7_CHOSEN = [
    'umbrella_ab_test1_variant2', 'umbrella_ab_test2_variant1', 'umbrella_ab_test3_variant2',
    'umbrella_ab_test4_variant2', 'umbrella_ab_test5_variant2', 'umbrella_ab_test6_variant2',
    'umbrella_ab_test7_variant1', 'umbrella_ab_test8_variant2', 'umbrella_ab_test9_variant2',
];

/** Невидимый разделитель, который скрипт может поставить на место тега. */
const WS7_MARK = '<!-- -->';

/** Копия umbrella_filter_ab_tests() темы до WS-7 (после WS-7 её в теме нет). */
function ws7_filter(string $content): string
{
    foreach (WS7_CHOSEN as $counter => $ab_tag) {
        $counter = $counter + 1;
        for ($i = 1; $i < 10; $i++) {
            $tag = 'umbrella_ab_test' . $counter . '_variant' . $i;
            $content = strtr($content, [
                "<p></$tag><br />" => "</$tag>",
                "<p><$tag><br />" => "<$tag>",
                "<p></$tag></p>" => "</$tag>",
                "<$tag></p>" => "<$tag>",
                "</$tag><br />" => "</$tag>",
                "<$tag><br />" => "<$tag>",
            ]);
            if ($tag !== $ab_tag) {
                $content = preg_replace('/<' . $tag . '>[\s\S]+?<\/' . $tag . '>/', '', $content);
            } else {
                $content = strtr($content, ["<$tag>" => '', "</$tag>" => '']);
            }
        }
    }
    return $content;
}

/**
 * Цепочка the_content до раскрытия шорткодов (приоритеты ≤ 10). $with_ab — вставить
 * старый фильтр туда, где он стоял (вместо umbrella_filter_ab_tests темы, если
 * она ещё подключена; иначе перед flatsome_contentfix, иначе в конец 10).
 */
function ws7_pipeline(string $content, bool $with_ab): string
{
    global $wp_filter, $shortcode_tags;
    $done = !$with_ab;
    foreach ($wp_filter['the_content']->callbacks as $prio => $cbs) {
        if ($prio > 10) {
            break;
        }
        foreach ($cbs as $cb) {
            $fn = $cb['function'];
            $is_theme_ab = $fn === 'umbrella_filter_ab_tests';
            if (!$done && ($is_theme_ab || $fn === 'flatsome_contentfix')) {
                $content = ws7_filter($content);
                $done = true;
            }
            if ($is_theme_ab) {
                continue;
            }
            // flatsome_contentfix в конце сам зовёт do_shortcode — только на
            // этот вызов шорткоды выключены: сравнивается текст до их
            // раскрытия (у Flatsome в разметке шорткодов случайные id).
            // Раньше выключать нельзя: wptexturize и shortcode_unautop
            // смотрят на список зарегистрированных шорткодов.
            $mute = $fn === 'flatsome_contentfix';
            if ($mute) {
                $saved_tags = $shortcode_tags;
                $shortcode_tags = [];
            }
            $content = call_user_func_array($fn, array_slice([$content], 0, (int) $cb['accepted_args']));
            if ($mute) {
                $shortcode_tags = $saved_tags;
            }
        }
    }
    if (!$done) {
        $content = ws7_filter($content);
    }
    return $content;
}

/**
 * Новый post_content, на котором цепочка без фильтра даёт $target, или null.
 *
 * Сначала — фильтр на сыром контенте целиком. Если wpautop на нём ставит
 * <p>/<br>/переносы иначе (теги стояли отдельными абзацами, и фильтр оставлял
 * от них висячие «<p>», «</p>», пустые строки), — по одному: первый тег в
 * контенте (выбранный — сам тег, невыбранный вариант — блок целиком)
 * заменяется одной из замен ws7_options(), при которой цепочка *со* старым
 * фильтром по-прежнему даёт $target — оставшиеся теги ещё обрабатывает
 * фильтр, так что каждая замена проверяется на настоящем выводе. Если дальше
 * тупик — возврат и следующая замена (ws7_step). Когда тегов не осталось,
 * фильтр ничего не делает, и цепочка без него даёт то же самое.
 */
function ws7_flatten(string $raw, string $target, array &$picked): ?string
{
    $picked = [];
    $simple = ws7_filter($raw);
    if (ws7_pipeline($simple, false) === $target) {
        return $simple;
    }
    $budget = 3000;
    $fail = '';
    $res = ws7_step($raw, 0, $target, ws7_ws($target), $budget, $fail);
    if ($res === null) {
        $picked = [$fail . ($budget < 0 ? ' (исчерпан предел перебора)' : '')];
        return null;
    }
    [$new, $picked] = $res;
    return $new;
}

/**
 * Один шаг перебора: первый тег начиная с $from. Для каждой замены (сначала
 * точное совпадение, потом «≈») — рекурсия на остаток; если остаток не
 * сходится ни при какой замене следующего тега, пробуется следующая замена
 * этого (возврат). Возвращает [контент, список замен] или null; $fail — самый
 * дальний тег, на котором перебор упёрся. $budget — предел прогонов цепочки.
 */
function ws7_step(string $cur, int $from, string $target, string $target_ws, int &$budget, string &$fail): ?array
{
    if (!preg_match('/<(\/?)(umbrella_ab_test(\d+)_variant\d+)>/', $cur, $m, PREG_OFFSET_CAPTURE, $from)) {
        return ws7_ws(ws7_pipeline($cur, false)) === $target_ws ? [$cur, []] : null;
    }
    $s = $m[0][1];
    $tag = $m[2][0];
    $e = $s + strlen($m[0][0]);
    if ($m[1][0] === '' && !in_array($tag, WS7_CHOSEN, true)
        && preg_match('/<\/' . $tag . '>/', $cur, $mc, PREG_OFFSET_CAPTURE, $e)) {
        $e = $mc[0][1] + strlen($mc[0][0]);   // невыбранный вариант — весь блок
    }
    $opts = ws7_options($cur, $s, $e);
    foreach ([true, false] as $exact) {
        foreach ($opts as $label => [$os, $oe, $rep]) {
            if (--$budget < 0) {
                break 2;
            }
            $try = substr($cur, 0, $os) . $rep . substr($cur, $oe);
            $out = ws7_pipeline($try, true);
            if ($exact ? $out !== $target : ws7_ws($out) !== $target_ws) {
                continue;
            }
            $rest = ws7_step($try, $os + strlen($rep), $target, $target_ws, $budget, $fail);
            if ($rest !== null) {
                array_unshift($rest[1], "<{$m[1][0]}$tag> → $label" . ($exact ? '' : ' ≈'));
                return $rest;
            }
        }
    }
    $line = substr_count(substr($cur, 0, $s), "\n") + 1;
    $fail = "<{$m[1][0]}$tag> (строка $line) — замены нет";
    return null;
}

/**
 * Для сверки «≈» — только то, чего браузер не различает:
 *  - серия пробельных символов → один пробел (вне <pre> так и рендерится;
 *    наличие пробела сравнивается: от него зависит и зазор между inline-
 *    элементами, и :empty — у Flatsome `p:empty{display:none}`, так что
 *    «<p></p>» скрыт, а «<p> </p>» занимает место своими margin);
 *  - WS7_MARK (пустой комментарий) — ничто (:empty комментарии не считает);
 *  - незакрытый <p> перед блоком = явно закрытый там же: по HTML5 <div>,
 *    <section>, <p>… закрывают открытый <p>; [row]/[section]/[col]/[gap]
 *    Flatsome раскрываются в <div>/<section>. «<p> [row]» ≡ «<p> </p> [row]».
 */
function ws7_ws(string $html): string
{
    $html = preg_replace('/\s+/', ' ', str_replace(WS7_MARK, '', $html));
    return preg_replace(
        '/<p>( ?)(?=\[(?:row|row_inner|section|col|col_inner|gap)[ \]]|<(?:div|section|p|ul|ol|h[1-6]|table|form|blockquote|hr)[ >\/])/',
        '<p>$1</p> ',
        $html
    );
}

/**
 * Замены для тега/блока [$s, $e): что поставить на его место и захватить ли
 * пробелы и перенос строки слева/справа. Порядок — от «просто убрать».
 */
function ws7_options(string $c, int $s, int $e): array
{
    // Контент частью в CRLF: \r — тоже «пробел» у конца строки.
    $ls = $s;
    while ($ls > 0 && strpos(" \t\r", $c[$ls - 1]) !== false) {
        $ls--;
    }
    $re = $e;
    while ($re < strlen($c) && strpos(" \t\r", $c[$re]) !== false) {
        $re++;
    }
    $spans = ['' => [$s, $e], 'пробелы' => [$ls, $re]];
    if ($ls > 0 && $c[$ls - 1] === "\n") {
        $spans['перенос слева'] = [$ls - 1, $re];
    }
    if ($re < strlen($c) && $c[$re] === "\n") {
        $spans['перенос справа'] = [$ls, $re + 1];
    }
    // Все пробелы и пустые строки слева/справа: абзац с тегом сливается с
    // соседним, как после фильтра (тег съедал <p>…<br /> вокруг себя).
    $la = $s;
    while ($la > 0 && ctype_space($c[$la - 1])) {
        $la--;
    }
    $rb = $e;
    while ($rb < strlen($c) && ctype_space($c[$rb])) {
        $rb++;
    }
    $spans['пустые строки слева'] = [$la, $re];
    $spans['пустые строки справа'] = [$ls, $rb];
    $spans['пустые строки вокруг'] = [$la, $rb];
    $reps = [
        'убрать' => '', '\n' => "\n", '<p>' => '<p>', '</p>' => '</p>', '<p> </p>' => '<p> </p>',
        'пробел' => ' ', '\n</p>' => "\n</p>", '<p>\n' => "<p>\n",
        // Пустой комментарий — невидимый разделитель: «]</p>» и «<p>[»
        // flatsome_contentfix склеивает, а висячий </p>/<p>, который
        // оставлял фильтр, нужно сохранить (браузер делает из него пустой <p>).
        WS7_MARK => WS7_MARK, '\n' . WS7_MARK => "\n" . WS7_MARK, WS7_MARK . '\n' => WS7_MARK . "\n",
        '<p>' . WS7_MARK => '<p>' . WS7_MARK, '<p>\n' . WS7_MARK => "<p>\n" . WS7_MARK,
    ];
    $out = [];
    foreach ($reps as $rl => $rep) {
        foreach ($spans as $sl => [$a, $b]) {
            $out[trim("$rl $sl")] = [$a, $b, $rep];
        }
    }
    return $out;
}

global $wpdb;
$ids = $wpdb->get_col(
    "SELECT ID FROM {$wpdb->posts}
     WHERE post_content LIKE '%<umbrella\\_ab\\_test%'
       AND post_type NOT IN ('revision')
       AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
     ORDER BY ID"
);

if (!$ids) {
    WP_CLI::success('A/B-тегов в контенте нет — менять нечего.');
    return;
}

WP_CLI::log(sprintf('%s: записей с A/B-тегами — %d', $ws7_dry ? 'DRY-RUN' : 'Миграция', count($ids)));
if (!is_dir($ws7_export) && !mkdir($ws7_export, 0700, true)) {
    WP_CLI::error("Не создать каталог экспорта $ws7_export");
}
WP_CLI::log("Экспорт исходного контента: $ws7_export");

$fails = 0;
$changed = 0;
foreach ($ids as $id) {
    $post = get_post((int) $id);
    $raw = $post->post_content;
    file_put_contents("$ws7_export/{$post->ID}.html", $raw);

    $GLOBALS['post'] = $post;
    setup_postdata($post);
    $target = ws7_pipeline($raw, true);
    $picked = [];
    $new = ws7_flatten($raw, $target, $picked);
    wp_reset_postdata();

    $tags = preg_match_all('/<umbrella_ab_test\d+_variant\d+>/', $raw);
    $line = sprintf('#%d %s [%s] %s: тегов %d, %d → %d байт',
        $post->ID, $post->post_type, $post->post_status, $post->post_name ?: '(без slug)',
        $tags, strlen($raw), $new === null ? 0 : strlen($new));
    if ($new === null) {
        $fails++;
        WP_CLI::warning("$line — FAIL: " . implode('; ', $picked) . ' — запись не тронута');
        continue;
    }
    if (preg_match('/umbrella_ab_test/', $new)) {
        $fails++;
        WP_CLI::warning("$line — FAIL: после разворачивания остались теги, запись не тронута");
        continue;
    }
    WP_CLI::log("$line — OK" . ($picked ? ': ' . implode('; ', $picked) : ''));
    if ($ws7_dry) {
        continue;
    }
    $wpdb->update($wpdb->posts, ['post_content' => $new], ['ID' => $post->ID]);
    clean_post_cache($post->ID);
    wp_save_post_revision($post->ID);
    $changed++;
}

if ($fails) {
    WP_CLI::error("Не развёрнуто: $fails, изменено: $changed");
}
WP_CLI::success($ws7_dry ? 'DRY-RUN: всё сходится, ничего не записано.' : "Изменено записей: $changed");
