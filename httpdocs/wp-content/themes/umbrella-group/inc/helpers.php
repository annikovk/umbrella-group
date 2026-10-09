<?php
/**
 * Общие хелперы темы (спека 1 §3.1). Создаёт и правит WS-4, остальные пакеты
 * только зовут. Подключается первым в inc/bootstrap.php.
 *
 *  - ug_ru_date()                  — date() с русскими месяцами в родительном падеже;
 *  - ug_current_service_section()  — раздел услуг текущей страницы по её предкам
 *                                    вместо проверок подстрок REQUEST_URI;
 *  - ug_service_category()         — тот же раздел как категория записей
 *                                    (case_branch, feedback_branch, …);
 *  - ug_request_path()             — путь текущего запроса без query string;
 *  - ug_render()                   — шаблон template-parts/blocks/<имя>.php в строку;
 *  - ug_remember()                 — кэш данных шорткода на запрос.
 */

declare(strict_types=1);

const UG_RU_MONTHS_GENITIVE = [
    'January' => 'января', 'February' => 'февраля', 'March' => 'марта', 'April' => 'апреля',
    'May' => 'мая', 'June' => 'июня', 'July' => 'июля', 'August' => 'августа',
    'September' => 'сентября', 'October' => 'октября', 'November' => 'ноября', 'December' => 'декабря',
];

/**
 * Дата с русским месяцем (`F` → «октября»).
 *
 * Строка (post_date и т.п.) читается как местное время сайта и в нём же
 * форматируется — тот же результат, что прежний date($f, strtotime($s)),
 * без сдвига часового пояса.
 */
function ug_ru_date(string $format, DateTimeInterface|int|string $date = 'now'): string
{
    $tz = wp_timezone();
    if (is_int($date)) {
        $date = (new DateTimeImmutable('@' . $date))->setTimezone($tz);
    } elseif (is_string($date)) {
        $date = new DateTimeImmutable($date, $tz);
    }
    return strtr($date->format($format), UG_RU_MONTHS_GENITIVE);
}

/** Путь текущего запроса без query string («/services/licensing/»). */
function ug_request_path(): string
{
    $path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return is_string($path) ? $path : '/';
}

/**
 * Раздел услуг текущей страницы: slug дочерней страницы /services/, под
 * которой лежит текущая (сама или через предков) — `licensing`,
 * `register-elimination`, `services-le`, `audit`, `bukhgalterskie-uslugi`, …
 * Вне /services/ (и не на странице) — null. $post_id — для сверки и wp-cli;
 * по умолчанию текущая страница запроса.
 */
function ug_current_service_section(?int $post_id = null): ?string
{
    static $current = false;
    if ($post_id === null) {
        if (!did_action('wp')) { // запрос ещё не разобран — не запоминаем
            return ug_current_service_section(get_queried_object_id());
        }
        if ($current === false) {
            $current = ug_current_service_section(get_queried_object_id());
        }
        return $current;
    }
    if ($post_id === 0 || get_post_type($post_id) !== 'page') {
        return null;
    }
    // [сама, родитель, …, корень] → [корень, …, сама]
    $chain = array_reverse(array_merge([$post_id], get_post_ancestors($post_id)));
    if (count($chain) >= 2 && get_post_field('post_name', $chain[0]) === 'services') {
        return (string) get_post_field('post_name', $chain[1]);
    }
    return null;
}

/**
 * Категория записей (case_branch, feedback_branch, specialist_branch, faq_branch)
 * для текущего раздела услуг; null — раздел не из таблицы.
 *
 * Таблица повторяет прежние проверки strpos(REQUEST_URI, "services/<раздел>")
 * 1:1 (сверено по всем 289 опубликованным записям): `licensing-maket1` —
 * макет страницы лицензирования (28975), подстрока "services/licensing"
 * находила и его.
 */
function ug_service_category(?int $post_id = null): ?string
{
    return [
        'licensing'               => 'Лицензирование',
        'licensing-maket1'        => 'Лицензирование',
        'register-elimination'    => 'Регистрация и ликвидация',
        'services-le'             => 'Юридические услуги',
        'audit'                   => 'Аудит',
        'bukhgalterskie-uslugi'   => 'Бухгалтерия',
        'sdelki-s-nedvizhimostyu' => 'Сделки с недвижимостью',
    ][ug_current_service_section($post_id) ?? ''] ?? null;
}

/**
 * Шаблон template-parts/blocks/<имя>.php в строку; $args доступны в шаблоне
 * как $args (как у get_template_part).
 */
function ug_render(string $template, array $args = []): string
{
    ob_start();
    get_template_part('template-parts/blocks/' . $template, null, $args);
    return (string) ob_get_clean();
}

/**
 * Данные шорткода, посчитанные один раз на запрос для одного набора аргументов:
 * один и тот же блок (отзывы, кейсы, FAQ) часто стоит на странице дважды —
 * в контенте и в UX-блоке.
 *
 * @template T
 * @param callable(): T $compute
 * @return T
 */
function ug_remember(string $group, array $key, callable $compute): mixed
{
    static $memo = [];
    $k = $group . ':' . md5(serialize($key));
    if (!array_key_exists($k, $memo)) {
        $memo[$k] = $compute();
    }
    return $memo[$k];
}

/**
 * <img> первого экрана мимо lazy-load Flatsome.
 *
 * Пока в theme_mods включён lazy_load_images, Flatsome на the_content:9999
 * переписывает каждый <img> контента на SVG-заглушку 100×100 + data-src —
 * в том числе LCP-баннер, который тогда грузится только после JS. Внутри
 * the_content картинка подменяется комментарием-меткой и возвращается на
 * место после всех фильтров (PHP_INT_MAX); вне the_content — как есть.
 */
function ug_eager_img(string $img): string
{
    static $hooked = false;
    if (!doing_filter('the_content')) {
        return $img;
    }
    $key = '<!--ug-eager-' . md5($img) . '-->';
    $GLOBALS['ug_eager_imgs'][$key] = $img;
    if (!$hooked) {
        $hooked = true;
        add_filter('the_content', static function (string $content): string {
            return strtr($content, $GLOBALS['ug_eager_imgs'] ?? []);
        }, PHP_INT_MAX);
    }
    return $key;
}
