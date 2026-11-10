<?php
/**
 * Пункты списка мессенджеров (<li>) — выпадашка «Связаться в мессенджере» в
 * шапке и мобильная панель. Ссылки те же, что в подвале (блок «Мы в
 * соцсетях»): Telegram, Max, WhatsApp.
 */
$img = esc_url(get_theme_file_uri('assets/img')) . '/';
$messengers = [
    ['Telegram', 'https://t.me/umbrella_club_nsk', 'tg-img.png'],
    ['Max', 'https://max.ru/u/f9LHodD0cOJIOXNE7YL84YaWLr0Fmn7wgpMChu5575DDdYM9wCgFx0Dmzuw', 'max-icon.webp'],
    ['WhatsApp', 'https://api.whatsapp.com/send?phone=79137202634', 'whatsapp.svg'],
];
foreach ($messengers as [$name, $url, $icon]) : ?>
    <li><a class="ugh-msg__item" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer nofollow" data-label="<?php echo esc_attr($name); ?>"><img src="<?php echo $img . $icon; ?>" width="28" height="28" alt="" loading="lazy" decoding="async"><span><?php echo esc_html($name); ?></span></a></li>
<?php endforeach;
