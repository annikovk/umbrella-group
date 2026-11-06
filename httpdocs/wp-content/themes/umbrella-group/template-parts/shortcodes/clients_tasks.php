<?php
/**
 * Нижние блоки страницы «Клиенты» (/about/clients/, page 1000), под плитками
 * [ug_client_tiles] и перед «Нашими кейсами»:
 *  - [clients_tasks] — светлая плашка «Не просто работаем с компаниями —
 *    решаем конкретные задачи»: заголовок, тонкая черта, три направления
 *    со ссылками на разделы услуг;
 *  - [clients_cta] — тёмно-бордовый баннер «Задачи у бизнеса разные…»,
 *    кнопка открывает форму заявки из шапки (#header-contact-form-lightbox,
 *    как «Заказать звонок» и баннер под кейсами), картинка справа.
 * Заголовки — PT Serif, как у остальных блоков страницы ([clients_hero],
 * «Наши кейсы»); на макете — гротеск.
 */

defined('ABSPATH') || exit;

/**
 * Фон всего баннера — картинка заказчика 1916 × 821: тёмно-бордовый градиент,
 * левые ~45% пустые под текст, справа красный зонт и чёрная папка. Рядом лежат
 * <имя>.webp (полный размер) и <имя>-m.{webp,jpg} — правая часть кадра
 * 1100 × 821 с зонтом и папкой под телефон (≤849px), как у [clients_hero].
 * Пусто — заглушка.
 */
const UG_CLIENTS_CTA_IMAGE = '/wp-content/themes/umbrella-group/assets/img/clients-tasks-cta.jpg';

/** Иконки направлений — svg 24 × 24, обводка 1.6, как UG_ABOUT_ICONS. */
const UG_CLIENTS_TASK_ICONS = [
    // Портфель — тот же, что 'case' в иконках страницы кейсов
    'case' => '<rect x="3.75" y="7.25" width="16.5" height="12" rx="1.75"/><path d="M9 7.25V5.5c0-.7.55-1.25 1.25-1.25h3.5c.7 0 1.25.55 1.25 1.25v1.75M3.75 12.25h16.5" stroke-linejoin="round"/>',
    'coins' => '<ellipse cx="12" cy="5.75" rx="6.25" ry="2.25"/><path d="M5.75 5.75v3.5c0 1.25 2.8 2.25 6.25 2.25s6.25-1 6.25-2.25v-3.5M5.75 9.25v3.5c0 1.25 2.8 2.25 6.25 2.25s6.25-1 6.25-2.25v-3.5M5.75 12.75v3.5c0 1.25 2.8 2.25 6.25 2.25s6.25-1 6.25-2.25v-3.5"/>',
    // Щит — контур как 'shield' в UG_ABOUT_ICONS, внутри «i»
    'shield' => '<path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke-linejoin="round"/><path d="M12 11v4.5" stroke-linecap="round"/><circle cx="12" cy="8.4" r=".55" fill="currentColor"/>',
];

/** Направления: [иконка, заголовок, текст, раздел услуг]. */
const UG_CLIENTS_TASKS = [
    ['case', 'Корпоративные изменения', 'Регистрация, реорганизация, изменения в&nbsp;структуре бизнеса.', '/services/register-elimination/'],
    ['coins', 'Налоги и&nbsp;учет', 'Аудит, бухгалтерское сопровождение, оценка рисков.', '/services/bukhgalterskie-uslugi/'],
    ['shield', 'Лицензирование и&nbsp;право', 'Получение лицензий и&nbsp;юридическое сопровождение бизнеса.', '/services/licensing/'],
];

function ug_clients_tasks_shortcode(): string
{
    $items = '';
    foreach (UG_CLIENTS_TASKS as [$icon, $title, $text, $url]) {
        $items .= '<a class="clients-tasks__item" href="' . esc_url(home_url($url)) . '">'
            . '<svg class="clients-tasks__icon" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' . UG_CLIENTS_TASK_ICONS[$icon] . '</svg>'
            . '<span class="clients-tasks__name">' . $title . '</span>'
            . '<span class="clients-tasks__text">' . $text . '</span>'
            . '</a>';
    }

    ug_enqueue_style('assets/css/blocks/clients_tasks.css');

    return <<<EOHTML
    <section class="clients-bottom clients-bottom--tasks">
        <div class="clients-bottom__inner">
            <div class="clients-tasks">
                <h2 class="clients-tasks__title">Не просто работаем <br>с&nbsp;компаниями&nbsp;— решаем <br>конкретные задачи</h2>
                <div class="clients-tasks__items">$items</div>
            </div>
        </div>
    </section>
    EOHTML;
}

function ug_clients_cta_shortcode(): string
{
    $image = '<span class="clients-cta__stub">Картинка 1916&nbsp;×&nbsp;821</span>';
    if (UG_CLIENTS_CTA_IMAGE) {
        $base = esc_url(home_url(preg_replace('~\\.jpg$~', '', UG_CLIENTS_CTA_IMAGE)));
        // ux-skip-lazy: lazy-load Flatsome подменил бы src заглушкой, а <source> не тронул бы
        $image = '<picture>'
            . '<source type="image/webp" media="(max-width: 849px)" srcset="' . $base . '-m.webp">'
            . '<source media="(max-width: 849px)" srcset="' . $base . '-m.jpg">'
            . '<source type="image/webp" srcset="' . $base . '.webp">'
            . '<img src="' . $base . '.jpg" alt="Красный зонт и чёрная папка с логотипом Umbrella Group" width="1916" height="821" loading="lazy" decoding="async" class="ux-skip-lazy">'
            . '</picture>';
    }
    $arrow = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    ug_enqueue_style('assets/css/blocks/clients_tasks.css');

    return <<<EOHTML
    <section class="clients-bottom clients-bottom--cta">
        <div class="clients-bottom__inner">
            <div class="clients-cta">
                <div class="clients-cta__image">$image</div>
                <div class="clients-cta__text">
                    <h2 class="clients-cta__title">Задачи у&nbsp;бизнеса разные. <br>Подход к&nbsp;ним&nbsp;— всегда предметный.</h2>
                    <p class="clients-cta__desc">Расскажите, с&nbsp;какой задачей столкнулась ваша компания. <br>Подключим специалистов нужного направления <br>и&nbsp;предложим решение.</p>
                    <a href="#header-contact-form-lightbox" class="clients-cta__btn">Обсудить задачу$arrow</a>
                </div>
            </div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('clients_tasks', 'ug_clients_tasks_shortcode');
add_shortcode('clients_cta', 'ug_clients_cta_shortcode');
