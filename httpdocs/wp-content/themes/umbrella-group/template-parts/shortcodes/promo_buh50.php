<?php
/**
 * Акция «−50% на первый месяц бухгалтерского обслуживания» (до 31.12.2026) —
 * та же, что первый слайд главной (new-main-first-screen.php, promo-slide-buh50):
 *
 *   [buh50_promo] — информационный блок в контенте страницы
 *                   «Бухгалтерское обслуживание» (/services/bukhgalterskie-uslugi/obsluzhivanie/, page 50),
 *                   стоит перед секцией «Для чего используется бухгалтерский аутсорсинг»;
 *   всплывающее окно на /services/bukhgalterskie-uslugi/ (page 26) — через 4 с
 *                   после загрузки, печатается в wp_footer только на этой странице.
 *
 * Кнопки открывают лайтбокс #leave-request-contact-form-lightbox (CF7 2886,
 * «Бесплатная консультация по бухгалтерскому обслуживанию») — он выводится
 * на всех страницах сайта.
 *
 * После UG_BUH50_UNTIL (по часовому поясу сайта) ни блок, ни окно не выводятся;
 * на случай страницы, застрявшей в кэше WP Rocket, тот же срок проверяет JS
 * (data-until) — окно не откроется, блок спрячется.
 *
 * Иллюстрация — assets/img/promo-50-banner.webp / .png (925×630, прозрачный фон),
 * общая со слайдом.
 */

defined('ABSPATH') || exit;

const UG_BUH50_UNTIL = '2026-12-31';          // последний день акции, включительно
const UG_BUH50_POPUP_PAGE = 26;               // /services/bukhgalterskie-uslugi/
const UG_BUH50_FORM = '#leave-request-contact-form-lightbox';
const UG_BUH50_IMG = '/wp-content/themes/umbrella-group/assets/img/promo-50-banner';

/** Акция ещё идёт (дата по часовому поясу сайта). */
function ug_buh50_active(): bool
{
    return wp_date('Y-m-d') <= UG_BUH50_UNTIL;
}

/** Конец акции для JS: полночь после последнего дня, по Новосибирску. */
function ug_buh50_until_attr(): string
{
    return UG_BUH50_UNTIL . 'T23:59:59+07:00';
}

function ug_buh50_icon(string $name): string
{
    $icons = [
        'clipboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M9 4.75H7A1.25 1.25 0 0 0 5.75 6v13A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V6A1.25 1.25 0 0 0 17 4.75h-2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><rect x="9" y="3.25" width="6" height="3" rx="1" stroke="currentColor" stroke-width="1.8"/><path d="M9 11h6M9 14.5h6M9 18h3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'shield-check' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'headset' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4.75 14v-2a7.25 7.25 0 0 1 14.5 0v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><rect x="3.75" y="13" width="4" height="5.5" rx="1.25" stroke="currentColor" stroke-width="1.8"/><rect x="16.25" y="13" width="4" height="5.5" rx="1.25" stroke="currentColor" stroke-width="1.8"/><path d="M18.25 18.5v.25a2.5 2.5 0 0 1-2.5 2.5H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'calendar' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><rect x="3.75" y="5.25" width="16.5" height="15" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3.75 9.75h16.5M8 3.5v3.5M16 3.5v3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8 13.5h.01M12 13.5h.01M16 13.5h.01M8 16.75h.01M12 16.75h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
        'arrow' => '<svg width="24" height="12" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M1 6h21M17 1l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'close' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M4 4l12 12M16 4 4 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
    ];
    return $icons[$name] ?? '';
}

/** Тексты — как на слайде главной. */
function ug_buh50_features(): string
{
    $items = [
        ['clipboard', 'Ведение учёта', 'Без ошибок и&nbsp;штрафов'],
        ['shield-check', 'Налоговый контроль', 'С&nbsp;учётом актуальных требований'],
        ['headset', 'Поддержка экспертов', 'По&nbsp;всем вопросам вашего бизнеса'],
    ];
    $html = '<ul class="ug-buh50__features">';
    foreach ($items as [$icon, $title, $text]) {
        $html .= '<li class="ug-buh50__feature"><span class="ug-buh50__feature-icon">' . ug_buh50_icon($icon) . '</span>'
            . '<span class="ug-buh50__feature-body"><span class="ug-buh50__feature-title">' . $title . '</span>'
            . '<span class="ug-buh50__feature-text">' . $text . '</span></span></li>';
    }
    return $html . '</ul>';
}

function ug_buh50_note(): string
{
    return '<div class="ug-buh50__note"><span class="ug-buh50__note-icon">' . ug_buh50_icon('calendar') . '</span>'
        . '<p>Действует при заключении договора до&nbsp;31.12.2026 для клиентов, ранее не&nbsp;стоявших на&nbsp;бухгалтерском обслуживании в&nbsp;ГК&nbsp;Umbrella&nbsp;Group.</p></div>';
}

const UG_BUH50_ALT = 'Бухгалтерская отчётность, книга «Бухучёт» и калькулятор — скидка 50% на первый месяц бухгалтерского обслуживания';

/* ── блок [buh50_promo] ─────────────────────────────────────────────────── */

add_shortcode('buh50_promo', static function (): string {
    if (!ug_buh50_active()) {
        return '';
    }
    ug_enqueue_style('assets/css/blocks/promo_buh50.css');
    ug_enqueue_script('assets/js/blocks/promo_buh50.js', [], ['in_footer' => true, 'strategy' => 'defer']);

    $img = UG_BUH50_IMG;
    $alt = esc_attr(UG_BUH50_ALT);
    $until = ug_buh50_until_attr();
    $form = UG_BUH50_FORM;
    $features = ug_buh50_features();
    $note = ug_buh50_note();
    $arrow = ug_buh50_icon('arrow');
    // Нативный loading="lazy" мимо lazy-load Flatsome: тот переписал бы <img> на
    // заглушку + data-src, а <source> webp оставил бы как есть — и webp грузился
    // бы сразу. ug_eager_img возвращает разметку на место после the_content.
    $picture = ug_eager_img('<picture><source srcset="' . $img . '.webp" type="image/webp">'
        . '<img src="' . $img . '.png" alt="' . $alt . '" width="925" height="630" loading="lazy" decoding="async"></picture>');

    return <<<EOT
        <section class="ug-buh50 ug-buh50--block" data-buh50-block data-until="$until" aria-labelledby="ug-buh50-block-title">
            <div class="ug-buh50__inner">
                <div class="ug-buh50__card">
                    <div class="ug-buh50__text">
                        <p class="ug-buh50__kicker">Акция до&nbsp;31&nbsp;декабря 2026</p>
                        <h2 class="ug-buh50__title" id="ug-buh50-block-title"><span class="ug-buh50__accent">&minus;50%</span> на&nbsp;первый месяц бухгалтерского обслуживания</h2>
                        <p class="ug-buh50__lead">Передайте учёт экспертам и&nbsp;начните сотрудничество на&nbsp;выгодных условиях.</p>
                        $features
                        <a href="$form" class="button primary lowercase ug-buh50__button" data-buh50-cta>Получить скидку 50% $arrow</a>
                    </div>
                    <div class="ug-buh50__media">
                        $picture
                        $note
                    </div>
                </div>
            </div>
        </section>
        EOT;
});

/* ── всплывающее окно на странице бухгалтерских услуг ───────────────────── */

add_action('wp', static function (): void {
    if (is_admin() || !is_page(UG_BUH50_POPUP_PAGE) || !ug_buh50_active()) {
        return;
    }
    ug_enqueue_style('assets/css/blocks/promo_buh50.css');
    ug_enqueue_script('assets/js/blocks/promo_buh50.js', [], ['in_footer' => true, 'strategy' => 'defer']);
    add_action('wp_footer', 'ug_buh50_print_popup', 5);
});

function ug_buh50_print_popup(): void
{
    $img = UG_BUH50_IMG;
    $alt = esc_attr(UG_BUH50_ALT);
    $until = ug_buh50_until_attr();
    $form = UG_BUH50_FORM;
    $features = ug_buh50_features();
    $note = ug_buh50_note();
    $arrow = ug_buh50_icon('arrow');
    $close = ug_buh50_icon('close');

    // Картинка без src: адрес подставляет JS при открытии окна (data-src),
    // чтобы 80 КБ не грузились тем, кто окна не увидит.
    echo <<<EOT
        <dialog class="ug-buh50 ug-buh50--popup" data-buh50-popup data-until="$until" data-delay="4000" tabindex="-1" aria-labelledby="ug-buh50-popup-title" aria-describedby="ug-buh50-popup-lead">
            <div class="ug-buh50__card">
                <button type="button" class="ug-buh50__close" data-buh50-close aria-label="Закрыть">$close</button>
                <div class="ug-buh50__media">
                    <picture>
                        <source data-srcset="$img.webp" type="image/webp">
                        <img data-src="$img.png" alt="$alt" width="925" height="630" decoding="async">
                    </picture>
                </div>
                <div class="ug-buh50__text">
                    <p class="ug-buh50__kicker">Акция до&nbsp;31&nbsp;декабря 2026</p>
                    <h2 class="ug-buh50__title" id="ug-buh50-popup-title">Бухгалтерское обслуживание <span class="ug-buh50__accent">&minus;50% на&nbsp;первый месяц</span></h2>
                    <p class="ug-buh50__lead" id="ug-buh50-popup-lead">Передайте учёт экспертам и&nbsp;начните сотрудничество на&nbsp;выгодных условиях.</p>
                    $features
                    <div class="ug-buh50__actions">
                        <a href="$form" class="button primary lowercase ug-buh50__button" data-buh50-cta>Получить скидку 50% $arrow</a>
                        <a href="/services/bukhgalterskie-uslugi/obsluzhivanie/" class="ug-buh50__more">Подробнее об&nbsp;обслуживании</a>
                    </div>
                    $note
                </div>
            </div>
        </dialog>
        EOT;
}
