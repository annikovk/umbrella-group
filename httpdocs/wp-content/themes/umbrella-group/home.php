<?php
/**
 * Блог /blog/, его направления и поиск по статьям — см. inc/blog.php.
 *
 * Сверху крошки, h1 и подзаголовок, справа поиск по статьям. Ниже две
 * колонки: слева «Направления» (дочерние рубрики «Блога») и «Не нашли
 * ответ? Задать вопрос» — окно #blog-ask-form с формой «Задать вопрос FAQ»
 * (CF7 11210, как на /faq/), справа «Последние публикации»
 * карточками и пагинация. Фильтр, поиск, сортировка и страницы — обычные
 * ссылки и GET-форма, без JS.
 */

defined('ABSPATH') || exit;

$ug_blog_url  = get_permalink((int) get_option('page_for_posts'));
$ug_term      = is_category() ? get_queried_object() : null;
$ug_is_search = is_search();
$ug_query     = get_search_query();
$ug_asc       = strtolower((string) get_query_var('order')) === 'asc';

if ($ug_term) {
    $ug_h1   = $ug_term->name;
    $ug_lead = trim(wp_strip_all_tags(term_description($ug_term))) ?: 'Статьи блога Umbrella Group по направлению «' . $ug_term->name . '»';
} elseif ($ug_is_search) {
    $ug_h1   = 'Поиск по статьям';
    $ug_lead = $GLOBALS['wp_query']->found_posts
        ? sprintf('По запросу «%s» — %s', $ug_query, ug_blog_count($GLOBALS['wp_query']->found_posts))
        : sprintf('По запросу «%s» ничего не нашлось', $ug_query);
} else {
    $ug_h1   = UG_BLOG_TITLE;
    $ug_lead = UG_BLOG_LEAD;
}

// Сортировка: ссылка ведёт на первую страницу текущего раздела (поиск — с запросом).
$ug_sort_base = remove_query_arg(['order', 'paged'], get_pagenum_link(1, false));
$ug_sort_url  = $ug_asc ? $ug_sort_base : add_query_arg('order', 'asc', $ug_sort_base);

$ug_crumbs = ['<a href="' . esc_url(home_url('/')) . '">Главная</a>'];
if ($ug_term || $ug_is_search || is_paged()) {
    $ug_crumbs[] = '<a href="' . esc_url($ug_blog_url) . '">Блог</a>';
}
if ($ug_term) {
    $ug_crumbs[] = esc_html($ug_term->name);
} elseif ($ug_is_search) {
    $ug_crumbs[] = 'Поиск';
} elseif (!is_paged()) {
    $ug_crumbs[] = 'Блог';
}
if (is_paged()) {
    $ug_crumbs[] = 'Страница ' . (int) get_query_var('paged');
}
$ug_sep = ' <span class="blog-crumbs__sep">›</span> ';

get_header();
do_action('flatsome_before_page'); ?>
<div id="content" class="content-area page-wrapper blog-page" role="main">
    <div class="blog-page__inner">
        <div class="blog-crumbs breadcrumbs"><?php echo implode($ug_sep, $ug_crumbs); ?></div>

        <header class="blog-head">
            <div class="blog-head__text">
                <h1 class="blog-head__title"><?php echo esc_html($ug_h1); ?></h1>
                <p class="blog-head__lead"><?php echo esc_html($ug_lead); ?></p>
            </div>
            <form class="blog-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="blog-search-q">Поиск по статьям</label>
                <input class="blog-search__input" id="blog-search-q" type="search" name="s"
                       value="<?php echo esc_attr($ug_query); ?>" placeholder="Поиск по статьям" autocomplete="off">
                <input type="hidden" name="post_type" value="post">
                <button class="blog-search__btn" type="submit" aria-label="Найти"></button>
            </form>
        </header>

        <div class="blog-layout">
            <aside class="blog-side">
                <nav class="blog-nav" aria-label="Направления блога">
                    <p class="blog-nav__title">Направления</p>
                    <ul class="blog-nav__list">
                        <li><a class="blog-nav__item<?php echo !$ug_term ? ' is-active' : ''; ?>" href="<?php echo esc_url($ug_blog_url); ?>"<?php echo !$ug_term && !$ug_is_search ? ' aria-current="page"' : ''; ?>>Все статьи</a></li>
                        <?php foreach (ug_blog_directions() as $ug_dir) :
                            $ug_on = $ug_term && $ug_term->term_id === $ug_dir->term_id; ?>
                            <li><a class="blog-nav__item<?php echo $ug_on ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_category_link($ug_dir)); ?>"<?php echo $ug_on ? ' aria-current="page"' : ''; ?>><?php echo str_replace(' и ', ' и&nbsp;', esc_html($ug_dir->name)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <div class="blog-ask">
                    <p class="blog-ask__text">Не нашли ответ?</p>
                    <a class="blog-ask__link" href="#blog-ask-form">Задать вопрос</a>
                </div>
            </aside>
            <script>
                // Телефон: лента направлений прокручена к активному чипу.
                (function () {
                    var a = document.querySelector('.blog-nav__item.is-active'), l = a && a.closest('.blog-nav__list');
                    if (l && l.scrollWidth > l.clientWidth) { l.scrollLeft = a.parentNode.offsetLeft - 15; }
                })();
            </script>

            <section class="blog-main">
                <div class="blog-main__head">
                    <h2 class="blog-main__title"><?php echo $ug_is_search ? 'Найденные статьи' : 'Последние публикации'; ?></h2>
                    <?php if (have_posts() && $GLOBALS['wp_query']->found_posts > 1) : ?>
                        <a class="blog-sort<?php echo $ug_asc ? ' is-asc' : ''; ?>" href="<?php echo esc_url($ug_sort_url); ?>" rel="nofollow"><?php echo $ug_asc ? 'Сначала старые' : 'Сначала новые'; ?></a>
                    <?php endif; ?>
                </div>

                <?php if (have_posts()) : ?>
                    <div class="blog-list">
                        <?php while (have_posts()) : the_post();
                            echo ug_blog_card(get_post());
                        endwhile; ?>
                    </div>
                    <?php echo ug_blog_pagination(); ?>
                <?php else : ?>
                    <div class="blog-empty">
                        <p>Статей не нашлось. Попробуйте другой запрос или посмотрите <a href="<?php echo esc_url($ug_blog_url); ?>">все статьи</a>.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
    <?php
    // Окно «Задать вопрос» — форма «Задать вопрос FAQ» (CF7 11210), как на /faq/.
    echo do_shortcode('[lightbox id="blog-ask-form" width="400px" padding="0px"]'
        . '<div class="blog-ask-box">'
        . '[contact-form-7 id="11210" title="Форма Задать вопрос FAQ"]</div>[/lightbox]'); ?>
</div>
<?php
do_action('flatsome_after_page');
get_footer();
