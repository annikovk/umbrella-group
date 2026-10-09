# Спека 1: модернизация темы umbrella-group (taxlab.ru)

Дата: 2026-10-09. Основание: девять аудитов от 09.10.2026 (perf, shots, assets,
deadcode, php, frontend, platform, research) и их артефакты в `/tmp/taxlab-audit/`.
Спорные места перепроверены по коду и БД стенда; где аудиты расходятся, в тексте
стоит результат проверки.

Тема: `httpdocs/wp-content/themes/umbrella-group/` (child Flatsome 3.13.4),
19 256 строк PHP в 350 отслеживаемых файлах. WordPress 6.5.10, PHP 8.3.
Стенд: `http://localhost:8088` (Docker, база прода, WP Rocket / webp-express /
Яндекс.Метрика выключены mu-плагином `docker/overlay/mu-plugins/zz-taxlab-local.php`).

---

## 1. Цель и метрики успеха

Цель: та же вёрстка попиксельно, меньше кода и запросов, современный конвейер
ассетов, безопасный PHP. Всё меряется на стенде против baseline «до».

### 1.1 Визуальная неизменность (обязательно)

`tools/shots-set.sh diff /tmp/taxlab-audit/shots-before <after> <out>` — **0.0000 %**
отличий на всех 28 парах (14 страниц × 1440 и 390m). Шум эталона проверен: два
прогона «до» дают 0.0000 % на всех 28 парах (`/tmp/taxlab-audit/diff-noise.txt`).

Разрешённые отличия — только из списка §7.3, каждое отдельным коммитом после
одобрения владельца, и не смешиваются с рефакторингом.

### 1.2 Lighthouse (медианы, `tools/perf.mjs --runs 5`)

Baseline: `/tmp/taxlab-audit/perf-before/summary.md` (Lighthouse 12.8.2, медиана из 3).

| Страница | Perf mob → цель | Perf desk → цель | CLS mob/desk → цель | LCP mob → цель |
|---|---|---|---|---|
| `/` | 58 → ≥ 75 | 64 → ≥ 90 | 0.215 / 0.836 → ≤ 0.1 | 5.5 с → ≤ 3.0 с |
| `/services/` | 53 → ≥ 75 | 69 → ≥ 90 | 0.531 / 0.588 → ≤ 0.1 | 4.7 → ≤ 3.0 |
| `/services/bukhgalterskie-uslugi/` | 69 → ≥ 80 | 93 → ≥ 95 | 0.051 / 0.082 → ≤ 0.1 | 5.2 → ≤ 3.0 |
| `/services/licensing/roznichnaya-torgovlya-alkogolem/` | 38 → ≥ 70 | 60 → ≥ 90 | 0.932 / 0.910 → ≤ 0.1 | 12.8 → ≤ 3.5 |
| `/blog/` | 43 → ≥ 70 | 68 → ≥ 90 | 0.641 / 0.307 → ≤ 0.1 | 13.3 → ≤ 3.5 |
| `/blog/smena-direktora-v-ooo/` | 58 → ≥ 75 | 83 → ≥ 95 | 0.435 / 0.277 → ≤ 0.1 | 4.5 → ≤ 3.0 |
| `/price/ceny-na-buhgalterskie-uslugi/` | 47 → ≥ 75 | 73 → ≥ 90 | 0.927 / 0.679 → ≤ 0.1 | 4.9 → ≤ 3.0 |
| `/about/contacts/` | 63 → ≥ 75 | 91 → ≥ 95 | 0.073 / 0.088 → ≤ 0.1 | 14.8 → ≤ 3.5 |

Остальные числовые цели (стенд, главная, desktop, если не сказано иначе):

| Метрика | Было | Цель | Откуда цифра |
|---|---|---|---|
| TBT mobile | 78–181 мс | ≤ 150 мс везде | perf: TBT и так низкий, не ухудшить |
| Вес страницы `/` | 10 987 КБ (mob 7 841) | ≤ 5 000 КБ (mob ≤ 3 500) | rutube+карта −3,4 МБ, картинки −1,6 МБ, шрифты −0,7 МБ |
| Запросов `/` | 284 (mob 227) | ≤ 150 | убраны дубли swiper/FA/marquiz, фасады iframe |
| Шрифты | 816–954 КБ | ≤ 250 КБ | один variable Inter с сабсетом + PT Serif 700 локально |
| HTML `/` | 440 КБ | ≤ 280 КБ | минус 113 КБ инлайн-CSS + 23 КБ инлайн-JS + 7 копий marquiz |
| Инлайн `<style>` темы в HTML | 57 блоков, 136 КБ | 0 (кроме UX Builder Flatsome) | assets-аудит |
| PHP warnings на обход 149 URL | 208 | 0 | `/tmp/taxlab-audit/php-audit/php-errors-grouped.txt` |
| SQL-запросов `/` и `/…/otkrytie-biznesa-pod-klyuch/` | 281 / 338 | ≤ 150 / ≤ 150 | `/tmp/taxlab-audit/php-audit/queries.txt` |
| `Set-Cookie` на анонимный GET | 3 (PHPSESSID, source_cookie, exp_mXyZ…) | 0 | php-аудит |
| `Cache-Control: no-store` на страницах | есть (session_start) | нет | research/platform |
| Accessibility | 65–74 | ≥ 85 | viewport, alt, aria-label, title iframe; контраст — отдельно с владельцем |
| Строк PHP в теме | 19 256 | ≤ 12 000 | −5,5 тыс. мёртвых, −2 тыс. дублей |
| Файлов темы в git | 350 | ≤ 260 | §6 |
| `fonts/` | 18 МБ (88 файлов) | ≤ 1 МБ | §6.3 |

Stretch (зависит от решений владельца вне скоупа: один GTM, антибот, nginx expires):
Perf mobile ≥ 85 на главной, LCP mobile ≤ 2.5 с.

Прод: до выкатки владелец снимает PageSpeed Insights (web-UI, mobile+desktop) для
8 URL из таблицы, после выкатки — те же 8. Сравнивать с продом напрямую стендовые
цифры нельзя: на проде WP Rocket, brotli и антибот.

---

## 2. Границы

### 2.1 В скоупе

- Весь код темы `umbrella-group` (PHP, CSS, JS, шрифты, картинки темы).
- Два правленых шорткода Flatsome, которые уже в git:
  `flatsome/inc/shortcodes/{accordion,team_members}.php` — не трогаем, но проверяем, что работают с новым конвейером.
- Новый `umbrella-group/footer.php` — override вендорского `flatsome/footer.php`,
  который правлен руками (кнопки Telegram/Max + 4,2 КБ `<style>`, строки 17–80) и
  **не в git**: слетит при обновлении Flatsome.
- `tools/` (shot.mjs, shots-set.sh, perf.mjs, новый html-diff).
- Настройки, принадлежащие теме, меняемые через wp-cli при деплое: `theme_mods_umbrella-group`
  (lazy_load_images, lazy_load_backgrounds, disable_blockcss, disable_emoji, шрифты),
  snapshot `wp_rocket_settings` для одного флага (combine CSS) — с одобрения владельца.
- Две миграции контента **только с одобрения владельца**: разворачивание A/B-тегов
  (WS-7) и перенос PHP-сниппетов insert-php в тему (WS-8).

### 2.2 Вне скоупа (вынесено в §9 для владельца)

Обновление ядра/Flatsome/плагинов, nginx/Plesk, protect918, Cloudflare, Redis,
удаление/замена плагинов, состав счётчиков (какие GTM/gtag/Метрика живые), контент
страниц (кроме §2.1), чистка БД (ревизии, таблицы).

### 2.3 Заморозка и порядок старта

В дереве лежит чужая незакоммиченная работа: `includes/upsell.php`, `assets/{css,js}/blocks/upsell.*`,
`assets/js/blocks/main-first-screen-slider.js`, правки `new-main-first-screen.php`,
`new_main_first_screen.css`, `su_welcome_screen_conditions.{php,css}`, одна строка в
`functions.php:242`, `tools/shot.mjs` (флаг `--eval`, которым пользуется shots-set.sh),
а также новые `tools/perf.mjs`, `tools/shots-set.sh`. Эталон снят с этой работой.

**Шаг 0 (до любого пакета):** автор коммитит эту работу, ставится тег
`before-modernize`. Пока тега нет, перечисленные файлы никто не трогает.

### 2.4 Контракты, которые нельзя ломать

- **Имена и атрибуты всех 64 шорткодов** — они в контенте 301 опубликованной записи
  (`/tmp/taxlab-audit/php-audit/shortcode-usage.txt`).
- **Функции, которые зовут PHP-сниппеты insert-php из БД** (grep по `db_published.tsv`,
  post_type `wbcr-snippets`): `umbrella_post_icon`, `umbrella_draw_tiles`,
  `umbrella_get_ratings`, `umbrella_form_title`, `umbrella_feedback_scan`,
  `umbrella_draw_header_with_breadcrumbs`, `umbrella_draw_filter_tabs`,
  `umbrella_client_personal_industry`; файлы `includes/pages/feedback-page.php`
  (сниппет 1870), `includes/blog-side-panel.php` (сниппет 1997). Сигнатуры и пути
  сохраняются до WS-8.
- **CSS-классы**, на которые ссылаются инлайн-стили контента (29 page + 7 blocks, 13 КБ)
  и триггеры GTM (`/tmp/taxlab-audit/GTM-TRFKKFL.js`, `GTM-WW2PFC.js`) — класс удаляется
  только при нуле вхождений в коде темы, БД, theme_mods и обоих контейнерах.
- **Текст писем CF7**: строка «Источник - …, Канал - …» должна остаться.

---

## 3. Целевая архитектура

### 3.1 Структура PHP

```
functions.php                 ≤ 30 строк: константы, require inc/bootstrap.php
inc/bootstrap.php             явный список require inc/*.php (без glob — порядок важен)
inc/setup.php                 supports, mime webp, redirect_canonical, ACF options, отключение
                              block-library / global-styles / emoji / IE-фолбэков Flatsome
inc/assets.php                реестр и API подключения ассетов (§3.2)
inc/attribution.php           серверная часть UTM-атрибуции: только фильтр wpcf7_form_elements
                              ([omatraffic]/[omacid] → hidden input) и sanitize posted data
inc/cf7.php                   recaptcha threshold, обёртка response output, стаб-шорткоды вебинара
inc/cookie-notice.php         разметка плашки (всегда в HTML, скрыта CSS, показывает JS)
inc/helpers.php               ug_ru_date() вместо 4 копий массива месяцев (functions.php:269,750,897,908),
                              ug_current_service_section() вместо 62 проверок REQUEST_URI,
                              ug_render(string $template, array $args): string
inc/post-types.php            один реестр CPT (7 типов) + ug_register_meta_box(schema) + save_post_{type}
                              с nonce/cap/sanitize по образцу client.php:266-300
inc/tiles.php                 umbrella_draw_tiles() и спутники (контракт §2.4), CSS вынесен в файл
inc/shortcodes.php            require шорткодов (пока они не переехали в классы — список как сейчас)
inc/legacy-stubs.php          webinar_registration_form / webinar_participants_table → ''
template-parts/blocks/*.php   чистые шаблоны: $args, esc_html/esc_attr/esc_url/wp_kses_post
template-parts/shortcodes/*.php  тонкие обёртки: parse atts → данные (с кэшем) → ug_render()
template-parts/analytics.php  единственное место для GTM/gtag/Метрика/roistat/marquiz/yadro
footer.php                    child-override Flatsome footer.php с кнопками Telegram/Max через хук
```

Правила для нового/переписанного кода: `declare(strict_types=1)` в inc/*, типы у
параметров и возвратов, экранирование на выходе, никаких `?>` в чисто-PHP файлах
(сейчас functions.php:1096-1098 даёт «headers already sent» в wp-cli), `===`,
`wp_date()` вместо `date()`, `get_theme_file_uri()` вместо 192 захардкоженных
`/wp-content/themes/umbrella-group/…`.

### 3.2 Конвейер ассетов

Сейчас: `css/css_collector.php` склеивает main.css + styles-intelsib.css +
tablets-and-down.css (37 426 Б) и CSS блоков через `file_get_contents` в один
`<style>` на `wp_footer:100` — после всех футерных скриптов, на 67–88 % длины HTML
(functions.php:251-257). Без него первый экран разваливается
(`/tmp/taxlab-audit/assets-pipeline/nocoll-1440.png`). Отсюда CLS 0.5–0.93 на desktop;
перенос этого блока в `<head>` в эксперименте дал CLS 0.027–0.044 (frontend-аудит).
Список файлов копится в `wp_cache` без привязки к запросу — при включении Redis
drop-in CSS одной страницы поедет на другие.

Целевое:

```php
// inc/assets.php
ug_asset_ver(string $rel): string            // (string) filemtime(get_theme_file_path($rel))
ug_style_handle(string $rel): string         // 'ug-' . sanitize_title(путь без расширения)
ug_enqueue_style(string $rel, array $deps = ['ug-main']): void
ug_enqueue_script(string $rel, array $deps = [], array $args = ['in_footer' => true]): void
```

- Хэндл выводится из пути (`assets/css/blocks/cases.css` → `ug-blocks-cases`), регистрация
  ленивая при первом вызове, версия — filemtime. Реестр руками — только для библиотек:
  `ug-swiper` (локальный `js/swiper-bundle.min.js` 11.2.5 + локальный CSS вместо трёх
  CDN-ссылок без версии), `ug-fancybox` (локальные `js/fancybox.umd.js` + `css/fancybox.css` v4),
  `ug-main`.
- **`ug-main`** = main.css + styles-intelsib.css + tablets-and-down.css + css/form-custom.css
  + fonts/fonts-configuration.css + `ul.tabs` из header.php:20-50 + стили кнопок
  Telegram/Max из flatsome/footer.php + 151 Б из плагина wp-add-custom-css
  (`wpacc_settings.main_custom_style`) + CSS блоков, которые есть на ≥ 90 % страниц
  по `trace/file_usage.json` (block-laurels.css 960 Б — 562/602 рендеров; CSS подвала).
  Один файл `assets/css/main.css`, печатается в `<head>` **после** Flatsome `custom-css`
  (тот стоит на `wp_head:100`, function-custom-css.php:666): `add_action('wp_head', fn() =>
  wp_print_styles(['ug-main']), 101)`. Так сохраняется нынешний порядок каскада — 32 пары
  селектор+свойство пересекаются с custom-css, и сейчас побеждает тема.
- **CSS блоков**: шорткод в render вызывает `ug_enqueue_style(...)`; WP печатает их в
  `print_late_styles` (`wp_footer:20`). Относительно head-стилей и body-инлайна порядок
  тот же, что у коллектора. Для первого экрана — предскан на хуке `wp`:
  `has_shortcode($post->post_content, …)` для `new_main_first_screen`, `su_welcome_screen`,
  `su_welcome_screen_conditions`, `business_first_screen`, `service_tiles` → enqueue до
  `wp_head`, чтобы CSS первого экрана лёг в head. Шорткоды из UX-блоков (`why_choose_us`,
  `umbrella_footer`, `service_tiles` живут в типе `blocks`, а не в контенте страницы) —
  рендерятся до `wp_footer:20`, поэтому попадают в late styles сами.
- **Переходный шим** (чтобы пакеты не пересекались по файлам): `umbrella_add_custom_css_files()`
  и `umbrella_add_custom_js_files()` переопределяются как обёртки над `ug_enqueue_*`, с
  пометкой `@deprecated`. 41 файл с 84 вызовами продолжает работать без правок; каждый
  пакет, переписывая свой шорткод, заменяет вызов на прямой `ug_enqueue_*`. Шим удаляется
  последним коммитом WS-2 после `grep -r umbrella_add_custom_ → 0`.
- **JS**: `in_footer`, зависимости `jquery` / `ug-swiper` / `ug-fancybox` явно. `strategy => 'defer'`
  включается **по хэндлу** и только когда у блока не осталось инлайн-`<script>` в heredoc,
  зависящих от него (на главной 22 инлайн-скрипта, 35 КБ). Три глобальных `var swiper = new Swiper`
  (on_call.js:107, open_bussnise.js:19, result_clinet_business.js:1) оборачиваются в IIFE.
- **Атрибут `custom_css` шорткода `service_tiles`** (service-tiles.php:123-124 →
  file_get_contents произвольного пути, в том числе `../../wp-config.php`) заменяется на
  whitelist из одного значения, которое есть в БД: `/assets/css/blocks/services-tiles-new.css`
  (страницы 13514, 13516).
- **Flatsome lazy-load выключается** (`lazy_load_images=0`, `lazy_load_backgrounds=0`): он
  подставляет SVG-заглушку 100×100 без размеров, в том числе на LCP-баннер, и даёт основной
  CLS. Взамен — нативный `loading="lazy"` ядра с width/height. Первый слайд первого экрана
  и баннер `su_welcome_screen`: `loading="eager" fetchpriority="high"`; остальные слайды —
  `fetchpriority="low"`. Все `<img>` в шаблонах темы получают width/height
  (`wp_get_attachment_image` там, где есть attachment).
- **Шрифты**: один `Inter.var.woff2` (уже лежит, 299 КБ) с сабсетом latin+cyrillic
  (pyftsubset, ожидаемо 100–150 КБ) вместо 7 статических по ~100 КБ; `font-display: swap`;
  preload одного файла. PT Serif 700 (заголовки, theme_mod `type_headings`) — локально woff2
  с сабсетом; Google Fonts у Flatsome выключить (сейчас URL ещё и запрашивает системные
  стеки из `type_texts`/`type_nav` как семейства). Font Awesome 4.7 с cdnjs (header.php:18,
  31 КБ CSS + 78 КБ шрифт на каждой странице) — заменить на `assets/css/icons.css` с инлайн
  SVG под теми же классами `fa fa-…`: нужны `fa-telegram` (кнопка в футере) и 5 иконок в
  контенте страниц 1228 и 28490 (единственные вхождения `fa fa-` в БД).
- **WP Rocket на проде**: выключить «Combine CSS» (`minify_concatenate_css=0`) — склейка
  поднимает все `<link>` выше `custom-css` и перевернёт каскад; на проде HTTP/2 + brotli,
  склейка не нужна (в 3.15+ опция удалена). Минификацию оставить. Если владелец против —
  добавить `ug-main` в `exclude_css`.

### 3.3 Компоненты-шорткоды

Шаблон переписывания одного шорткода (все 45 файлов `template-parts/shortcodes/`
устроены одинаково: класс с `generate_shortcode()` → heredoc → `umbrella_add_custom_*`):

1. Данные: запросы в метод `data()`; `WP_Query`/`get_posts` с `posts_per_page=-1` внутри
   циклов (calculate_business.php:11-44, 81-115, 144, 306; cases.php:237-291;
   feedback.php:178-235) → один запрос + группировка в PHP; результат в transient с
   ключом `wp_cache_get_last_changed('posts')`.
2. Выбор категории по URL (62 проверки `REQUEST_URI` в su_welcome_screen, specialists,
   komanda_specialistov, faq, cases, feedback, umbrella_feedback) → `ug_current_service_section()`
   (по предкам страницы `get_post_ancestors`, таблица «slug раздела → категория»); маппинг
   из существующих условий переносится 1:1 и сверяется таблицей «URL → категория» до/после
   по всем страницам `services/*`.
3. Разметка → `template-parts/blocks/<name>.php` с `$args`, экранирование на выходе.
   Внимание: внутри heredoc есть шорткоды Flatsome (`[section]`, `[row]`, `[col]`) — пробелы
   и порядок атрибутов влияют на `do_shortcode`; сверка нормализованным HTML-диффом
   обязательна (§4.3).
4. Ассеты: `ug_enqueue_style/script` в render; инлайн `<style>`/`<script>` из heredoc —
   в файлы блока.
5. Статичные шорткоды без логики (since_90, stock, more_question_bus, ignor_static,
   static_startup, help_start_business, trouble_2022, free_consultation …) — просто шаблон
   + однострочная регистрация в inc/shortcodes.php.

### 3.4 Конфигурация платформы, которую меняет тема

Через wp-cli (стенд — `docker exec taxlab-local-web-1 wp …` или SQL; прод — команда из §8):
`theme_mods_umbrella-group`: `lazy_load_images=0`, `lazy_load_backgrounds=0`,
`disable_blockcss=1`, `disable_emoji=1`; шрифты Flatsome — локальный PT Serif, Google Fonts
выключены. Это конфигурация темы, а не плагинов, поэтому в скоупе; выполняется в деплое.

---

## 4. Методика проверки (общая для всех пакетов)

### 4.1 Снимки

```
tools/shots-set.sh /tmp/taxlab-audit/shots-after            # ~220 с, 28 PNG
tools/shots-set.sh diff /tmp/taxlab-audit/shots-before /tmp/taxlab-audit/shots-after /tmp/taxlab-audit/diff-after
```

До WS-T (исправление shot.mjs) — с `SHOT=/tmp/taxlab-audit/shot-pipe.mjs`: встроенный
WebSocket Node рвёт соединение на ответе CDP > 4 МиБ, и `--full` на 5 из 14 страниц висит
до watchdog (shots-аудит). Дополнительные страницы пакета — `PAGES_ONLY="/url/" tools/shots-set.sh <dir>`.

**Расширенный эталон** снимается на шаге 0 в `/tmp/taxlab-audit/shots-before-extra/`
(URL проверить по БД): `/services/register-elimination/otkrytie-biznesa-pod-klyuch/`
(единственная страница шаблона page-bussnise, 14148), обе акции-лендинги (30768, 30770),
`/about/clients/`, `/feedback/`, `/services/licensing/litsenziya-mchs/` (иконки FA),
один пост формата aside, `/?s=налог`, 404, страница кейса, `/akcii/<любая>`.

Смена месяца меняет текст «До 01.11.2026» в блоках акций (akcii.php:47) — если сравнение
«после» идёт в другом месяце, эталон переснимается с тега: `git checkout before-modernize --
httpdocs/wp-content/themes/umbrella-group && tools/shots-set.sh …` (Docker монтирует рабочее
дерево), потом вернуть ветку. Также нельзя между «до» и «после» делать `local-site.sh fresh`
или `sync-from-server.sh` (появится laurel-5-400x400.png, которого нет в зеркале, на 12 из
14 страниц).

Маскировка в shots-set.sh прячет iframe и внешние виджеты — регрессии внутри rutube/карты/
marquiz проверяются глазами на стенде.

### 4.2 Lighthouse

`node tools/perf.mjs --out /tmp/taxlab-audit/perf-after --runs 5` (≈ 26 мин). Сравнивать
медианы `summary.md` с `perf-before`. LCP mobile шумный (5,5 / 5,3 / 28,7 с на главной) —
только медиана из 5.

### 4.3 Нормализованный HTML-дифф

Снимок «до» (шаг 0) и «после»: для каждого URL из `/tmp/taxlab-audit/urls.txt` (302 URL)
два `curl` — desktop UA и iPhone UA — в `/tmp/taxlab-audit/html-before/<slug>.<ua>.html`.
Нормализация перед diff: убрать `?ver=…`, nonce (`_wpnonce`, `data-wpcf7-…`), `meta name="ip"`,
значения cookie, комментарий кэша, схлопнуть пробелы между тегами. Инструмент —
`tools/html-diff.sh` (WS-T); до него — одноразовый цикл curl на шаге 0. Пакеты, которые
меняют разметку намеренно (hidden input атрибуции, плашка cookie), перечисляют ожидаемые
отличия в описании коммита.

### 4.4 Остальное

- PHP warnings: `docker logs taxlab-local-web-1 2>&1 | grep -c "umbrella-group"` во время
  обхода `urls.txt` — должно быть 0. На стенде временно включить `E_DEPRECATED`.
- Заголовки: `curl -sD - -o /dev/null http://localhost:8088/` — нет `Set-Cookie`, нет `no-store`.
- SQL: дельта `Questions` MariaDB на один запрос (`/tmp/taxlab-audit/q.sh`).
- Консоль браузера без новых ошибок на `/`, 14148, `/price/`, `/faq/` (CORS venyoo/leadia —
  известные, не наши).
- Формы: отправить CF7 на стенде (почта заблокирована mu-плагином; письмо перехватить фильтром
  `pre_wp_mail` в mu-плагине стенда и сравнить тело с текущим).

---

## 5. План работ: рабочие пакеты

Пакеты не пересекаются по файлам. Где пересечение неизбежно, указана последовательность.
Каждый пакет — своя ветка от `master` после шага 0, мелкие коммиты, перед merge — §4.1
(0.0000 %), §4.3, PHP warnings = 0.

```
Шаг 0 ──► WS-0 (скелет, последовательно, 1 день)
             ├──► WS-1 безопасность        ─┐
             ├──► WS-2 ассеты/шрифты       ─┤
             ├──► WS-3 шапка/подвал/аналитика (после WS-2: иконки, fancybox-хэндл)
             ├──► WS-4 шорткоды услуг      ─┤ параллельно
             ├──► WS-5 шорткоды главной/14148/акций (6 файлов — после WS-2)
             ├──► WS-6 шаблоны записей, CPT, tiles ─┘
             ├──► WS-7 A/B flatten (одобрение владельца; после WS-2)
             └──► WS-8 сниппеты → тема (одобрение; после WS-6)
WS-T инструменты — независимо, желательно первым.
```

### Шаг 0 — подготовка (владелец/автор текущей работы)

1. Закоммитить незакоммиченную работу (§2.3), тег `before-modernize`.
2. Снять расширенный эталон снимков (§4.1) и HTML-снимок «до» (§4.3).
3. Владелец снимает PSI «до» для 8 URL на проде (§1.2).
4. Владелец отвечает на вопросы §10 (не блокирует WS-0…WS-6).

### WS-T — инструменты

Файлы: `tools/shot.mjs`, `tools/shots-set.sh`, `tools/html-diff.sh` (новый), `tools/perf.mjs` (не трогать).

Шаги:
1. `shot.mjs` → `--remote-debugging-pipe` (fd 3/4, JSON через `\0`), готовый патч
   `launch()/connect()` ≈ 45 строк в `/tmp/taxlab-audit/shot-pipe.mjs`; при закрытии
   канала отклонять все ожидающие промисы. Сохранить флаг `--eval`.
2. В `shots-set.sh` убрать оговорку про `SHOT`.
3. `tools/html-diff.sh snapshot <dir>` и `tools/html-diff.sh diff <A> <B>` по §4.3.
4. Поправить комментарий в shot.mjs:16-19 про «бесконечный таймер qoopler» — timers.mjs
   показал, что у qoopler таймеров нет; висел WebSocket (frontend/shots-аудиты).

Приёмка: `node tools/shot.mjs --full --url /services/licensing/ 1440` укладывается в ~10 с;
`tools/shots-set.sh /tmp/x && tools/shots-set.sh diff /tmp/taxlab-audit/shots-before /tmp/x /tmp/d`
→ 0.0000 % на 28 парах без `SHOT=`.

### WS-0 — скелет functions.php → inc/ (последовательно, первым)

Файлы: `functions.php`, `inc/**` (новые), `functions_1.php`, `functions_2.php`, `functions_3_1.php` (удалить),
`css/css_collector.php` (только перенос require — содержимое не менять).

Шаги (перенос **дословно**, без изменения поведения):
1. `functions.php:18-122` → `inc/attribution.php` (UTM-блок исполняется в глобальной области
   при загрузке functions.php; include из functions.php на верхнем уровне сохраняет область
   видимости `$html` для `$GLOBALS['html']`). `:6-15, :138-142, :1057-1064` → `inc/cf7.php`.
   `:126-137, :1099-1110` → `inc/cookie-notice.php`. `:144-146, :155-161, :260-264, :1068-1092`
   → `inc/setup.php`. `:165-188` → `inc/assets.php` (пока как есть). `:190-196` → `inc/post-types.php`.
   `:197-242` → `inc/shortcodes.php`. `:244-250` → `inc/ab-tests.php`. `:251-257` → `inc/assets.php`.
   `:266-1055` (umbrella_draw_tiles и спутники, umbrella_date_of_post удалить — 0 вызовов) → `inc/tiles.php`.
   `:149-151` (webinar) → `inc/legacy-webinar.php` (WS-1 удалит).
2. `functions.php` ≤ 30 строк; убрать `?>` на :1096-1098.
3. Удалить `functions_1.php`, `functions_2.php`, `functions_3_1.php` (WP грузит только functions.php;
   0 require; mtime май–сентябрь 2024).

Приёмка: §4.3 — HTML 302 URL идентичен; §4.1 — 0.0000 %; `wp eval '1;'` в контейнере без
«headers already sent»; warnings не прибавилось.

### WS-1 — безопасность и серверное состояние

Файлы: `inc/attribution.php`, `inc/cf7.php`, `inc/cookie-notice.php`, `inc/legacy-webinar.php`
(удалить), `inc/legacy-stubs.php` (новый), `js/script.js`, `assets/js/attribution.js` (новый),
`includes/webinar/**` (удалить), `mail.php` (удалить), `assets/css/cookie-notice.css` (новый) —
или правило в ug-main через WS-2 (согласовать одну строку).

Шаги:
1. **Атрибуция в JS.** Удалить серверный блок (unserialize cookie — PHP object injection,
   functions.php:81; XSS через `utm_campaign=<img onerror…>` подтверждён: 6 вхождений payload
   в HTML `/main/`, живёт в cookie 30 дней; кэш WP Rocket раздаёт всем атрибуцию того, кто
   прогрел страницу — 92 кэш-файла прода). `assets/js/attribution.js` (defer): та же логика
   источника (utm_* / yclid / gclid / yabs / referrer → organic|referral|direct), JSON-cookie
   `ug_src` на 30 дней, заполнение `input[name=omatraffic]` и `[name=omacid]` (из `_gaexp`)
   во всех формах при `DOMContentLoaded` и при инициализации формы CF7 (событие формы, имя
   уточнить по версии CF7 5.9.3 на стенде). PHP: `wpcf7_form_elements` заменяет
   только `[omatraffic]`/`[omacid]` на `<input type="hidden" name="…">` (регулярка, без
   `do_shortcode` — сейчас `add_filter('wpcf7_form_elements','do_shortcode')` исполняет любой
   шорткод в форме); `wpcf7_posted_data` → `sanitize_text_field` этих двух полей. Шаблоны
   писем с `[omatraffic]` CF7 резолвит сам как поле формы. Удалить `wpcf7_mail_components →
   do_shortcode` (functions.php:114-122: шорткод, набранный посетителем в сообщении, исполнялся
   в письме). Удалить `custom_filter_wpcf7_is_tel` (ни одной `[tel ]` в 41 форме, сигнатура неверна).
2. **Плашка cookie.** Всегда рендерить (сейчас `isset($_COOKIE['cookieNotice'])` запекается в
   кэш и у согласившихся плашка висит, а «Принимаю» не работает — script.js вешает обработчик
   только при отсутствии cookie); скрыта по умолчанию CSS, инлайн-скрипт в head (≤ 200 Б)
   ставит класс `html.ug-consent-needed`, если cookie нет; script.js — один `getCookie` (убрать
   дубль из functions.php:126-137). Это позволит убрать `cookieNotice` из `cache_reject_cookies`
   WP Rocket (§8).
3. **Вебинары.** Удалить `includes/webinar/` целиком: open relay (`wp_ajax_nopriv_submitRegistrationForm`
   без nonce шлёт письмо с телом из POST на любой адрес, init.php:152-216), SMS на любой номер
   без лимита (:98-118), SQL из POST без prepare (:50, report.php:49,73), `session_start` на
   каждом init (:3-8 → PHPSESSID и `Cache-Control: no-store` на всех страницах, убивает bfcache
   и loopback), токен SMS-шлюза в публичном репо (send-sms.php:11). Последняя регистрация —
   2021-09-20. Стаб-шорткоды в `inc/legacy-stubs.php` возвращают `''` для 6 страниц
   (5809, 6693, 6695, 7214, 7992, 10302) и 7232. При деплое: `wp cron event delete <hook из report.php>`,
   экспорт и удаление `r17Y1_webinar_register_log` — решение владельца (ПДн).
4. Удалить `mail.php` (0 ссылок; на проде 200; шлёт `mail()` на kirill.rusakov@intelsib-team.com).

Приёмка: `curl -sD -` на `/` без `Set-Cookie` и `no-store`; `curl '/main/?utm_campaign=%3Cimg%20onerror%3Dalert(1)%3E'`
— payload в HTML отсутствует; отправка формы на стенде с `?utm_medium=cpc&utm_source=test`
даёт в теле письма «Источник - test, Канал - cpc»; `curl -I /wp-content/themes/umbrella-group/mail.php`
→ 404; `POST admin-ajax.php?action=send_otp` → 400/0; 6 страниц /learning/ без сырого текста
шорткода; §4.1 0.0000 % (плашка в эталоне скрыта); §4.3 — отличия только hidden input и плашка.

### WS-2 — конвейер ассетов, шрифты, иконки, платформенные хуки

Файлы: `inc/assets.php`, `inc/setup.php`, `css/**` (css_collector.php удалить; all.min.css,
font-awesome.min.css, fancybox.css→оставить локально для ug-fancybox, slick*.css, services-tiles.css удалить;
form-custom.css влить в main.css), `js/{swiper-bundle.min.js,fancybox.umd.js}` (оставить),
`js/{jquery.mask.js,recaptcha-wpcf7.js}` (удалить), `assets/css/{main,styles-intelsib,tablets-and-down}.css`
→ один `assets/css/main.css`, `assets/css/pages/**`, `assets/js/pages/**`, `assets/js/{main,chat,pop_up}.js`,
`assets/css/icons.css` (новый), `assets/css/vendor/swiper-bundle.min.css` (новый, локальная копия),
`fonts/**`, `assets/img/**`, `assets/css/common-style*.css`, `assets/css/admin/chosen.css`,
и **только строки с `<script src>`/`<link>`** в 6 шорткодах: open_business.php:5-7,
on_call.php:11, case_business.php:6,83, customer_reviews_business.php:6-7,
service_evaluation.php:11, team_business.php:6-8 (WS-5 берёт эти файлы после WS-2).

Шаги:
1. API §3.2 и шим `umbrella_add_custom_*` → `ug_enqueue_*`. Удалить `css_collector.php` и
   хук `get_umbrella_customizations`. Per-page CSS/JS коллектора (`price.css/js` по подстроке
   `/price/`, `faq.js`) → enqueue по `is_page()`; `pages/main.css` не подключать нигде
   (сейчас привязан к ID 2 — черновик; на главной не действует).
2. `ug-main` в head на `wp_head:101`. Сверка каскада: §4.1 на 14 + extra страницах; при
   расхождении — точечно поднять специфичность, а не `!important`. Известное пересечение:
   `#umbrella-menu-button` (header-bus-bottom.php:3, только страница 14148).
3. `assets/js/main.js` (435 Б) и `chat.js` (315 Б, весь код закомментирован) → main.js в
   enqueue, chat.js удалить. 
4. Библиотеки: `ug-swiper`, `ug-fancybox`; из 6 файлов убрать сырые теги, заменить на
   `wp_enqueue_script('ug-swiper')` и т.п. (сейчас на 14148 Swiper трижды в двух мажорных
   версиях 11.2.5 и CDN 14.x без версии; на главной fancybox.umd.js 105 КБ синхронно посреди
   body и без CSS при 7 `data-fancybox`). Проверка: слайдеры `.step_slider_busnisse`,
   `.swiper-result`, отзывы на 14148; лайтбокс на главной.
5. Шрифты: сабсет `Inter.var.woff2` (pyftsubset, `--unicodes` latin+cyrillic+punctuation),
   новый `fonts/inter.css` с одним `@font-face` (`font-weight: 100 900`, `font-display: swap`),
   preload; PT Serif 700 локально; удалить 18 otf, 22 ttf, 6 txt, 18 woff, 20 статических woff2,
   `fonts-configuration.backup.css`. Flatsome: Google Fonts выключить (theme_mod), семейство
   заголовков — локальный PT Serif. Проверка: DevTools → Rendering → Font fallback нет;
   снимки заголовков 1440/390m.
6. `assets/css/icons.css`: `fa fa-telegram` + 5 иконок страниц 1228/28490 как inline-SVG
   (`mask-image`/`background`), классы прежние. WS-3 после этого убирает CDN-ссылку.
7. `inc/setup.php`: `wp_dequeue_style` block-library (113 КБ, 0 классов `wp-block-*` в HTML
   5 страниц, блоки только в wp_navigation #25416), `classic-theme-styles`, `global-styles`
   (8,9 КБ — предварительно `grep 'wp--preset'` по CSS темы и контенту); `remove_action`
   эмодзи; `remove_action('wp_head','flatsome_add_fallbacks')` (IE-условные html5shiv/
   ie-flexibility). Также через theme_mods `disable_blockcss=1`, `disable_emoji=1`.
8. Картинки темы: `assets/img/bg-consl.jpg` 1 226 КБ → webp (экономия ≈ 1 МБ по
   modern-image-formats); `includes/max.png` → `assets/img/max.svg`; иконка flamp (хотлинк
   `flamp.ru/…/f.svg` даёт 403 части клиентов, карточка отзыва меняет высоту) → `assets/img/flamp.svg`,
   ссылка меняется в WS-4 (umbrella_feedback/feedback). `btn-play.svg`, `check-calc.svg`,
   `prompt.svg` (0 ссылок) удалить. `assets/Umbrella_2_1.mp4` 149 МБ (0 ссылок в коде, БД и
   логах) удалить из зеркала и с сервера.
9. Удалить мёртвое: `pop_up.js` 14 КБ, `common-style.min.css` 0 Б, `admin/chosen.css`,
   `css/all.min.css` 102 КБ, `css/font-awesome.min.css`, `css/slick*.css`, `css/services-tiles.css`,
   `js/jquery.mask.js`, `js/recaptcha-wpcf7.js`, `pages/main.css`, `blocks/umbrella_ab_test4_variant2.css`
   (подключает только мёртвый класс AB-модификатора — но партиалы `*_ab_test4_variant2.php`
   живые через theme_mods, их CSS проверить по снимкам 390m перед удалением),
   `blocks/bus-first-screen.js` (живой — business_first_screen.js).
10. Последним коммитом удалить шим, когда `grep -r "umbrella_add_custom_" → 0` (после WS-3…WS-6).
11. Дедуп CSS внутри нового main.css: 70 одинаковых правил в разных файлах, 124 повтора
    внутри файлов, 96 классов без вхождений (63 в styles-intelsib.css) — удалять по правилу
    §2.4; брейкпоинты `849px` в четырёх написаниях привести к одному написанию без смены
    значений (смена 768→849 запрещена — меняет вёрстку на 769–849).

Приёмка: §4.1 0.0000 % на 14 + extra; `grep -c "<style" html-after/home` — только UX Builder;
Lighthouse `/` desktop CLS ≤ 0.1, `render-blocking-resources` без cdnjs/googleapis/block-library;
шрифтов ≤ 250 КБ; `fonts/` ≤ 1 МБ; консоль без 404.

### WS-3 — шапка, подвал, аналитика, мобильная ветка

Файлы: `header.php`, `header-bussnise.php`, `header_1.php`, `header_2.php`, `header_3.php` (удалить),
`page.php`, `page-bussnise.php`, `a-b.php` (удалить), `template-parts/header/**`,
`template-parts/overlays/overlay-menu.php`, `template-parts/analytics.php` (новый), `footer.php` (новый),
`umbrella_footer.php` (корень, удалить), `template-parts/shortcodes/umbrella_footer.php`,
`template-parts/shortcodes/umbrella_footer_1.php` (удалить), `template-parts/shortcodes/footer_map.php`,
`template-parts/shortcodes/umbrella_feedback.php` (рендерится из подвала), `flatsome/footer.php`
(вернуть вендорный вид после появления child footer.php), `flatsome/max-logo.svg` → `assets/img/`.

Шаги:
1. Первым коммитом: убрать `<meta name="ip" content="REMOTE_ADDR">` из header.php:14 и
   header-bussnise.php:12 (в 92 кэш-файлах прода зашиты чужие IP; JS темы и GTM meta не читают —
   проверено grep по assets/js и обоим контейнерам в `/tmp/taxlab-audit/GTM-*.js` — повторить
   grep перед удалением). Удалить header_1/2/3.php (get_header грузит только header.php и
   header-{name}.php; в них второй ключ reCAPTCHA `6LexE8cp…`), a-b.php (шаблон не назначен
   ни одной записи), `umbrella_footer.php` в корне и `umbrella_footer_1.php` (не require),
   `header-main_1/_2.php` (в `_2` var_dump), все `*.old.php`, `ab-tests-header-modifier.php`
   (класс никогда не инстанцируется; require из header-wrapper*.php:3 убрать).
2. **Один header.php.** header-bussnise.php отличается 77 строками из ~360: CDN fancybox/slick
   (slick не используется нигде — `slick(` 0 вызовов), старый `UA-168903290-1`, обёртка
   `header-wrapper-bussnise`. Параметр `variant` через `get_template_part(..., $args)`;
   page-bussnise.php остаётся тонким (`get_header()` + `set_query_var('ug_header_variant','business')`).
   header-wrapper ↔ -bussnise (4 строки diff), header-main ↔ header-bus-main (3), element-ratings ↔
   -bus (14) — один партиал с аргументами. header-bus-top.php = flatsome header-top.php (diff 0) —
   удалить, звать `'top'`.
3. **Без `wp_is_mobile()`** (header-wrapper*.php:8, overlay-menu.php:33; su_welcome_screen.php
   — у WS-4): один DOM, показ/скрытие классами Flatsome `show-for-medium`/`hide-for-medium`.
   Это снимает нужду в отдельном мобильном кэше WP Rocket (сейчас из-за `.mobile-active`
   Rocket-Nginx не отдаёт статику никому). Приёмка — снимки 390m и 1440 без отличий, DOM
   mobile = DOM desktop на `/about/` (сейчас 569 строк diff).
4. **`template-parts/analytics.php`**: единственное место для gtag/GTM/ROISTAT/yadro/marquiz,
   включается из header.php. По умолчанию состав не меняется (решение владельца, §10);
   меняется форма: 8 копий загрузчика marquiz (header.php:95-292, три id квиза по
   `REQUEST_URI` с URL-encoded кириллицей) → один загрузчик + карта «slug страницы → id квиза»;
   ROISTAT-блок на jQuery в начале body (header.php:318-327) → defer-скрипт после jQuery.
   Протокольно-относительные `//script.marquiz.ru`, `//counter.yadro.ru` → `https://`.
   `user-scalable=no, maximum-scale=1.0` из viewport убрать (a11y).
5. Инлайн `ul.tabs` (header.php:20-50) → ug-main (строки передать WS-2 одним списком —
   пересечение по одному блоку CSS, вставляет WS-2). CDN Font Awesome (header.php:18) убрать
   после `icons.css` WS-2.
6. **Подвал.** `footer.php` в child: копия вендорного без правленого блока + кнопки Telegram/Max
   через `add_action('flatsome_footer', …, 99)` из шаблона `template-parts/blocks/messenger-buttons.php`;
   их CSS → ug-main; `max-logo.svg` → `assets/img/`. Вендорный `flatsome/footer.php` после
   этого к работе не относится (override), в §9 — восстановить из дистрибутива при обновлении.
   В `umbrella_footer.php`: невалидные `<a 2 href>`/`<a 3 href>` (:77, :109); иконки
   `telegram_logo.png` 115 КБ / `vk_logo.png` 88 КБ / `whatsapp_logo.png` 50 КБ (исходники
   1024 px, показаны 42 px) и `logo-3.png` → SVG/webp с width/height; `fetchpriority`, `alt`.
   `footer_map.php:14` — `loading="lazy"` у iframe карты (2,7 МБ на главной сразу) и `title`.
   `umbrella_feedback.php:51` — 115 warnings «Array to string conversion» (`strlen((string)$array)`
   и смесь `&&`/`||` без скобок); чинить с проверкой списка `excluded_pages` (29 ID в
   `client_shortcode_options`): на этих страницах блока нет, на остальных есть.
7. `element-ratings.php:17`: фон `max-icon.png` 1080×1080, 395 КБ под 32 px → `assets/img/max.svg`
   (из WS-2); `laurel-1.svg` абсолютным URL `https://taxlab.ru/…` → относительный.

Приёмка: §4.1 на 14 + extra (особенно 14148 на 1440/390m); §4.3 — ожидаемые отличия:
нет meta ip, один marquiz-блок, viewport; в DevTools `dataLayer` содержит те же события
`gtm.js`, что до; `roistat.event.send` вешается на «Скачать анкету/памятку» (проверить на
странице с этими ссылками); консоль без ошибок.

### WS-4 — шорткоды услуг

Файлы: `template-parts/shortcodes/{su_welcome_screen,su_welcome_screen_conditions,cases,feedback,
specialists,komanda_specialistov,faq,accordion,akcii,service-tiles,buhuslugi_packs_table,video_block,
request_call_back,why_choose_us,why_umbrella,garantiya-srokov}.php` и их CSS/JS в `assets/{css,js}/blocks/`,
`template-parts/faq/archive-simple.php`, новые `template-parts/blocks/<имя>.php`, `inc/helpers.php`
(создаёт WS-4; `ug_ru_date`, `ug_current_service_section`, `ug_render` — WS-5/6 пользуются, не правят).

Шаги (по §3.3 для каждого файла; порядок — от самых частых: su_welcome_screen 73 страницы,
cases 62, feedback 56, specialists 52, akciya_block 52, accordion 76):
1. `service-tiles.php`: whitelist `custom_css` (§3.2); `$hideforsmall` undefined (:184, 63 warnings
   на обход, 2 387 строк в день в лог антибота); `$tabs` (:92) и перепутанные кавычки
   `class='main-services-tile-col" span="12"]`; шорткод `service_tiles` живёт в UX-блоке.
2. `su_welcome_screen.php`: 9 проверок `REQUEST_URI` и 3 `wp_is_mobile()` (:165,192,201) →
   `ug_current_service_section()` и один DOM; баннер первого экрана — `loading="eager"
   fetchpriority="high"` + width/height (это LCP на страницах услуг, сейчас через заглушку
   lazy-load: LCP 12,8 с mobile на алкоголе).
3. `akcii.php:47`: дата «До 01.<след. месяц>.<текущий год>» — в декабре даст прошедшую дату;
   та же логика в `template-parts/akcii/archive-2-col.php:69` и `single.php:18` с захардкоженным
   `2026` (у WS-6) → `ug_ru_date`/`(new DateTimeImmutable('first day of next month'))`.
   `get_the_post_thumbnail` баннера акции получает `fetchpriority="high"` внизу страницы →
   передать `['fetchpriority' => 'low', 'loading' => 'lazy']`.
4. `why_umbrella.php:22`: rutube iframe (≈ 730 КБ сразу) → фасад: превью + iframe по клику,
   `title` у iframe. В эталоне iframe замаскирован белым прямоугольником того же размера —
   фасад должен занимать тот же бокс (1200×405 по атрибутам).
5. `cases.js:22-24`: `touchmove` с `{passive:false}` снимает все обработчики `.slider` — убрать;
   `faq.js`/`price.js` scroll-spy — один модуль, `IntersectionObserver`.
6. N+1 в cases.php:237-291, feedback.php:178-235 → один запрос + transient.
7. Иконка flamp → `assets/img/flamp.svg` без lazy-load (видимое изменение: в эталоне внешние
   img замаскированы — ожидаемое отличие, зафиксировать в коммите, §7.3).
8. `wp_is_mobile`-ветки и `REQUEST_URI` в specialists/komanda_specialistov/faq/cases/feedback
   → helper; таблица «URL → категория» до/после по всем `services/*`.

Приёмка: §4.1 0.0000 % (14 + extra; алкоголь, бухуслуги, ликвидация, арбитраж, лицензирование
— все в наборе), §4.3 без отличий кроме width/height/loading/fetchpriority/title, warnings 0,
SQL на `/services/licensing/` 180 → ≤ 120, Lighthouse алкоголь mobile LCP ≤ 3.5 с.

### WS-5 — шорткоды главной, бизнес-лендинга 14148 и акций-лендингов

Файлы: главная — `{new-main-first-screen,numbers_statistics,help_start_business,trouble_2022,
service_evaluation,on_call,free_consultation,stock,komanda_specialistov→WS-4}.php`; 14148 —
`{bus-first-main,business-directions,static_startup,ignor_static,since_90,open_business,
calculate_business,form_business_calculation,case_business,result_clinet_business,
customer_reviews_business,more_question_bus,team_business,foooter_business_page,form_bottom_business}.php`;
акции — `{akciya_tz,akciya_pb}.php`; все их CSS/JS в `assets/{css,js}/blocks/`; `includes/upsell.php`
(после шага 0). Шесть файлов из списка WS-2 (open_business, on_call, case_business,
customer_reviews_business, service_evaluation, team_business) — **только после merge WS-2**.

Шаги:
1. `new-main-first-screen.php`: первый слайд `fetchpriority="high"` + `<link rel=preload as=image>`,
   остальные `fetchpriority="low"`; `block-laurels.js:4,6,8` console.log (562 рендера из 602).
2. `calculate_business.php:11-44, 81-115, 144, 306`: `WP_Query(posts_per_page=-1)` внутри
   foreach по термам → один запрос; SQL на 14148 338 → ≤ 150.
3. `form_business_calculation` ↔ `form_bottom_business` (14 строк diff) → один шорткод,
   `variant="bottom"`, старые имена шорткодов сохраняются как алиасы.
4. `akciya_tz.php` (363 + 618 CSS) ↔ `akciya_pb.php` (256 + 788 CSS): общие секции hero/audience/
   steps/cta — один компонент с данными; 9 имён шорткодов остаются.
5. Инлайн-скрипты heredoc → файлы блоков; после этого `strategy=>'defer'` для хэндлов блока.
6. console.log/debugger: services-tiles.js:45,162 (WS-4), calculate_business.js:115,
   team_business.js:20,33, business_first_screen.js:17, case_business.js:18, static_startup.js:20,
   buhuslugi-packs-block.js:54 (WS-4); ~420 строк закомментированного кода
   (buhuslugi-packs-block.css 100, on_call.js 72, buhuslugi_packs_table.php 67, main.css 32).

Приёмка: §4.1 на `/`, 14148, 30768, 30770 (1440/390m) 0.0000 %; слайдеры 14148 и лайтбокс
главной работают; консоль чистая; Lighthouse `/` mobile ≥ 75.

### WS-6 — шаблоны записей, архивы, CPT, tiles

Файлы: `archive.php`, `archive-akcii.php`, `archive-faq.php`, `single.php`, `single-akcii.php`,
`single-client.php`, `sidebar-akcii.php`, `404.php`, `template-parts/posts/**`, `template-parts/akcii/**`,
`includes/custom_post_types/**`, `includes/pages/**`, `includes/blog-side-panel.php`,
`includes/draw_header_with_breadcrumbs.php`, `inc/post-types.php`, `inc/tiles.php`, `inc/shortcodes.php`,
`template-parts/shortcodes/wp-blog-header.php` (удалить).

Шаги:
1. Удалить переопределения Flatsome, идентичные родителю по `diff -wB` (проверено: отличия —
   HTML-комментарии): `posts/archive.php`, `posts/single.php`, `loop/post-simple.php`,
   `partials/archive-title.php`, `entry-footer-single.php`, `entry-image.php`, `entry-post-date.php`,
   `entry-video.php`, `single-featured.php`; `entry-header-bottom.php` (у родителя
   `get_theme_mod('blog_badge',1)`, мод не задан → тот же вывод); `entry-footer.php` (своё —
   только сломанный `<script>$(document)…console.log("fffff")`); `content.php` (только аргументы
   `wp_link_pages`, `<!--nextpage-->` в контенте 0); `layout-aside.php` = child `layout.php`
   (в single.php:15 звать `layout`); `posts/old_archive-2-col.php`; `*.old` в includes.
   Приёмка — снимки `/blog/`, поста, поста формата aside.
2. `archive*.php` (2 строки diff из 23) → один с `match(get_post_type())`; `posts/*` ↔ `akcii/*`
   (57/67/16 строк) → общие шаблоны с параметром `post_type`.
3. `inc/post-types.php`: 7 копипастных файлов → реестр + `ug_register_meta_box(schema)`;
   `save_post` без nonce/cap/sanitize/проверки типа (specialist.php:202-225, feedback.php:208-233,
   cases.php:229-257; akcii/faq — без nonce и прав) → `save_post_{type}` по образцу
   client.php:266-300; HTML-поля (`specialist_description`, `specialist_achievements`) —
   `wp_kses_post`. `print_r` вместо `echo` в колонках админки (faq.php:65, akcii.php:71-80,
   client.php:368). `client.php:399-400` ссылается на несуществующие chosen.* — убрать.
   CPT `client`: `publicly_queryable => false` (18 URL `/client/<slug>/` отдают заглушку
   «Шаблог страницы типа 'клиент'»; ссылок на них в контенте и sitemap 0) + 301 на `/about/clients/`;
   `single-client.php` удалить. Проверка: пересохранить по записи каждого CPT на стенде,
   postmeta до/после совпадает.
4. `inc/tiles.php`: 432 строки CSS внутри PHP (бывш. functions.php:401-610, 623-747, 799-886)
   → `assets/css/blocks/tiles.css`, `ug_enqueue_style` при вызове; ветка `'cases'` мертва
   (вызывающие передают только blogposts/clients/feedback) — удалить; 4 копии массива месяцев
   → `ug_ru_date`. Сигнатуры контракта §2.4 сохранить.
5. `includes/pages/feedback-page.php:34` — 14 warnings (undefined key / offset on null);
   `cases-page.php` (0 ссылок) и `laurel-badge.html` (грузит только невставленный UX-блок 9237)
   удалить; `wp-blog-header.php` (копия ядра) удалить.

Приёмка: §4.1 на `/blog/`, посте, `/akcii/`, `/faq/`, `/about/clients/`, `/feedback/`, 404,
поиске — 0.0000 %; warnings 0; `/client/<slug>/` → 301.

### WS-7 — разворачивание A/B-тегов (нужно одобрение владельца: меняет контент)

Файлы: `umbrella_filter_ab_tests.php`, `inc/ab-tests.php`, `tools/ab-flatten.php` (новый).
После WS-2 (коллектор звал `umbrella_filter_ab_tests` для CSS — css_collector.php:36,39).

Факты: `$expVar1 = 1` захардкожен (umbrella_filter_ab_tests.php:86) — варианты фиксированы,
но на каждый некэшированный рендер ставится cookie `exp_mXyZSFCYQ-…` на `.taxlab.ru` и на
каждый `the_content` крутятся 9×9 `preg_replace`. Теги в 28 опубликованных записях
(`<umbrella_ab_test7_variant1>` 51 раз), test1 — в 1217, 28486, 868; test2 — в 2.
`add_filter('wp_footer', …)` (functions.php:250) ничего не делает — wp_footer это action.

Шаги:
1. `tools/ab-flatten.php` для `wp eval-file`: для каждой записи с `<umbrella_ab_test` в
   `post_content` применить текущую `umbrella_filter_ab_tests()` к контенту и сохранить
   (ревизия создаётся). Перед этим экспорт этих записей `wp post get --field=post_content` в файлы.
2. На стенде: прогнать, затем §4.3 по этим 28+3 URL — HTML должен совпасть. Риск:
   `umbrella_content_fix` чистит `<p>`/`<br>` вокруг тегов, а фильтр стоит после `wpautop` —
   возможны расхождения в абзацах; правятся руками в контенте до совпадения.
3. Удалить файл, `inc/ab-tests.php`, cookie, параметр `ab_test` у шорткодов (umbrella_feedback.php:52-53 —
   согласовать с WS-3: он не трогает эти строки).
4. На проде — те же шаги после одобрения, с экспортом записей.

Приёмка: HTML 31 URL идентичен; `Set-Cookie exp_*` нет; `grep -r umbrella_ab_test` по теме → 0.

### WS-8 — PHP-сниппеты insert-php → тема (нужно одобрение: меняет контент)

Факты: 31 сниппет `wbcr-snippets` исполняется через `eval` из БД; 6 не используются (57, 528,
1121, 28410, 29846, 30354); сниппеты зовут функции темы (§2.4) и дают warnings в eval'd code
(`$align`, `$flatsome_footer_*`). Самые частые: 786 (плитки услуг, 51 пост), 1961 и 10644 (по 35),
8152 (15), 701 (14 мест в теме).

Шаги: код каждого используемого сниппета → `inc/snippets/<id>-<slug>.php` с `add_shortcode('ug_<slug>')`;
в контенте `[wbcr_php_snippet id=786]` → `[ug_service_tiles]` через `wp search-replace` с бэкапом;
сверка §4.3; неиспользуемые 6 — в черновик. После этого плагин insert-php можно удалить (§9).
После WS-6 (контракт функций).

---

## 6. Списки на удаление (сводно, с доказательствами)

Доказательства: runtime-трассировка 602 рендеров (`/tmp/taxlab-audit/trace/file_usage.json`),
grep по коду/Flatsome/плагинам, выгрузки БД, прод-логи 08–09.10.2026 (`/tmp/taxlab-audit/deadcode.md`).

### 6.1 PHP (кто удаляет)

| Файл | Доказательство | WS |
|---|---|---|
| functions_1.php, functions_2.php, functions_3_1.php | WP грузит только functions.php; 0 require; runtime 0/602 | 0 |
| header_1.php, header_2.php, header_3.php | get_header грузит header.php / header-{name}.php; `_` не грузится; единственный `get_header('bussnise')` в page-bussnise.php:13 | 3 |
| a-b.php | шаблон «A-B» не назначен ни одной записи (`_wp_page_template`: default 167, page-blank 6, page-bussnise 1) | 3 |
| umbrella_footer.php (корень), template-parts/shortcodes/umbrella_footer_1.php | functions.php:224 require только template-parts/shortcodes/umbrella_footer.php; 109/98 строк diff | 3 |
| template-parts/header/header-main_1.php, header-main_2.php, *.old.php (3), ab-tests-header-modifier.php | header-wrapper.php:11 зовёт `header-main`; `new AB_tests_header_modifier` нигде; в `_2` var_dump | 3 |
| template-parts/header/header-bus-top.php | diff 0 с flatsome header-top.php | 3 |
| mail.php | 0 ссылок; на проде 200; mail() подрядчику | 1 |
| includes/webinar/** (14 файлов, в т.ч. jquery.inputmask.min.js 144 КБ) | §WS-1; последняя запись 2021-09-20 | 1 |
| template-parts/posts/{archive,single,content,layout-aside,old_archive-2-col}.php, loop/post-simple.php, partials/{archive-title,entry-footer,entry-footer-single,entry-header-bottom,entry-image,entry-post-date,entry-video,single-featured}.php | diff -wB с Flatsome: только комментарии (проверено на archive, single, post-simple, content, entry-header-bottom) | 6 |
| includes/custom_post_types/{blog,cases}.php.old, includes/pages/{feedback-page.php.old,cases-page.php,laurel-badge.html}, template-parts/shortcodes/wp-blog-header.php, single-client.php | 0 require; wp-blog-header — копия ядра (diff 0); UX-блок 9237 никуда не вставлен | 6 |
| css/css_collector.php | заменён inc/assets.php | 2 |
| umbrella_filter_ab_tests.php | после разворачивания тегов | 7 |
| umbrella_date_of_post() (functions.php:895), custom_filter_wpcf7_is_tel(), umbrella_add_custom_css(), ветка 'cases' в umbrella_draw_tiles | 0 вызовов / сломаны | 0, 1, 2, 6 |

### 6.2 CSS/JS

| Файл | Доказательство | WS |
|---|---|---|
| css/all.min.css 102 КБ, css/font-awesome.min.css 31 КБ, css/slick.css, css/slick-theme.css, css/services-tiles.css | 0 вхождений в 602 рендерах; FA 4.7 грузится с CDN; `slick(` 0 вызовов; service-tiles.php подключает assets/css/blocks/services-tiles.css | 2 |
| assets/css/pages/main.css | только при `get_the_ID()==2` — черновик «Главная(Старый вариант)» | 2 |
| assets/css/common-style.min.css (0 Б), assets/css/admin/chosen.css, assets/css/blocks/umbrella_ab_test4_variant2.css | 0 Б; не подключён; подключал только мёртвый класс (проверить снимками 390m) | 2 |
| assets/js/pop_up.js 14 КБ, assets/js/blocks/bus-first-screen.js, assets/js/chat.js, js/jquery.mask.js, js/recaptcha-wpcf7.js | 0 ссылок; chat.js — весь код закомментирован; ключ reCAPTCHA в recaptcha-wpcf7.js не совпадает с живым | 2 |
| assets/css/{styles-intelsib,tablets-and-down}.css, css/form-custom.css, fonts/fonts-configuration.css | влиты в ug-main | 2 |

### 6.3 Шрифты, медиа

| Что | Доказательство | WS |
|---|---|---|
| fonts/*.otf (18), *.ttf (22), *.txt (6), *.woff (18), 20 статических *.woff2, fonts-configuration.backup.css | inter.css ссылается только на woff2/woff; остаётся variable-сабсет; в прод-логах только woff2 | 2 |
| assets/Umbrella_2_1.mp4 149 МБ | 0 ссылок в коде, БД (включая черновики), логах; в git не может быть | 2 |
| assets/img/btn-play.svg, check-calc.svg, prompt.svg | 0 ссылок (prompt берётся из uploads) | 2 |
| flatsome/max-logo.svg | переезжает в assets/img | 3 |

На проде удалённые файлы сначала переименовать (`.removed`) и неделю смотреть 404 по ним в
access-логе, потом удалить.

---

## 7. Риски и откат

### 7.1 Технические риски

| Риск | Где | Смягчение |
|---|---|---|
| Каскад CSS перевернётся при переносе в head | WS-2 шаг 2; 32 пересечения с custom-css; `#umbrella-menu-button` в body-инлайне | `wp_head:101`; combine CSS off на проде; снимки на каждом шаге; getComputedStyle ключевых узлов (.header-bottom, ul.tabs, h1, #logo, .button.primary) |
| `defer` сломает инлайн-скрипты, ждущие jQuery/Swiper | WS-2/5 | defer только по хэндлу после выноса инлайна в файлы; ROISTAT-блок первым |
| Flatsome lazy-load off → картинки ниже первого экрана грузятся сразу, высоты блоков на заглушке 100×100 | WS-2/4 | нативный `loading="lazy"` + width/height у всех img темы; CLS по perf.mjs ≤ 0.1 |
| Переход на Swiper 11.2.5 с CDN 14.x на 14148 | WS-2 шаг 4 | прокликать слайдеры; при различиях — закрепить локальную 14.x |
| Потеря атрибуции лидов на период смены cookie | WS-1 | старая `source_cookie` в PHP-serialize — не читаем; у вернувшихся посетителей атрибуция сбросится один раз на ≤ 30 дней, принимаем (90 из 92 кэш-страниц прода и так раздают «direct») |
| Миграция A/B и сниппетов меняет прод-БД | WS-7/8 | экспорт записей до, HTML-дифф после, одобрение владельца |
| Удалённый файл подключается извне (плагин/сниппет с динамическим путём) | §6 | обход 302 URL до/после, 0 новых строк в логах; на проде — переименование на неделю |
| Переход `heredoc → шаблон` меняет пробелы внутри `[section]/[row]/[col]` | WS-4/5 | нормализованный HTML-дифф (§4.3) обязателен для каждого шорткода |
| Удаление мёртвых классов CSS, которые собирает JS конкатенацией | WS-2 шаг 11 | правило §2.4: grep по коду, БД, theme_mods, GTM-контейнерам |
| variable Inter рисует Light-BETA/Black чуть иначе | WS-2 шаг 5 | снимки заголовков; при расхождении — оставить два статических файла |
| Эталон устареет (смена месяца, синк зеркала) | все | переснять с тега `before-modernize` (§4.1) |

### 7.2 Откат

Код: каждый пакет — ветка; `git revert` merge-коммита; на проде — `mv` каталогов (§8).
Настройки: перед деплоем `wp option get theme_mods_umbrella-group --format=json` и
`wp option get wp_rocket_settings --format=json` в файл; откат — `wp option update … < файл`.
Контент (WS-7/8): `wp post update <id> <file>` из экспортов или ревизии.

### 7.3 Намеренные видимые изменения (отдельные коммиты, после одобрения)

- `/akcii/` 390m: заголовки упираются в правую рамку (`cases-tiles.css:89-97` padding-left 25
  без padding-right) → `padding-inline: 25px`.
- Иконка flamp локально (в эталоне внешняя картинка замаскирована).
- `laurel-5-400x400.png` появится после `sync-from-server.sh` (на проде 200, 42 КБ).
- Контраст текста (a11y color-contrast до 32 элементов) — только с дизайнерским решением владельца.

---

## 8. Выкатка на прод

Автодеплоя нет (CLAUDE.md): на сервере нет `.git`, файлы заливаются вручную. wp-cli прода:

```
ssh annikov.com 'sudo -u taxlabru /opt/plesk/php/8.3/bin/php /usr/local/bin/wp --path=/var/www/vhosts/taxlab.ru/httpdocs <команда>'
```

Порядок (один вечер, окно без правок контента редакторами):

1. **Бэкап.** `tar czf` каталога `umbrella-group` и `flatsome/footer.php`; `wp db export`
   (или минимум `wp post get` для записей WS-7/8); `wp option get` theme_mods и wp_rocket_settings в JSON.
2. **Файлы.** Залить новую тему в `umbrella-group.new` (Plesk File Manager / SFTP из IDE),
   затем атомарно: `mv umbrella-group umbrella-group.old && mv umbrella-group.new umbrella-group`.
   Так не будет состояния «functions.php уже новый, inc/ ещё нет» (fatal на весь сайт).
   Файлы §6 в `.old` остаются для отката; удалить через неделю.
3. **Настройки (wp-cli):**
   - theme_mods: `lazy_load_images 0`, `lazy_load_backgrounds 0`, `disable_blockcss 1`, `disable_emoji 1`,
     шрифты (через `wp eval` — массив сериализован).
   - WP Rocket (с одобрения): `minify_concatenate_css 0`; `cache_reject_cookies` без `cookieNotice`
     и `exp_test`; `preload_fonts` — один Inter + PT Serif; `remove_unused_css 0` (не работает: 0 строк
     в `wpr_rucss_used_css`); `purge_cron_interval` ≈ 10 ч. Остальные настройки Rocket — §9.
   - `wp cron event delete <webinar hook>`; WS-7/8 — `wp eval-file`.
4. **Кэш.** `wp eval 'rocket_clean_domain();'` + `wp eval 'rocket_clean_minify();'` + `wp cache flush`
   (Redis на проде не запущен и drop-in нет — команда безвредна). Версии ассетов по filemtime
   обновят браузерный кэш сами.
5. **Проверка.** В браузере (curl видит капчу антибота): главная, услуга, 14148, `/price/`,
   `/blog/`, форма с тестовой заявкой → Flamingo; Tag Assistant / отладчик Метрики —
   события те же; `curl -I …/mail.php` → 404 (капча отдаёт 200 на HTML — проверять по
   `x-robots-tag`/размеру или из браузера); заголовки страницы без `PHPSESSID`. PSI «после» на
   8 URL. Прод-лог: ошибки PHP в `protect918/data/errorlog.txt` по дате — около нуля.
6. **Откат:** `mv umbrella-group umbrella-group.failed && mv umbrella-group.old umbrella-group`,
   `wp option update` из JSON, п. 4.

---

## 9. Рекомендации вне скоупа (для владельца)

| # | Что | Польза | Риск / цена |
|---|---|---|---|
| 1 | **Срочно:** закрыть `/protect918/data/` (errorlog.txt 578 МБ и sqlite.db 81 МБ с IP/UA посетителей скачиваются публично), `/F_F/` («Searcher» по файлам и БД, db_user root), `/phpinfo.php`, `index_2.php` — `deny all` в vhost_nginx.conf или удалить | закрывает утечку ПДн и структуры сервера | нулевой; проверка — те же URL дают 403 |
| 2 | **Срочно:** отозвать токен SMS-шлюза smsint (в публичном git с initial commit) и Global API Key Cloudflare из `wp_rocket_settings` (DNS-only, ключ бесполезен, уходит в каждый дамп) | — | у кого-то могут перестать уходить SMS |
| 3 | nginx: `expires`/`Cache-Control` для статики не работают (regex-location Plesk nginx.conf:217 перекрывает vhost_nginx.conf), то же для правил webp-express | повторные визиты без перекачки 1–2 МБ; webp отдаётся | без версий в URL — залипание; тема после рефакторинга версионирует всё |
| 4 | WP Rocket 3.10.2 нелегальный («BAD_WEBSITE»), RUCSS/critical не работают; лицензия + 3.19 (self-host fonts, Optimize critical images, Delay JS) или замена | Delay JS даёт основной выигрыш по сторонним скриптам | RUCSS/Delay JS ломают слайдеры/формы — только со снимками |
| 5 | После WS-3/4 (нет `wp_is_mobile`): выключить мобильный кэш Rocket → Rocket-Nginx отдаёт HTML статикой | TTFB без PHP и антибота | закэшированные страницы обходят антибот — решение владельца |
| 6 | Состав счётчиков: GTM-TRFKKFL (header.php) + GTM-WW2PFC (theme_mods) + третий через контейнер, gtag G-L1SYSHHPK5 дважды, мёртвый UA-168903290-1, Метрика tag.js + watch.js, VK openapi дважды, venyoo 612 КБ без сжатия, qoopler шлёт все cookie третьей стороне | −1–1,5 МБ и −100–150 мс блокировки на каждой странице | потеря целей/конверсий — согласовать с маркетингом |
| 7 | Flatsome 3.13.4 → 3.20.x (XSS закрыта в 3.20.11), затем WP 6.5.10 → 7.1.x, CF7 | поддержка и безопасность; скорость +единицы % | вендорные правки вне git (`flatsome/footer.php`, по mtime ещё functions.php, header.php, template-parts/footer/*, inc/structure/* — проверить diff с чистым 3.13.4) будут перезаписаны; `sizes=auto` (6.7) и on-demand block CSS (6.9) — снимки до/после |
| 8 | Плагины: duplicate-post + post-duplicator (дубль), duplicator (бэкапы в Plesk), wp-add-custom-css (после переноса 151 Б и 64 per-post CSS 127 КБ), insert-php (после WS-8), redis-cache (Redis не запущен — включить с maxmemory или удалить), неактивные с диска, yandex-metrica vs Метрика в GTM | меньше PHP на запрос, меньше поверхности | per-post CSS переносить по ID со снимками |
| 9 | БД: 12 285 ревизий (173 МБ) → `WP_POST_REVISIONS 5` + чистка; `_transient_dirsize_cache` 157 КБ autoload; Flamingo 3 462 заявки без срока; сиротские таблицы mailpoet/newsletter (10 854 подписчика — ПДн) | бэкапы, дампы стенда, админка | необратимо — только после дампа |
| 10 | Антибот отдаёт анонимным мобильным капчу с `no-store` вместо сайта — вклад в LCP первого визита; измерить долю через CrUX (нужен PSI API-ключ) | понимание реального LCP | ослабление → спам в формы |
| 11 | 6 страниц /learning/ 2020–2021 с формой вебинара — снять с публикации; таблицу `webinar_register_log` (465 ПДн) экспортировать и удалить | — | — |
| 12 | Контент: 29 page + 7 blocks с инлайн `<style>` (13 КБ), 15 форм CF7 со стилями (8,8 КБ), 50 копий форм CF7 в DOM главной (попапы) | меньше HTML | правки контента руками |
| 13 | `DISABLE_WP_CRON` + системный cron; `laurel-5-400x400.png` в зеркало (`sync-from-server.sh`) | — | — |

---

## 10. Вопросы владельцу (ответы не блокируют WS-0…WS-6)

1. Какие счётчики живые (GTM-TRFKKFL / GTM-WW2PFC / G-L1SYSHHPK5 / UA / Метрика ×2 / VK ×2 / venyoo / qoopler / roistat / yadro / marquiz ×3)? До ответа WS-3 сохраняет состав, меняя только форму.
2. Одобрение WS-7 (A/B-теги в 28 записях) и WS-8 (сниппеты → тема, 25 замен в контенте).
3. Вебинарные страницы: снять с публикации или оставить со стабом?
4. WP Rocket: можно ли выключить Combine CSS и править reject-cookies/preload? Если нет — `exclude_css`.
5. Выключать ли мобильный кэш Rocket после удаления `wp_is_mobile` (страницы пойдут мимо антибота)?
6. PT Serif в заголовках оставить (self-host) или перейти на Inter везде (визуальное изменение)?
7. Три видимых правки §7.3 — делать?
