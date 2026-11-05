<?php
/**
 * Блок «Заказать услугу» в статье — inc/post-service-cta.php.
 *
 * $args: variant ('full'|'compact'), title, url, price (может быть «»),
 * request — якорь попапа заявки. title «» — общий вариант без услуги.
 */

defined('ABSPATH') || exit;

$title   = (string) ($args['title'] ?? '');
$url     = (string) ($args['url'] ?? '');
$price   = (string) ($args['price'] ?? '');
$request = (string) ($args['request'] ?? '');

if (($args['variant'] ?? 'full') === 'compact') : ?>
<aside class="ug-pcta-line" aria-label="Услуга по теме статьи">
    <span class="ug-pcta-line__label">Услуга по теме:</span>
    <a class="ug-pcta-line__link" href="<?= esc_url($url) ?>"><?= esc_html($title) ?></a><?php if ($price !== '') : ?>
    <span class="ug-pcta-line__price"><?= esc_html($price) ?></span><?php endif; ?>
</aside>
<?php return; endif; ?>
<section class="ug-pcta<?= $title === '' ? ' ug-pcta--general' : '' ?>" aria-label="<?= $title === '' ? 'Бесплатная консультация' : 'Заказать услугу' ?>">
    <div class="ug-pcta__body">
<?php if ($title !== '') : ?>
        <p class="ug-pcta__label">Услуга по теме статьи</p>
        <p class="ug-pcta__title"><a href="<?= esc_url($url) ?>"><?= esc_html($title) ?></a></p>
<?php if ($price !== '') : ?>
        <p class="ug-pcta__price"><?= esc_html($price) ?></p>
<?php endif; ?>
        <p class="ug-pcta__text">Проконсультируем бесплатно: оценим вашу ситуацию, сроки и&nbsp;стоимость.</p>
<?php else : ?>
        <p class="ug-pcta__title">Нужна помощь специалиста?</p>
        <p class="ug-pcta__text">Оставьте заявку — перезвоним, бесплатно проконсультируем по&nbsp;вашему вопросу и&nbsp;подскажем, с&nbsp;чего начать.</p>
<?php endif; ?>
    </div>
    <div class="ug-pcta__actions">
        <a class="ug-pcta__btn" href="<?= esc_attr($request) ?>">Оставить заявку</a>
<?php if ($title !== '') : ?>
        <a class="ug-pcta__btn ug-pcta__btn--ghost" href="<?= esc_url($url) ?>">Подробнее об услуге</a>
<?php endif; ?>
    </div>
</section>
