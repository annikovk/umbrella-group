<?php
/**
 * Первый экран страницы услуги — разметка шорткода [su_welcome_screen].
 *
 * $args (готовит SU_welcome_screen::generate_shortcode()):
 *  breadcrumbs  string  HTML заголовка с крошками (umbrella_draw_header_with_breadcrumbs)
 *  background   string  <img> фона — LCP, уже через ug_eager_img()
 *  icon         string  <img> иконки преимуществ
 *  button       string  HTML кнопки (выводится дважды: show-for-medium и hide-for-medium)
 *  lightbox     string  [lightbox] формы кнопки или '' — один на блок
 *  flex_button  bool    десктопная кнопка в обёртке display:flex (страница медлицензии)
 *  offer, advantages[3], price, phone_text, phone, show_phone, specialist_text — из атрибутов
 *  ratings      array[2] ['icon', 'header', 'subheader']
 */

$offer = wp_kses_post($args['offer']);
$price = $args['price'] === null ? ''
    : '<p class="price">Стоимость услуги <span class="price_from_conditions_number">' . wp_kses_post($args['price']) . '</span></p>';
$phone = $args['phone'];
$formatted_phone = sprintf('+%s (%s) %s-%s-%s', substr($phone, 0, 1), substr($phone, 1, 3), substr($phone, 4, 3), substr($phone, 7, 2), substr($phone, 9));
$button = $args['button'];
$desktop_button = $button . $args['lightbox'];
?>
            <div class="su_welcome_screen">
                <div class="su_welcome_banner">
                        <div class="su_welcome_banner_content dark">
                            <?= $args['breadcrumbs'] ?>
                            <div class="su_welcome_banner_offer container">
                                <p><?= $offer ?></p> 
                            </div>      
                        </div>
                        <div class="su_welcome_banner_background">
                            <?= $args['background'] ?>
                        </div>
                </div>
            <div class="su_welcome_banner_overlay">
                <div class="show-for-medium"><?= $button ?></div>
                    <div class="su_welcome_banner_overlay_top">
<?php foreach ($args['advantages'] as $advantage) : ?>
                        <div class="advantage-block"> 
                            <div class="advantage-block-icon"><?= $args['icon'] ?></div> 
                            <div class="advantage-block-text"><?= wp_kses_post($advantage) ?></div>
                        </div>
<?php endforeach; ?>
                    </div>
                    <div class="su_welcome_banner_overlay_bottom">
                        <?= $price ?>
<?php if ($args['show_phone']) : ?>
                        <p>            <?= wp_kses_post($args['phone_text']) ?> <span class="su_welcome_banner_overlay_bottom_phone"><a href="tel:<?= esc_attr($phone) ?>"><?= esc_html($formatted_phone) ?></a></span></p>
<?php else : ?>
                        <p><?= wp_kses_post($args['phone_text']) ?></p>
<?php endif; ?>
                        <p><?= wp_kses_post($args['specialist_text']) ?></p>
<?php if ($args['flex_button']) : ?>
                <div style="display:flex;">
                    <div data-rr style="display:block" class="hide-for-medium"><?= $desktop_button ?></div>
                    
                </div>
<?php else : ?>
                    <div style="display:block" class="hide-for-medium"><?= $desktop_button ?></div>
<?php endif; ?>
                    </div>
            </div>
            <div class="su_welcome_screen_ratings show-for-medium">
<?php foreach ($args['ratings'] as $i => $rating) : ?>
                <div class="su_welcome_banner_ratings_icon_box">
                    <div class="su_welcome_banner_ratings_icon" > 
                        <img src="<?= esc_url($rating['icon']) ?>" alt="rating">
                    </div>
                    <div class="su_welcome_banner_ratings_header"><?= $i === 0 ? ' ' : '' ?><?= esc_html($rating['header']) ?> </div>
                    <div class="su_welcome_banner_ratings_subheader"><?= $i === 0 ? ' ' : '' ?><?= esc_html($rating['subheader']) ?><?= $i === 0 ? ' ' : '' ?></div>
                </div>
<?php endforeach; ?>
            </div>
        </div>
