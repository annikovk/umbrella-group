<?php
/**
 * Блоки страницы «Бизнес-старт за один час» — консультация юриста и бухгалтера
 * для начинающих предпринимателей
 * (/services/register-elimination/konsultaciya-dlya-nachinayushchih-predprinimatelej/):
 *
 * Первый экран — стандартный [su_welcome_screen button='consult'] +
 * [su_welcome_screen_conditions] в контенте, как у соседних страниц раздела.
 *
 *   [bs_for]     — «Эта консультация для вас, если вы…» (три коротких карточки);
 *   [bs_gains]   — что вы получаете (шесть пунктов) + пример из практики про РКН;
 *   [bs_docs]    — что остаётся у вас после консультации;
 *   [bs_steps]   — как проходит консультация (инфографика: четыре шага со стрелками);
 *   [bs_team]    — кто консультирует;
 *   [bs_compare] — пять визитов против одной консультации (таблица, на телефоне — карточки);
 *   [bs_price]   — цена и что в неё входит;
 *   [bs_form]    — финальный блок с формой заявки.
 *
 * FAQ — обычный [accordion] в контенте страницы (секция «Частые вопросы»):
 * из него inc/schema.php собирает JSON-LD FAQPage.
 *
 * Тексты — из упаковки услуги, согласованной с редактором (ноябрь 2026),
 * правятся здесь. Заявка — общие попапы: «Записаться» → #leave-request-contact-form-lightbox
 * (CF7 2886, стоит в «Footer Bottom» на каждой странице; заголовок попапа —
 * мета страницы umbrella_form_title), «Задать вопрос» → #ask-question-contact-form-lightbox
 * (CF7 9967, лайтбокс стоит в конце контента). В письме обеих форм есть адрес
 * страницы ([_url]) — менеджер видит, откуда заявка. Сами формы не правились.
 */

defined('ABSPATH') || exit;

const UG_BS_FORM = '#leave-request-contact-form-lightbox';
const UG_BS_FORM_ID = 2886;

/** Иконки — содержимое svg 24 × 24, обводка (как в tz_packages). */
const UG_BS_ICONS = [
    'check'  => '<path d="m5 12.5 4.5 4.5L19 7.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'users'  => '<circle cx="9" cy="8.5" r="3.25"/><path d="M3.5 19.25c.6-3.1 2.8-5 5.5-5s4.9 1.9 5.5 5" stroke-linecap="round"/><circle cx="16.5" cy="9" r="2.5"/><path d="M15.75 14.4c2.3.1 4.1 1.8 4.75 4.85" stroke-linecap="round"/>',
    'award'  => '<circle cx="12" cy="9.5" r="5.25"/><path d="m8.75 13.75-1.5 6.5L12 18l4.75 2.25-1.5-6.5" stroke-linejoin="round"/>',
    'globe'  => '<circle cx="12" cy="12" r="8.25"/><path d="M3.75 12h16.5M12 3.75c2.3 2.3 3.4 5.05 3.4 8.25s-1.1 5.95-3.4 8.25c-2.3-2.3-3.4-5.05-3.4-8.25S9.7 6.05 12 3.75Z" stroke-linejoin="round"/>',
    'doc'    => '<path d="M7 3.75h7l4 4v12.5H7z" stroke-linejoin="round"/><path d="M14 3.75v4h4M9.75 12h5.5M9.75 15.5h5.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'idea'   => '<path d="M9.25 17.25h5.5M10 20.25h4M12 3.75a5.75 5.75 0 0 0-3.4 10.4c.5.4.65.9.65 1.5v1.6h5.5v-1.6c0-.6.15-1.1.65-1.5A5.75 5.75 0 0 0 12 3.75Z" stroke-linecap="round" stroke-linejoin="round"/>',
    'start'  => '<path d="M5 20.25V4.5M5 5h11l-2.25 3.75L16 12.5H5" stroke-linecap="round" stroke-linejoin="round"/>',
    'search' => '<circle cx="10.5" cy="10.5" r="6"/><path d="m15 15 5 5" stroke-linecap="round"/>',
    'scale'  => '<path d="M12 4v16M7.5 20h9M5 7.5h14M5 7.5 2.75 13a2.25 2.25 0 0 0 4.5 0zM19 7.5 16.75 13a2.25 2.25 0 0 0 4.5 0z" stroke-linecap="round" stroke-linejoin="round"/>',
    'coin'   => '<circle cx="12" cy="12" r="8.25"/><path d="M9.5 9.25h3.75a1.9 1.9 0 0 1 0 3.75H9.5V8M9.5 13v3.5M8.25 15h4" stroke-linecap="round" stroke-linejoin="round"/>',
    'cal'    => '<rect x="4" y="5.5" width="16" height="14.75" rx="1.5"/><path d="M4 10h16M8.5 3.75v3.5M15.5 3.75v3.5" stroke-linecap="round"/>',
    'list'   => '<path d="M9.5 7h10M9.5 12h10M9.5 17h10" stroke-linecap="round"/><path d="m4 7 1.25 1.25L7.5 6M4 12l1.25 1.25L7.5 11M4 17l1.25 1.25L7.5 16" stroke-linecap="round" stroke-linejoin="round"/>',
    'phone'  => '<path d="M6.5 4h3l1.5 4-2 1.25a9 9 0 0 0 5.75 5.75L16 13l4 1.5v3a1.75 1.75 0 0 1-1.9 1.75A15.25 15.25 0 0 1 4.75 5.9 1.75 1.75 0 0 1 6.5 4Z" stroke-linejoin="round"/>',
    'chat'   => '<path d="M4.75 6.25c0-.83.67-1.5 1.5-1.5h11.5c.83 0 1.5.67 1.5 1.5v8.5c0 .83-.67 1.5-1.5 1.5H10l-4.25 3.5v-3.5h-.5c-.83 0-1.5-.67-1.5-1.5z" stroke-linejoin="round"/><path d="M8.5 9.5h7M8.5 12.5h4.5" stroke-linecap="round"/>',
    'clock'  => '<circle cx="12" cy="12" r="8.25"/><path d="M12 7.5V12l3 2" stroke-linecap="round" stroke-linejoin="round"/>',
    'shield' => '<path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke-linejoin="round"/><path d="m9 12 2.2 2.2L15.5 10" stroke-linecap="round" stroke-linejoin="round"/>',
];

function ug_bs_icon(string $name, int $size = 24): string
{
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' . UG_BS_ICONS[$name] . '</svg>';
}

/**
 * Неразрывные пробелы: после коротких слов и союзов, перед тире и «₽»,
 * внутри «5 000». Флаг /u обязателен — без него PCRE прода режет кириллицу.
 */
function ug_bs_t(string $s): string
{
    $s = preg_replace('/(\d) (\d{3})/u', '$1&nbsp;$2', $s);
    $s = preg_replace('/ (₽|—)/u', '&nbsp;$1', $s);
    $s = preg_replace('/(\d) (лет|час|дней)/u', '$1&nbsp;$2', $s);
    $s = preg_replace('/(?<![\p{L}\p{N}])([\p{L}]{1,2}|без|для|при|под|над|про|что|как|или|все|вы|мы|не|ни) /iu', '$1&nbsp;', $s);
    return (string) $s;
}

function ug_bs_assets(): void
{
    ug_enqueue_style('assets/css/blocks/biznes_start.css');
}

function ug_bs_btn(string $text, string $href = UG_BS_FORM, string $mod = ''): string
{
    return '<a href="' . esc_attr($href) . '" class="bs-btn' . ($mod !== '' ? ' bs-btn--' . $mod : '') . '">' . ug_bs_t($text) . '</a>';
}

function ug_bs_head(string $title, string $lead = '', string $mod = ''): string
{
    return '<div class="bs-head' . ($mod !== '' ? ' bs-head--' . $mod : '') . '"><h2 class="bs-title">' . ug_bs_t($title) . '</h2>'
        . ($lead !== '' ? '<p class="bs-lead">' . ug_bs_t($lead) . '</p>' : '') . '</div>';
}

/** Обёртка секции. */
function ug_bs_section(string $id, string $mod, string $inner): string
{
    return '<section class="bs bs--' . $mod . '" id="' . $id . '"><div class="bs__inner">' . $inner . '</div></section>';
}

// ── [bs_for] ────────────────────────────────────────────────────────────────

function ug_bs_for_shortcode(): string
{
    ug_bs_assets();
    $cards = [
        ['idea', 'Только планируете открыть ООО или ИП', 'Поможем выбрать ИП или ООО, коды ОКВЭД и налоговый режим — ещё до регистрации.'],
        ['start', 'Уже зарегистрировались, но ещё не начали работать', 'Документы на руках, а с чего начать — непонятно. Разберём договоры, сотрудников и первую отчётность.'],
        ['search', 'Уже работаете и хотите проверить, ничего ли не упустили', 'Бизнес идёт, но где-то остались пробелы. Обычно так и есть.'],
    ];
    $html = '';
    foreach ($cards as $i => [$icon, $title, $text]) {
        $html .= '<li class="bs-for__card"><div class="bs-for__top"><span class="bs-icon">' . ug_bs_icon($icon, 26) . '</span><span class="bs-for__num">' . sprintf('%02d', $i + 1) . '</span></div>'
            . '<h3 class="bs-for__title">' . ug_bs_t($title) . '</h3><p class="bs-for__text">' . ug_bs_t($text) . '</p></li>';
    }

    return ug_bs_section('bs-for', 'for', ug_bs_head('Эта консультация для вас, если вы…')
        . '<ol class="bs-for">' . $html . '</ol>'
        . '<div class="bs-center">' . ug_bs_btn('Проверить свой бизнес') . '</div>');
}

add_shortcode('bs_for', 'ug_bs_for_shortcode');

// ── [bs_gains] ──────────────────────────────────────────────────────────────

function ug_bs_gains_shortcode(): string
{
    ug_bs_assets();
    $items = [
        ['scale', 'ИП или ООО и коды ОКВЭД — если вы ещё не зарегистрировались.', 'Разберём, какая форма удобнее под ваши планы, партнёров и налоги, и подберём коды ОКВЭД, чтобы при регистрации сразу указать всё, чем вы будете заниматься.'],
        ['coin', 'Налоговый режим, который подходит вашей модели.', 'Не «обычно выбирают УСН», а обоснование: «вам выгоднее такой-то режим, потому что…» — с расчётом под вашу выручку и расходы.'],
        ['users', 'Ясность с сотрудниками.', 'Кого можно оформить по договору ГПХ, а с кем нужен трудовой договор, как правильно принять первого сотрудника и почему именно так в вашей ситуации.'],
        ['doc', 'Договоры, по которым можно работать с первого дня.', 'Шаблоны под вашу деятельность, проверенные юристами: поставка, услуги, аренда. Знаете, на что смотреть в договоре с арендодателем.'],
        ['cal', 'Календарь, в котором ничего не пропустишь.', 'Что, куда и когда сдавать и платить, какие сроки нельзя нарушать и что будет, если их пропустить.'],
        ['list', 'Полный список обязательных регистраций.', 'Уведомление в Роскомнадзор, ЕГАИС, лицензии, уведомление о начале деятельности — только то, что нужно именно вашему бизнесу.'],
    ];
    $html = '';
    foreach ($items as $i => [$icon, $title, $text]) {
        $html .= '<li class="bs-gain"><span class="bs-icon">' . ug_bs_icon($icon, 26) . '</span><div>'
            . '<h3 class="bs-gain__title">' . ug_bs_t($title) . '</h3><p class="bs-gain__text">' . ug_bs_t($text) . '</p></div></li>';
    }
    // пример из практики — сразу после пункта про обязательные регистрации
    $case = ug_bs_t('Компания работала третий месяц, когда пришла на консультацию. Выяснилось, что она не подала уведомление в Роскомнадзор об обработке персональных данных. Это обязанность почти каждого бизнеса, у которого есть сотрудники или клиенты-физлица. Без консультации владельцы так бы об этом и не узнали.');
    $shield = ug_bs_icon('shield', 22);

    return ug_bs_section('chto-vhodit', 'gains', ug_bs_head('После консультации вы точно знаете, как работать по правилам')
        . '<ol class="bs-gains">' . $html . '</ol>'
        . '<div class="bs-case"><div class="bs-case__body"><p class="bs-case__kicker">' . $shield . ' Пример из&nbsp;практики</p><p class="bs-case__text">' . $case . '</p></div></div>');
}

add_shortcode('bs_gains', 'ug_bs_gains_shortcode');

// ── [bs_docs] ───────────────────────────────────────────────────────────────

function ug_bs_docs_shortcode(): string
{
    ug_bs_assets();
    $rows = [
        ['Памятка по налоговому режиму и календарь отчётности', 'Видно, какой режим выбран, почему, и что сдавать в течение года'],
        ['Шаблон трудового договора', 'Чтобы оформить первого сотрудника правильно'],
        ['Шаблон договора ГПХ', 'Для исполнителей, которых можно не брать в штат'],
        ['Шаблоны договоров с контрагентами', 'Поставка, услуги, аренда — под вашу деятельность'],
        ['Календарь платежей и отчётности', 'Сроки, получатели, последствия пропуска — в одной памятке'],
        ['Список обязательных регистраций', 'Что пройти, куда подать и в какие сроки'],
    ];
    $html = '';
    foreach ($rows as [$what, $why]) {
        $html .= '<li class="bs-doc"><span class="bs-doc__icon">' . ug_bs_icon('doc', 22) . '</span>'
            . '<div><p class="bs-doc__what">' . ug_bs_t($what) . '</p><p class="bs-doc__why">' . ug_bs_t($why) . '</p></div></li>';
    }
    $note = ug_bs_t('Нужен договор под особую сделку? Подготовим его под ваш запрос, стоимость обсудим отдельно.');
    return ug_bs_section('dokumenty', 'docs', ug_bs_head('Вы уходите не только с ответами, но и с документами')
        . '<ul class="bs-docs">' . $html . '</ul>'
        . '<p class="bs-docs__note">' . ug_bs_icon('chat', 20) . '<span>' . $note . '</span></p>');
}

add_shortcode('bs_docs', 'ug_bs_docs_shortcode');

// ── [bs_steps] ──────────────────────────────────────────────────────────────

function ug_bs_steps_shortcode(): string
{
    ug_bs_assets();
    // инфографика: [иконка, подпись, короткая строка]
    $steps = [
        ['phone', 'Оставляете заявку', 'Перезваниваем и договариваемся о времени'],
        ['chat', 'Рассказываете о бизнесе', 'Готовить заранее ничего не нужно'],
        ['clock', 'Час с юристом и бухгалтером', 'В офисе или онлайн из любого города'],
        ['doc', 'Получаете документы', 'На встрече или на почту'],
    ];
    $html = '';
    $last = count($steps) - 1;
    foreach ($steps as $n => [$icon, $title, $text]) {
        $html .= '<li class="bs-flow__step"><div class="bs-flow__mark"><span class="bs-flow__icon">' . ug_bs_icon($icon, 30) . '</span><span class="bs-flow__num">' . ($n + 1) . '</span></div>'
            . '<div class="bs-flow__body"><h3 class="bs-flow__title">' . ug_bs_t($title) . '</h3><p class="bs-flow__text">' . ug_bs_t($text) . '</p></div></li>';
        if ($n < $last) {
            $html .= '<li class="bs-flow__arrow" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 12h15M13.5 6.5 19 12l-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/></svg></li>';
        }
    }
    return ug_bs_section('kak-prohodit', 'steps', ug_bs_head('Как проходит консультация')
        . '<ol class="bs-flow">' . $html . '</ol>'
        . '<div class="bs-center">' . ug_bs_btn('Записаться на консультацию') . '</div>');
}

add_shortcode('bs_steps', 'ug_bs_steps_shortcode');

// ── [bs_team] ───────────────────────────────────────────────────────────────

function ug_bs_team_shortcode(): string
{
    ug_bs_assets();
    $cols = [
        ['scale', 'Юристы отдела корпоративного права', ['Договоры', 'Сотрудники', 'Обязательные регистрации и лицензии']],
        ['coin', 'Бухгалтеры', ['Налоговый режим', 'Отчётность', 'Календарь платежей']],
    ];
    $html = '';
    foreach ($cols as [$icon, $title, $items]) {
        $li = '';
        foreach ($items as $item) {
            $li .= '<li>' . ug_bs_icon('check', 18) . '<span>' . ug_bs_t($item) . '</span></li>';
        }
        $html .= '<div class="bs-team__col"><div class="bs-team__top"><span class="bs-icon">' . ug_bs_icon($icon, 26) . '</span>'
            . '<h3 class="bs-team__title">' . ug_bs_t($title) . '</h3></div>'
            . '<p class="bs-team__label">На&nbsp;консультации берут на&nbsp;себя</p><ul class="bs-team__list">' . $li . '</ul></div>';
    }
    $text = ug_bs_t('Более 36 лет — с 1990 года — мы помогаем предпринимателям открывать и вести бизнес. На консультации с вами работают юристы отдела корпоративного права и бухгалтеры, у каждого опыт от 15 лет. Они видели сотни стартов и знают, где новички ошибаются чаще всего.');
    return ug_bs_section('bs-team', 'team', '<div class="bs-team">'
        . '<div class="bs-team__intro">' . ug_bs_head('Ваш бизнес разбирают эксперты с опытом от 15 лет')
        . '<p class="bs-team__year"><b>с&nbsp;1990&nbsp;года</b><span>помогаем предпринимателям открывать и&nbsp;вести бизнес</span></p>'
        . '<p class="bs-team__text">' . $text . '</p></div>'
        . '<div class="bs-team__cols">' . $html . '</div></div>');
}

add_shortcode('bs_team', 'ug_bs_team_shortcode');

// ── [bs_compare] ────────────────────────────────────────────────────────────

function ug_bs_compare_shortcode(): string
{
    ug_bs_assets();
    // [вопрос, к кому идут, по отдельности]
    $rows = [
        ['Какую систему налогообложения выбрать', 'налоговому консультанту', 'от 3 000 ₽'],
        ['Что и когда сдавать и платить', 'бухгалтеру', 'от 3 000 ₽'],
        ['Договоры с клиентами, поставщиками, арендодателем', 'юристу по договорам', 'от 6 000 ₽'],
        ['Какие регистрации и лицензии нужны', 'юристу по лицензированию', 'от 6 000 ₽'],
        ['Как оформлять сотрудников', 'кадровику', 'ещё один визит'],
    ];
    $check = ug_bs_icon('check', 20);
    $body = '';
    foreach ($rows as [$q, $who, $price]) {
        $body .= '<tr><th scope="row" class="bs-cmp__q">' . ug_bs_t($q) . '</th>'
            . '<td data-label="Обычно идут к"><span>' . ug_bs_t($who) . '</span></td>'
            . '<td data-label="По отдельности"><span>' . ug_bs_t($price) . '</span></td>'
            . '<td data-label="У нас" class="bs-cmp__our"><span class="bs-cmp__yes">' . $check . '<i>входит</i></span></td></tr>';
    }
    $total = '<tr class="bs-cmp__total"><th scope="row" class="bs-cmp__q">Итого</th>'
        . '<td data-label="Обычно идут к"><span><b>5&nbsp;визитов</b></span></td>'
        . '<td data-label="По отдельности"><span><b>от&nbsp;18&nbsp;000&nbsp;₽</b></span></td>'
        . '<td data-label="У нас" class="bs-cmp__our"><span><b>5&nbsp;000&nbsp;₽ и&nbsp;один час</b></span></td></tr>';
    $note = ug_bs_t('По отдельности те же вопросы — это пять специалистов, пять встреч и от 18 000 ₽ только за консультации, даже без кадровика. А потом ещё самому складывать их советы в одну картину. У нас юрист и бухгалтер смотрят на ваш бизнес одновременно — за один час и 5 000 ₽: налоговый режим согласован с договорами, а договоры — с тем, как вы оформляете людей. И шаблоны договоров вы получаете в комплекте, хотя отдельно составление договора стоит от 8 000 ₽.');

    return ug_bs_section('bs-compare', 'compare', ug_bs_head('Обычно на это уходит пять визитов. У нас — один час')
        . '<div class="bs-cmp-wrap"><table class="bs-cmp"><thead><tr><th scope="col">Вопрос</th><th scope="col">Обычно идут к</th><th scope="col">По отдельности</th><th scope="col" class="bs-cmp__our">У нас</th></tr></thead>'
        . '<tbody>' . $body . $total . '</tbody></table></div>'
        . '<p class="bs-cmp__note">' . $note . '</p>');
}

add_shortcode('bs_compare', 'ug_bs_compare_shortcode');

// ── [bs_price] ──────────────────────────────────────────────────────────────

function ug_bs_price_shortcode(): string
{
    ug_bs_assets();
    $items = [
        'Консультация юриста и бухгалтера',
        'Выбор ИП или ООО и подбор ОКВЭД — если ещё не зарегистрировались',
        'Шаблоны трудового договора и договора ГПХ',
        'Шаблоны договоров под вашу деятельность',
        'Календарь платежей и отчётности',
        'Список обязательных регистраций',
    ];
    $li = '';
    foreach ($items as $item) {
        $li .= '<li>' . ug_bs_icon('check', 18) . '<span>' . ug_bs_t($item) . '</span></li>';
    }
    $text = ug_bs_t('Вы платите за одну встречу, а ваш бизнес разбирают сразу два специалиста: налоговый режим согласован с договорами, а договоры — с тем, как вы оформляете людей. И уходите вы не с конспектом, а с готовыми документами: шаблонами договоров, календарём платежей и отчётности, списком обязательных регистраций.');

    return ug_bs_section('bs-price', 'price', '<div class="bs-price">'
        . '<div class="bs-price__text">' . ug_bs_head('5 000 ₽ за одну консультацию — а работают юрист и бухгалтер вместе')
        . '<p class="bs-price__desc">' . $text . '</p></div>'
        . '<div class="bs-price__card"><p class="bs-price__kicker">Бизнес-старт за&nbsp;один час</p><ul class="bs-price__list">' . $li . '</ul>'
        . '<div class="bs-price__foot"><p class="bs-price__sum"><b>5&nbsp;000&nbsp;₽</b><span>1&nbsp;час, в&nbsp;офисе или онлайн</span></p>'
        . ug_bs_btn('Записаться на консультацию') . '</div></div></div>');
}

add_shortcode('bs_price', 'ug_bs_price_shortcode');

// ── [bs_form] ───────────────────────────────────────────────────────────────

function ug_bs_form_shortcode(): string
{
    ug_bs_assets();
    $text = ug_bs_t('Оставьте телефон: перезвоним, уточним пару деталей о вашем бизнесе и подберём время консультации — в офисе или онлайн, из любого города России.');
    $form = do_shortcode('[contact-form-7 id="' . UG_BS_FORM_ID . '" html_class="bs-cf7"]');
    $title = ug_bs_t('Расскажите, чем планируете заниматься, — разберём остальное');

    return <<<EOHTML
    <section class="bs bs--form" id="forma">
        <div class="bs__inner bs-form">
            <div class="bs-form__text">
                <h2 class="bs-title">$title</h2>
                <p class="bs-lead">$text</p>
                <p class="bs-form__price"><b>5&nbsp;000&nbsp;₽</b> за&nbsp;консультацию · 1&nbsp;час · офис или онлайн</p>
            </div>
            <div class="bs-form__box">$form</div>
        </div>
    </section>
    EOHTML;
}

add_shortcode('bs_form', 'ug_bs_form_shortcode');
