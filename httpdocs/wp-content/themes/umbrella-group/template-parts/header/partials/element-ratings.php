<?php
/**
 * Элемент шапки «рейтинги» (header_elements_left = ratings, его зовёт Flatsome
 * без аргументов) и он же в варианте business ($args['variant'],
 * template-parts/header/header-main.php): рейтинг pravo.ru + WhatsApp из
 * опций ACF вместо лавров «Делового квартала» и кнопок Max/Telegram.
 */
$whatsAppText = get_field('__whatsapp_text', 'option');

if (($args['variant'] ?? '') === 'business') {
    $rating1 = umbrella_get_ratings('expert-header');
    $whatsAppUrl = get_field('__whatsapp_url', 'option');
    $whatsAppIconUrl = get_field('__whatsapp_icon', 'option');

    echo <<<EOHTML
        <div class="header-ratings-icons">
                <div class="header-ratings-icon-box hide-for-medium">
                    <div class="header-ratings-icon" style="background-image: url({$rating1['icon']});border-radius:0;  width: 47px;"> </div>
                    <div class="header-ratings-header" style="width: 194px;">{$rating1['header']}</div>
                </div>
                <div class="header-ratings-icon-box whatsapp-box">
                    <a href="{$whatsAppUrl}" class="header-ratings-icon" style="background-image: url({$whatsAppIconUrl});border-radius:0;  width: 48px;"> </a>

                    <a href="{$whatsAppUrl}" class="header-social-text"><b>{$whatsAppText}</b></a>
                </div>
            </div>

EOHTML;
    return;
}

// Max — uploads/max-icon.png (1080 px, 395 КБ), уменьшенный до 270 px (28 КБ)
// так же, как Chrome строит мип-уровни при уменьшении: два прохода бокс-фильтра
// 2×2 по premultiplied RGBA с отбрасыванием остатка, lossless webp. Иконка на
// экране — 28–96 физ. px, и браузер берёт для неё те же уровни 135/67/33,
// что строил из 1080 px, поэтому кадр совпадает попиксельно (PIL-ресемплы
// BOX/LANCZOS и SVG-перерисовка assets/img/max.svg — нет, видно на снимках).
$img = esc_url(get_theme_file_uri('assets/img')) . '/';

echo <<<EOHTML
        <div class="header-ratings-icons">
                <div class="header-ratings-icon-box hide-for-medium">
                    <div class="header-ratings-icon" style="background-image: url(/wp-content/uploads/2019/10/laurel-1.svg);border-radius: 100%;width: 50px;background-color: #424242;height: 65px;height: 50px;"> </div>
                    <div class="header-ratings-header" style="width: 316px;">1 место в отрасли «Корпоративное право» <span style="font-weight: 500;">рейтинг «Делового квартала», Новосибирск, 2025</span></div>
                </div>
                <div class="header-ratings-icon-box whatsapp-box">
                    <a href="https://max.ru/u/f9LHodD0cOJIOXNE7YL84YaWLr0Fmn7wgpMChu5575DDdYM9wCgFx0Dmzuw" class="header-ratings-icon" style="background-image: url({$img}max-icon.webp);border-radius:0;  width: 32px;"> </a>

                    <a href="https://max.ru/u/f9LHodD0cOJIOXNE7YL84YaWLr0Fmn7wgpMChu5575DDdYM9wCgFx0Dmzuw" class="header-social-text"><b>{$whatsAppText}</b></a>
                </div>
                <div class="header-ratings-icon-box whatsapp-box">
                    <a href="https://t.me/umbrella_club_nsk" class="header-ratings-icon" style="background-image: url({$img}tg-img.png);border-radius:0;  width: 28px;"> </a>

                    <a href="https://t.me/umbrella_club_nsk" class="header-social-text"><b>{$whatsAppText}</b></a>
                </div>
            </div>

EOHTML;
