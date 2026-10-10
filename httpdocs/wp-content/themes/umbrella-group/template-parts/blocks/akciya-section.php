<?php
/**
 * Секция страницы акции-лендинга (akciya_tz, akciya_pb): шапка + тело одного
 * из трёх видов. Классы — BEM от префикса блока («akciya-tz-benefits» →
 * akciya-tz-benefits__head, __cards, __card …), CSS у каждой акции свой.
 *
 * $args:
 *  block   string  префикс классов, он же класс <section>
 *  kicker  string  надзаголовок (HTML)
 *  title   string  заголовок h2 (HTML)
 *  lead    ?string текст справа от заголовка (HTML); null — шапка без обёртки
 *  head    string  класс обёртки шапки с lead: 'head' | 'head-row'
 *  layout  string  'cards' | 'steps' | 'numbered'
 *  items   array   cards — [icon, title, text]; steps, numbered — [title, text]
 *  icons   array   имя → SVG (cards; доверенные константы из кода темы, не экранируются)
 *  arrow   string  SVG стрелки между шагами (steps)
 *  after   string  HTML внутри <section> после тела (сноска, лайтбокс)
 *
 * Тексты — wp_kses_post: в них <br>, <span class>, &nbsp;.
 */

$b = esc_attr($args['block']);
$kicker = wp_kses_post($args['kicker']);
$title = wp_kses_post($args['title']);
$lead = $args['lead'] ?? null;
$items = $args['items'] ?? [];
$layout = $args['layout'];
$container = $layout === 'cards' ? 'cards' : 'items';
?>
<section class="<?= $b ?>">
<?php if ($lead === null) : ?>
    <p class="<?= $b ?>__kicker"><?= $kicker ?></p>
    <h2 class="<?= $b ?>__title"><?= $title ?></h2>
<?php else : ?>
    <div class="<?= $b ?>__<?= esc_attr($args['head'] ?? 'head') ?>">
        <div class="<?= $b ?>__head-left">
            <p class="<?= $b ?>__kicker"><?= $kicker ?></p>
            <h2 class="<?= $b ?>__title"><?= $title ?></h2>
        </div>
        <p class="<?= $b ?>__lead"><?= wp_kses_post($lead) ?></p>
    </div>
<?php endif; ?>
    <div class="<?= $b ?>__<?= $container ?>">
<?php foreach ($items as $index => $item) : ?>
<?php if ($layout === 'cards') : ?>
        <div class="<?= $b ?>__card">
            <div class="<?= $b ?>__icon"><?= $args['icons'][$item['icon']] ?? '' ?></div>
            <p class="<?= $b ?>__card-title"><?= wp_kses_post($item['title']) ?></p>
            <p class="<?= $b ?>__card-text"><?= wp_kses_post($item['text']) ?></p>
        </div>
<?php elseif ($layout === 'steps') : ?>
        <div class="<?= $b ?>__item">
            <div class="<?= $b ?>__head">
                <div class="<?= $b ?>__badge"><?= sprintf('%02d', $index + 1) ?></div>
                <div class="<?= $b ?>__line"><?= $args['arrow'] ?></div>
            </div>
            <p class="<?= $b ?>__item-title"><?= wp_kses_post($item['title']) ?></p>
            <p class="<?= $b ?>__item-text"><?= wp_kses_post($item['text']) ?></p>
        </div>
<?php else : ?>
        <div class="<?= $b ?>__item">
            <div class="<?= $b ?>__number"><?= sprintf('%02d', $index + 1) ?></div>
            <div class="<?= $b ?>__body">
                <p class="<?= $b ?>__item-title"><?= wp_kses_post($item['title']) ?></p>
                <p class="<?= $b ?>__item-text"><?= wp_kses_post($item['text']) ?></p>
            </div>
        </div>
<?php endif; ?>
<?php endforeach; ?>
    </div>
<?= $args['after'] ?? '' ?>
</section>
