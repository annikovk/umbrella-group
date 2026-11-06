<?php
/**
 * [contacts_page] — страница «Контакты» (/about/contacts/, page 18).
 *
 * Сверху хлебные крошки, h1 и подзаголовок; под ними полоса из трёх колонок
 * с вертикальными разделителями: телефон, почта и кнопка «Обсудить задачу»
 * (открывает форму заявки из шапки — #header-contact-form-lightbox, как
 * «Заказать звонок» и кнопки [clients_cta], [cases]). Ниже две карточки
 * офисов и карта 2ГИС с двумя метками.
 * Шапку page.php с крошками и h1 страница не выводит — см. has_shortcode() там.
 *
 * Карта — тот же виджет 2ГИС «Фирмы на карте», что стоял на странице
 * ([block id="map"], блок 2769): те же карточки организаций и центр. Только
 * iframe рисуется сразу здесь, а не через DGWidgetLoader.js — тот пишет его
 * document.write'ом и не даёт сделать карту ниже 400px. «Показать на карте»
 * подставляет в src тот же виджет с центром на офисе
 * (assets/js/blocks/contacts_page.js); без JS ссылка ведёт на карточку
 * офиса в 2ГИС.
 */

defined('ABSPATH') || exit;

const UG_CONTACTS_PHONE = ['+73833731717', '+7 (383) 373-17-17'];
const UG_CONTACTS_EMAIL = 'contact@taxlab.ru';

/**
 * Офисы: id карточки в 2ГИС, заголовок, адрес, индекс, координаты метки —
 * из данных самого виджета (point карточки организации).
 */
const UG_CONTACTS_OFFICES = [
    [
        'org'    => '141265769353602',
        'title'  => 'Офис на&nbsp;правом берегу',
        'street' => 'ул.&nbsp;Максима Горького, 34',
        'schema' => 'ул. Максима Горького, д. 34',
        'zip'    => '630099',
        'lat'    => 55.026301,
        'lon'    => 82.914995,
    ],
    [
        'org'    => '141265769353601',
        'title'  => 'Офис на&nbsp;левом берегу',
        'street' => 'площадь Труда, 1, офис&nbsp;102',
        'schema' => 'площадь Труда, д. 1, офис 102',
        'zip'    => '630108',
        'lat'    => 54.995232,
        'lon'    => 82.868349,
    ],
];

/** Центр и масштаб карты при загрузке — как в блоке 2769; по клику на офис — zoom 16. */
const UG_CONTACTS_MAP_POS = ['lat' => 55.01247314008618, 'lon' => 82.89167404174806, 'zoom' => 13];

/** Адрес iframe виджета — так его собирает DGWidgetLoader.js. */
function ug_contacts_map_src(array $pos): string
{
    $options = [
        'pos' => $pos,
        'opt' => ['city' => 'novosibirsk'],
        'org' => implode(',', array_column(UG_CONTACTS_OFFICES, 'org')),
    ];
    return 'https://widgets.2gis.com/widget?type=firmsonmap&options=' . rawurlencode(wp_json_encode($options));
}

function ug_contacts_page_shortcode(): string
{
    [$tel, $phone] = UG_CONTACTS_PHONE;
    $email = UG_CONTACTS_EMAIL;
    $arrow = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $arrow_sm = str_replace(['width="22" height="22"', 'stroke-width="2"'], ['width="18" height="18"', 'stroke-width="2.2"'], $arrow);
    $pin = '<svg class="contacts-office__pin" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 21.25s-6.75-5.9-6.75-11.5a6.75 6.75 0 0 1 13.5 0c0 5.6-6.75 11.5-6.75 11.5Z" stroke-linejoin="round"/><circle cx="12" cy="9.75" r="2.4" fill="currentColor" stroke="none"/></svg>';
    $mail = '<svg class="contacts-strip__icon" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="2.75" y="5.25" width="18.5" height="13.5" rx="2"/><path d="m3.5 6.5 8.5 6.75 8.5-6.75" stroke-linejoin="round"/></svg>';

    $offices = '';
    foreach (UG_CONTACTS_OFFICES as $o) {
        $link = 'https://2gis.ru/novosibirsk/firm/' . $o['org'];
        $offices .= '<div class="contacts-office" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">'
            . $pin
            . '<div class="contacts-office__body">'
            . '<h2 class="contacts-office__title">' . $o['title'] . '</h2>'
            . '<meta itemprop="streetAddress" content="' . esc_attr($o['schema']) . '">'
            . '<p class="contacts-office__street">' . $o['street'] . '</p>'
            . '<p class="contacts-office__city"><span itemprop="postalCode">' . $o['zip'] . '</span>, г.&nbsp;<span itemprop="addressLocality">Новосибирск</span></p>'
            . '<a class="contacts-office__map" href="' . esc_url($link) . '" target="_blank" rel="noopener"'
            . ' data-lat="' . $o['lat'] . '" data-lon="' . $o['lon'] . '">Показать на&nbsp;карте' . $arrow_sm . '</a>'
            . '</div></div>';
    }

    $map_src = esc_url(ug_contacts_map_src(UG_CONTACTS_MAP_POS));
    $map_options = esc_attr(wp_json_encode([
        'pos' => UG_CONTACTS_MAP_POS,
        'opt' => ['city' => 'novosibirsk'],
        'org' => implode(',', array_column(UG_CONTACTS_OFFICES, 'org')),
    ]));
    $breadcrumbs = get_breadcrumbs();

    ug_enqueue_style('assets/css/blocks/contacts_page.css');
    ug_enqueue_script('assets/js/blocks/contacts_page.js', [], ['in_footer' => true, 'strategy' => 'defer']);

    return <<<EOHTML
    <section class="contacts-page" itemscope itemtype="http://schema.org/Organization">
        <meta itemprop="name" content="Группа компаний «Umbrella Group»">
        <meta itemprop="telephone" content="$tel">
        <meta itemprop="email" content="$email">
        <div class="contacts-page__inner">
            <div class="breadcrumbs">$breadcrumbs</div>
            <h1 class="contacts-page__title">Контакты</h1>
            <p class="contacts-page__lead">Позвоните, напишите или&nbsp;выберите удобный офис в&nbsp;Новосибирске</p>

            <div class="contacts-strip">
                <div class="contacts-strip__col contacts-strip__col--phone contacts-text">
                    <span class="contacts-strip__label">Телефон</span>
                    <a class="contacts-strip__value contacts-strip__value--phone" href="tel:$tel">$phone</a>
                    <span class="contacts-strip__note">Пн–пт, 09:00–18:00 · Новосибирск</span>
                </div>
                <div class="contacts-strip__col contacts-strip__col--mail contacts-text">
                    $mail
                    <div>
                        <span class="contacts-strip__label">Электронная почта</span>
                        <a class="contacts-strip__value" href="mailto:$email">$email</a>
                    </div>
                </div>
                <div class="contacts-strip__col contacts-strip__col--cta">
                    <a href="#header-contact-form-lightbox" class="contacts-strip__btn">Обсудить задачу$arrow</a>
                </div>
            </div>

            <div class="contacts-offices">$offices</div>

            <div class="contacts-map" id="contacts-map">
                <iframe class="contacts-map__frame" src="$map_src" data-dg-options="$map_options" title="Офисы Umbrella Group на карте 2ГИС" loading="lazy"></iframe>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('contacts_page', 'ug_contacts_page_shortcode');
