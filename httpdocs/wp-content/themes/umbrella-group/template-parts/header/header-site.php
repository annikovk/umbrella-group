<?php
/**
 * Шапка сайта — у всех страниц, включая «Открытие бизнеса под ключ» (14148).
 *
 * Сверху вниз (макет 2026-11, вариант C): строка меню (.ugh-nav, «Акции» —
 * красной пилюлей); ряд в одну строку: логотип | город и часы | выпадашка
 * мессенджеров | телефон с «Показать» и «Заказать звонок»
 * (#masthead); под ними — серая полоса разделов услуг (.ugh-svc: меню
 * primary с выпадашками, вместо прежней чёрной #wide-nav Flatsome), поэтому
 * «Услуги» в строке меню — простая ссылка без своей панели. В залипании
 * строка меню прячется: остаются компактный ряд с логотипом и полоса услуг.
 * CSS — assets/css/header-site.css, JS — assets/js/header-site.js
 * (inc/layout.php). На ≤ 849px верхний ряд — логотип, телефон и бургер
 * (#main-menu Flatsome), мессенджеры — в мобильной панели
 * (template-parts/overlays/overlay-menu.php → element-sidebar-extra).
 *
 * ⚠️ Подмена номеров — тег GTM (calltracking): ищет a[href*="tel:"] внутри
 * #masthead. Ссылка с цифрами меняется целиком через .html() (только если в
 * тексте есть «+»), у иконки-ссылки — только href. Поэтому внутри <a> с
 * номером — только текст, иконки снаружи. «+7 (383) Показать» — та же
 * разметка, что ставит сам тег (span.phoneShow с phonevalue): с подменой тег
 * перерисует её своим номером и сам раскроет по клику, без подмены раскрывает
 * header-site.js.
 *
 * Меню: строка меню — здесь, в $ug_nav; выпадашка «О компании» — дети
 * пункта /about/ меню top_bar_nav; полоса услуг — дерево меню primary (оба
 * правятся в «Внешний вид → Меню», как и раньше).
 */

$ug_phone      = '+7 (383) 373-17-17';
$ug_phone_href = 'tel:+73833731717';
$ug_logo       = (string) get_theme_mod('site_logo', '');
$ug_site_name  = get_bloginfo('name', 'display');

/** Дерево меню по location: [ ['title','url','children'=>[…]], … ]. */
$ug_menu_tree = static function (string $location): array {
    $locations = get_nav_menu_locations();
    if (empty($locations[$location])) {
        return [];
    }
    $items = wp_get_nav_menu_items($locations[$location]);
    if (!is_array($items)) {
        return [];
    }
    $nodes = [];
    foreach ($items as $item) {
        $nodes[(int) $item->ID] = [
            'title'    => trim(wp_strip_all_tags((string) $item->title)),
            'url'      => (string) $item->url,
            'parent'   => (int) $item->menu_item_parent,
            'children' => [],
        ];
    }
    $tree = [];
    foreach (array_keys($nodes) as $id) {
        $parent = $nodes[$id]['parent'];
        if ($parent && isset($nodes[$parent])) {
            $nodes[$parent]['children'][] = &$nodes[$id];
        } else {
            $tree[] = &$nodes[$id];
        }
    }
    return $tree;
};

$ug_path = static fn(string $url): string => untrailingslashit((string) wp_parse_url($url, PHP_URL_PATH)) . '/';

// «О компании»: дети пункта /about/ из top_bar_nav
$ug_about = [];
foreach ($ug_menu_tree('top_bar_nav') as $node) {
    if ($ug_path($node['url']) === '/about/') {
        $ug_about = $node['children'];
        break;
    }
}

// Полоса услуг: разделы primary и их подпункты как есть (с «Другое...»)
$ug_services = $ug_menu_tree('primary');

$ug_nav = [
    ['title' => 'О компании',        'url' => '/about/',          'drop' => $ug_about],
    ['title' => 'Блог',              'url' => '/blog/'],
    ['title' => 'Услуги',            'url' => '/services/'],
    ['title' => 'Стоимость',         'url' => '/price/'],
    ['title' => 'Акции',             'url' => '/akcii/',          'class' => 'ugh-menu__item--accent'],
    ['title' => 'Клиенты и отзывы',  'url' => '/about/clients/'],
    ['title' => 'Наши кейсы',        'url' => '/about/cases/'],
    ['title' => 'Контакты',          'url' => '/about/contacts/'],
];

$ug_current = $ug_path((string) ($_SERVER['REQUEST_URI'] ?? '/'));

$ug_chevron = '<svg class="ugh-chevron" width="10" height="6" viewBox="0 0 10 6" aria-hidden="true"><path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<nav class="ugh-nav" aria-label="Основное меню">
    <div class="container">
        <ul class="ugh-menu">
            <?php foreach ($ug_nav as $ug_item) :
                $ug_has_drop = !empty($ug_item['drop']);
                $ug_classes  = ['ugh-menu__item'];
                if ($ug_has_drop) {
                    $ug_classes[] = 'ugh-menu__item--drop';
                }
                if (!empty($ug_item['class'])) {
                    $ug_classes[] = $ug_item['class'];
                }
                if ($ug_current === $ug_item['url']) {
                    $ug_classes[] = 'is-current';
                }
                ?>
                <li class="<?php echo esc_attr(implode(' ', $ug_classes)); ?>">
                    <a class="ugh-menu__link" href="<?php echo esc_url(home_url($ug_item['url'])); ?>"<?php echo $ug_has_drop ? ' aria-haspopup="true"' : ''; ?>><?php echo esc_html($ug_item['title']); ?><?php echo $ug_has_drop ? $ug_chevron : ''; ?></a>
                    <?php if (!empty($ug_item['drop'])) : ?>
                        <ul class="ugh-drop">
                            <?php foreach ($ug_item['drop'] as $ug_sub) : ?>
                                <li><a href="<?php echo esc_url($ug_sub['url']); ?>"><?php echo esc_html($ug_sub['title']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<div id="masthead" class="ugh-top">
    <div class="container ugh-top__inner">

        <a class="ugh-logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home" title="<?php echo esc_attr($ug_site_name); ?>">
            <?php if ($ug_logo !== '') : ?>
                <img src="<?php echo esc_url($ug_logo); ?>" width="1168" height="226" alt="<?php echo esc_attr($ug_site_name); ?>" fetchpriority="high">
            <?php else : ?>
                <?php echo esc_html($ug_site_name); ?>
            <?php endif; ?>
        </a>

        <span class="ugh-sep ugh-sep--place ugh-desktop" aria-hidden="true"></span>

        <div class="ugh-place ugh-desktop">
            <svg class="ugh-ico" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s7-6.6 7-12.3A7 7 0 0 0 5 9.7C5 15.4 12 22 12 22z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="9.6" r="2.6" fill="none" stroke="currentColor" stroke-width="1.7"/></svg>
            <div class="ugh-place__text">
                <b>Новосибирск</b>
                <span>Пн–Пт: 09:00–18:00</span>
            </div>
        </div>

        <span class="ugh-sep ugh-desktop" aria-hidden="true"></span>

        <div class="ugh-msg ugh-desktop" data-ugh-dropdown>
            <button type="button" class="ugh-msg__toggle" aria-expanded="false" aria-controls="ugh-msg-list" aria-haspopup="true">
                <svg class="ugh-msg__icon" width="30" height="28" viewBox="0 0 30 28" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M11.5 2.5c5.2 0 9.5 3.4 9.5 7.7s-4.3 7.7-9.5 7.7c-1 0-2-.1-2.9-.4L4 19.6l1.2-4C3.5 14.3 2 12.4 2 10.2 2 5.9 6.3 2.5 11.5 2.5z"/><path d="M22.8 9.6c3.1 1.1 5.2 3.6 5.2 6.4 0 1.9-.9 3.6-2.4 4.8l1 3.5-4.1-1.9c-.8.2-1.7.3-2.6.3-3.4 0-6.3-1.5-7.8-3.7"/></g><g fill="currentColor"><circle cx="7.6" cy="10.2" r="1.2"/><circle cx="11.5" cy="10.2" r="1.2"/><circle cx="15.4" cy="10.2" r="1.2"/></g></svg>
                <span class="ugh-msg__label">Связаться<br>в мессенджере</span>
                <?php echo $ug_chevron; ?>
            </button>
            <ul class="ugh-msg__list" id="ugh-msg-list" hidden>
                <?php get_template_part('template-parts/header/partials/element', 'messengers'); ?>
            </ul>
        </div>

        <span class="ugh-sep ugh-desktop" aria-hidden="true"></span>

        <div class="ugh-tel ugh-desktop">
            <svg class="ugh-ico" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 2.8l2.6 3.9c.4.6.3 1.4-.2 1.9L7.5 10c1.3 2.7 3.6 5 6.4 6.4l1.4-1.5c.5-.5 1.3-.6 1.9-.2l3.9 2.6c.7.5.9 1.4.4 2.1l-1.3 1.9c-.7 1-1.9 1.5-3.1 1.2C10 20.9 3.1 14 1.5 6.9c-.3-1.2.2-2.4 1.2-3.1l1.9-1.3c.7-.5 1.6-.3 2 .3z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
            <a class="ugh-phone" href="<?php echo esc_attr($ug_phone_href); ?>">+7 (383) <span class="phoneShow" phonevalue="<?php echo esc_attr($ug_phone); ?>">Показать</span></a>
        </div>

        <a class="ugh-callback ugh-desktop" href="#header-contact-form-lightbox">Заказать звонок</a>

        <div class="ugh-mobile">
            <a class="ugh-mobile__phone" href="<?php echo esc_attr($ug_phone_href); ?>" aria-label="Позвонить: <?php echo esc_attr($ug_phone); ?>"><svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 2.8l2.6 3.9c.4.6.3 1.4-.2 1.9L7.5 10c1.3 2.7 3.6 5 6.4 6.4l1.4-1.5c.5-.5 1.3-.6 1.9-.2l3.9 2.6c.7.5.9 1.4.4 2.1l-1.3 1.9c-.7 1-1.9 1.5-3.1 1.2C10 20.9 3.1 14 1.5 6.9c-.3-1.2.2-2.4 1.2-3.1l1.9-1.3c.7-.5 1.6-.3 2 .3z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></a>
            <a class="ugh-mobile__burger" href="#" data-open="#main-menu" data-pos="<?php echo esc_attr((string) flatsome_option('mobile_overlay')); ?>" data-bg="main-menu-overlay" data-color="<?php echo esc_attr((string) flatsome_option('mobile_overlay_color')); ?>" aria-label="Меню" aria-controls="main-menu" aria-expanded="false"><svg width="22" height="16" viewBox="0 0 22 16" aria-hidden="true"><path d="M1 1h20M1 8h20M1 15h20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></a>
        </div>

    </div>
</div>

<nav class="ugh-svc" aria-label="Разделы услуг">
    <div class="container">
        <ul class="ugh-menu ugh-menu--svc">
            <?php foreach ($ug_services as $ug_section) :
                $ug_has_drop = !empty($ug_section['children']);
                $ug_classes  = ['ugh-menu__item'];
                if ($ug_has_drop) {
                    $ug_classes[] = 'ugh-menu__item--drop';
                }
                if ($ug_current === $ug_path($ug_section['url'])) {
                    $ug_classes[] = 'is-current';
                }
                ?>
                <li class="<?php echo esc_attr(implode(' ', $ug_classes)); ?>">
                    <a class="ugh-menu__link" href="<?php echo esc_url($ug_section['url']); ?>"<?php echo $ug_has_drop ? ' aria-haspopup="true"' : ''; ?>><?php echo esc_html($ug_section['title']); ?><?php echo $ug_has_drop ? $ug_chevron : ''; ?></a>
                    <?php if ($ug_has_drop) : ?>
                        <ul class="ugh-drop">
                            <?php foreach ($ug_section['children'] as $ug_sub) : ?>
                                <li><a href="<?php echo esc_url($ug_sub['url']); ?>"><?php echo esc_html($ug_sub['title']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
