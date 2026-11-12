<?php
/**
 * Блог /blog/ (страница записей, page_for_posts = 1979) — шаблон home.php.
 *
 * Тем же шаблоном рисуются:
 *  - рубрики-направления — дочерние рубрики «Блога» (term 1):
 *    /category/blog/<slug>/, фильтр «Направления» слева — обычные ссылки на них;
 *  - поиск по статьям — /?s=…&post_type=post (форма справа от h1).
 * Остальные архивы (метки, «Полезное из блога», «Новости», /category/blog/)
 * и общий поиск сайта — прежним archive.php / search.php.
 *
 * Порядок направлений — порядок рубрик в плагине Category Order
 * (taxonomy-terms-order, перетаскиванием в админке). Пустые рубрики не
 * показываются. Пермалинки записей не меняются, пока у записи отмечен
 * «Блог»: %category% берёт рубрику с меньшим ID, а у «Блога» он 1.
 *
 * На странице 6 статей (UG_BLOG_PER_PAGE, pre_get_posts только для этих
 * запросов). «Задать вопрос» — окно #blog-ask-form с CF7 11210 (home.php).
 *
 * Сортировка «Сначала новые / старые» — ?order=asc, ссылка с rel=nofollow
 * (canonical AIOSEO у /blog/?order=asc — /blog/).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

const UG_BLOG_ROOT_CAT = 1;
const UG_BLOG_TITLE    = 'Блог для бизнеса';
const UG_BLOG_LEAD     = 'Разбираем вопросы учёта, налогов, лицензий и права';
const UG_BLOG_PER_PAGE = 6;

/** Дочерние рубрики «Блога» с записями, в порядке Category Order. */
function ug_blog_directions(): array
{
    static $dirs = null;
    if ($dirs === null) {
        $dirs = get_terms([
            'taxonomy'   => 'category',
            'parent'     => UG_BLOG_ROOT_CAT,
            'hide_empty' => true,
            'orderby'    => 'term_order',
        ]);
        $dirs = is_array($dirs) ? $dirs : [];
    }
    return $dirs;
}

/** Направление запроса (дочерняя рубрика «Блога») или null. */
function ug_blog_query_direction(WP_Query $q): ?WP_Term
{
    if (!$q->is_category()) {
        return null;
    }
    $term = $q->get_queried_object();
    return $term instanceof WP_Term && (int) $term->parent === UG_BLOG_ROOT_CAT ? $term : null;
}

/**
 * Страница блога: /blog/, направление или поиск по записям
 * (/?s=…&post_type=post, в направлении — ещё &category_name=<slug>).
 */
function ug_blog_query_is_blog(WP_Query $q): bool
{
    if ($q->is_search()) {
        return (array) $q->get('post_type') === ['post']
            && (!$q->is_category() || ug_blog_query_direction($q) !== null);
    }
    return $q->is_home() || ug_blog_query_direction($q) !== null;
}

function ug_is_blog_view(): bool
{
    return ug_blog_query_is_blog($GLOBALS['wp_query']);
}

/** Адрес раздела без поиска: направление или /blog/. */
function ug_blog_base_url(?WP_Term $dir): string
{
    return $dir ? get_category_link($dir) : get_permalink((int) get_option('page_for_posts'));
}

/** Направление записи для метки на карточке — первое по порядку из ug_blog_directions(). */
function ug_blog_post_direction(int $post_id): ?WP_Term
{
    $ids = wp_get_post_categories($post_id);
    foreach (ug_blog_directions() as $term) {
        if (in_array($term->term_id, $ids, true)) {
            return $term;
        }
    }
    return null;
}

/** «23 октября» в текущем году, «21 апреля 2025» — в прошлые. */
function ug_blog_date(WP_Post $post): string
{
    $ts = (int) get_post_time('U', true, $post);
    $format = wp_date('Y', $ts) === wp_date('Y') ? 'j F' : 'j F Y';
    return ug_ru_date($format, $ts);
}

/** «1 статья», «3 статьи», «11 статей». */
function ug_blog_count(int $n): string
{
    $m10 = $n % 10;
    $m100 = $n % 100;
    $word = $m10 === 1 && $m100 !== 11 ? 'статья'
        : ($m10 >= 2 && $m10 <= 4 && ($m100 < 12 || $m100 > 14) ? 'статьи' : 'статей');
    return $n . ' ' . $word;
}

/** Отрывок до ~$max знаков по границе слова, без разметки и шорткодов. */
function ug_blog_excerpt(WP_Post $post, int $max = 150): string
{
    $text = $post->post_excerpt !== '' ? $post->post_excerpt : strip_shortcodes($post->post_content);
    $text = trim((string) preg_replace('/\s+/u', ' ', wp_strip_all_tags($text)));
    if (mb_strlen($text) <= $max) {
        return $text;
    }
    $cut = mb_substr($text, 0, $max);
    $sp  = mb_strrpos($cut, ' ');
    $cut = $sp !== false && $sp > $max * 0.6 ? mb_substr($cut, 0, $sp) : $cut;
    return rtrim($cut, " ,.;:—–-") . '…';
}

/** Карточка записи в списке: вся карточка — ссылка на статью. */
function ug_blog_card(WP_Post $post): string
{
    $url   = esc_url(get_permalink($post));
    $title = esc_html(get_the_title($post));
    $thumb = get_post_thumbnail_id($post);
    $img   = $thumb
        ? wp_get_attachment_image($thumb, 'medium_large', false, [
            'class'   => 'blog-card__img',
            'alt'     => '',
            'sizes'   => '(max-width: 600px) 120px, 216px',
            'loading' => 'lazy',
        ])
        : '';
    $media = $img !== ''
        ? $img
        : '<span class="blog-card__ph" aria-hidden="true"></span>';
    $dir   = ug_blog_post_direction($post->ID);
    $label = $dir ? '<span class="blog-card__cat">' . esc_html($dir->name) . '</span>' : '';
    $exc   = esc_html(ug_blog_excerpt($post));
    $date  = esc_html(ug_blog_date($post));
    $iso   = esc_attr(get_post_time('c', false, $post));

    return <<<HTML
        <article class="blog-card">
            <a class="blog-card__link" href="$url">
                <span class="blog-card__media">$media</span>
                <span class="blog-card__body">
                    $label
                    <h3 class="blog-card__title">$title</h3>
                    <span class="blog-card__excerpt">$exc</span>
                    <time class="blog-card__date" datetime="$iso">$date</time>
                </span>
                <span class="blog-card__arrow" aria-hidden="true"></span>
            </a>
        </article>
        HTML;
}

/** Пагинация: ‹ 1 2 … 5 › со ссылками на /page/N/ (сохраняет ?order, ?s). */
function ug_blog_pagination(): string
{
    $links = paginate_links([
        'type'      => 'array',
        'mid_size'  => 1,
        'end_size'  => 1,
        'prev_text' => '<span aria-hidden="true">←</span><span class="screen-reader-text">Предыдущая страница</span>',
        'next_text' => '<span aria-hidden="true">→</span><span class="screen-reader-text">Следующая страница</span>',
    ]);
    if (!$links) {
        return '';
    }
    return '<nav class="blog-pager" aria-label="Страницы блога">' . implode('', $links) . '</nav>';
}

/** Основной запрос страницы блога (для pre_get_posts и posts_search). */
function ug_blog_is_blog_query(WP_Query $q): bool
{
    return !is_admin() && $q->is_main_query() && ug_blog_query_is_blog($q);
}

// По 6 статей на страницу только на страницах блога; общий posts_per_page (10) не трогаем.
// Поиск по блогу — по дате, как список (не по релевантности), ?order=asc работает и в нём.
add_action('pre_get_posts', function (WP_Query $q): void {
    if (ug_blog_is_blog_query($q)) {
        $q->set('posts_per_page', UG_BLOG_PER_PAGE);
        if ($q->is_search()) {
            $q->set('orderby', 'date');
        }
    }
});

// Поиск по блогу — только по заголовкам: каждое слово запроса — подстрока
// заголовка («бухгалт» находит «Бухгалтерия»). Регистр не важен: сравнивает
// MySQL по collation колонки (на проде utf8mb4_unicode_ci). Общий поиск сайта
// (/?s= без post_type=post) ищет, как раньше, по заголовку, отрывку и тексту.
add_filter('posts_search', function (string $search, WP_Query $q): string {
    if (!ug_blog_is_blog_query($q) || !$q->is_search()) {
        return $search;
    }
    global $wpdb;
    $where = [];
    foreach ((array) $q->get('search_terms') as $term) {
        $term = ltrim((string) $term, '-');
        if ($term !== '') {
            $where[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($term) . '%');
        }
    }
    return $where ? ' AND (' . implode(' AND ', $where) . ') ' : $search;
}, 10, 2);

// Пустой запрос (/?s=&post_type=post, одни пробелы) — не поиск: на раздел без поиска.
add_action('template_redirect', function (): void {
    if (!isset($_GET['s']) || trim(wp_unslash((string) $_GET['s'])) !== '' || !ug_is_blog_view() || !is_search()) {
        return;
    }
    $url = ug_blog_base_url(ug_blog_query_direction($GLOBALS['wp_query']));
    if (strtolower((string) get_query_var('order')) === 'asc') {
        $url = add_query_arg('order', 'asc', $url);
    }
    wp_safe_redirect($url, 302);
    exit;
});

// Направления открыты для индексации. В AIOSEO Lite noindex у рубрик один на
// всю таксономию (у «Новостей», «Полезного из блога», /category/blog/ он
// остаётся), правила для отдельной рубрики нет — поэтому фильтр. Страницы
// 2, 3… не трогаем: их noindex, nofollow ставит AIOSEO, как у /blog/page/N/.
add_filter('aioseo_robots_meta', function ($attributes) {
    if (!is_array($attributes) || !is_category() || is_search() || is_paged()) {
        return $attributes;
    }
    $term = get_queried_object();
    if ($term instanceof WP_Term && (int) $term->parent === UG_BLOG_ROOT_CAT) {
        unset($attributes['noindex']);
    }
    return $attributes;
});

// Направления и поиск по записям — тем же шаблоном, что и /blog/.
add_filter('category_template', function (string $template): string {
    return ug_is_blog_view() ? (locate_template('home.php') ?: $template) : $template;
});
add_filter('search_template', function (string $template): string {
    return ug_is_blog_view() ? (locate_template('home.php') ?: $template) : $template;
});

// CSS страницы — в <head>, сразу после ug-main; живой поиск — в подвале.
add_action('wp', function (): void {
    if (ug_is_blog_view()) {
        ug_head_style('assets/css/pages/blog.css');
    }
});
add_action('wp_enqueue_scripts', function (): void {
    if (ug_is_blog_view()) {
        ug_enqueue_script('assets/js/pages/blog.js');
    }
});
