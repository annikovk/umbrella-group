<?php
/**
 * Пользовательские типы записей (7 CPT): реестр, таксономии клиентов, колонки
 * админки, мета-боксы и их сохранение.
 *
 * Раньше — 7 копипастных файлов includes/custom_post_types/*.php (WS-6 свёл
 * их сюда). Аргументы register_post_type/register_taxonomy, ключи меты, id
 * мета-боксов и колонки — прежние, порядок регистрации тот же.
 *
 * Сохранение меты — save_post_{type} с nonce, current_user_can и санитайзером
 * на каждое поле (раньше у specialist/feedback/case в БД писался сырой $_POST
 * на save_post любой записи, у akcii/faq — без nonce и прав). Поле, которое
 * пришло из формы без изменений, не перезаписывается: санитайзер не трогает
 * уже сохранённые значения (например, <br/> в авторах отзывов).
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Реестр CPT: тип => аргументы register_post_type(). Порядок = порядок регистрации.
 */
function ug_post_types(): array
{
    $labels = static fn(array $l): array => array_map('__', $l);

    return [
        'client' => [
            'label' => __('Клиенты'),
            'description' => __('Клиенты и их отзывы'),
            'labels' => [
                'name' => _x('Клиенты', 'Post Type General Name'),
                'singular_name' => _x('Клиент', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Клиенты',
                'all_items' => 'Все клиенты',
                'add_new_item' => 'Добавить нового клиента',
                'add_new' => 'Добавить нового',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск клиенты',
                'not_found' => 'Клиентов не найдено',
            ]),
            'rewrite' => ['slug' => 'client'],
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'],
            'taxonomies' => ['industry', 'category'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-users',
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            // Своих страниц у клиента нет: /client/<slug>/ отдавали заглушку
            // «Шаблог страницы типа 'клиент'» (ссылок в контенте и sitemap 0).
            // Клиенты выводятся плитками на /about/clients/ — туда и 301 ниже.
            'publicly_queryable' => false,
            'capability_type' => 'post',
        ],
        'akcii' => [
            'label' => __('Акции'),
            'description' => __('Акции компании taxlab'),
            'labels' => [
                'name' => _x('Акции', 'Post Type General Name'),
                'singular_name' => _x('Акция', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Акции',
                'all_items' => 'Все акции',
                'add_new_item' => 'Добавить новую акцию',
                'add_new' => 'Добавить новую',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск акций',
                'not_found' => 'Акций не найдено',
            ]),
            'rewrite' => ['slug' => 'akcii'],
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-money-alt',
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        ],
        'faq' => [
            'label' => __('Вопросы'),
            'description' => __('Часто задаваемые вопросы'),
            'labels' => [
                'name' => _x('Вопросы', 'Post Type General Name'),
                'singular_name' => _x('Вопрос', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Вопросы',
                'all_items' => 'Все вопросы',
                'add_new_item' => 'Добавить новый вопрос',
                'add_new' => 'Добавить новый',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск вопросов',
                'not_found' => 'Вопросов не найдено',
            ]),
            'rewrite' => ['slug' => 'faq'],
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-pressthis',
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        ],
        'specialist' => [
            'label' => __('Специалисты'),
            'description' => '',
            'labels' => [
                'name' => _x('Специалисты', 'Post Type General Name'),
                'singular_name' => _x('Специалист', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Специалисты',
                'all_items' => 'Все специалисты',
                'add_new_item' => 'Добавить нового специалиста',
                'add_new' => 'Добавить нового',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск специалистов',
                'not_found' => 'Специалистов не найдено',
            ]),
            'rewrite' => ['slug' => 'specialist'],
            'supports' => ['title', 'thumbnail'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-businessperson',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
        ],
        'case' => [
            'label' => __('Кейсы'),
            'description' => '',
            'labels' => [
                'name' => _x('Кейсы', 'Post Type General Name'),
                'singular_name' => _x('Кейс', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Кейсы',
                'all_items' => 'Все кейсы',
                'add_new_item' => 'Добавить новый кейс',
                'add_new' => 'Добавить новый',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск кейсов',
                'not_found' => 'Кейсов не найдено',
            ]),
            'rewrite' => ['slug' => 'case'],
            'supports' => ['title', 'thumbnail', 'page-attributes'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-excerpt-view',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
        ],
        'feedback' => [
            'label' => __('Отзывы'),
            'description' => '',
            'labels' => [
                'name' => _x('Отзывы', 'Post Type General Name'),
                'singular_name' => _x('Отзыв', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Отзывы',
                'all_items' => 'Все отзывы',
                'add_new_item' => 'Добавить новый отзыв',
                'add_new' => 'Добавить новый',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск отзывов',
                'not_found' => 'Отзывов не найдено',
            ]),
            'rewrite' => ['slug' => 'feedback'],
            'supports' => ['title', 'thumbnail', 'page-attributes'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-feedback',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
        ],
        'result' => [
            'label' => __('Результаты'),
            'description' => __('Результаты других клиентов'),
            'labels' => [
                'name' => _x('Результаты клиентов', 'Post Type General Name'),
                'singular_name' => _x('Результат', 'Post Type Singular Name'),
            ] + $labels([
                'menu_name' => 'Результаты',
                'all_items' => 'Все результаты',
                'add_new_item' => 'Добавить новый результат',
                'add_new' => 'Добавить новый',
                'edit_item' => 'Редактировать',
                'update_item' => 'Обновить',
                'search_items' => 'Поиск результата',
                'not_found' => 'Результат не найдено',
            ]),
            'rewrite' => ['slug' => 'result'],
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-pressthis',
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        ],
    ];
}

/** Типы, которые редактируются в UX Builder Flatsome. */
const UG_UX_BUILDER_TYPES = ['client', 'akcii', 'faq', 'specialist', 'case', 'feedback'];

/** Таксономии клиентов (были в client.php). */
function ug_register_client_taxonomies(): void
{
    register_taxonomy('client_industry', ['client'], [
        'hierarchical' => true,
        'label' => 'Отрасль',
        'labels' => [
            'name' => _x('Отрасль', 'taxonomy general name'),
            'singular_name' => _x('Отрасль', 'taxonomy singular name'),
            'search_items' => __('Поиск отрасли'),
            'all_items' => __('Все отралси'),
            'parent_item' => __('Parent'),
            'parent_item_colon' => __('Parent:'),
            'edit_item' => __('Редактировать отрасль'),
            'update_item' => __('Обновить отрасль'),
            'add_new_item' => __('Добавить новую отрасль'),
            'new_item_name' => __('Новая отрасль'),
            'menu_name' => __('Отрасли'),
            'not_found' => __('Отраслей не найдено'),
        ],
    ]);
    register_taxonomy('client_category', ['client'], [
        'hierarchical' => false,
        'label' => 'Отображать на',
        'labels' => [
            'name' => _x('Отображать на', 'taxonomy general name'),
            'singular_name' => _x('Отображать на', 'taxonomy singular name'),
            'search_items' => __('Поиск отметок'),
            'all_items' => __('Все отметки'),
            'parent_item' => __('Parent'),
            'parent_item_colon' => __('Parent:'),
            'edit_item' => __('Редактировать отметку'),
            'update_item' => __('Обновить отметку'),
            'add_new_item' => __('Добавить новую отметку'),
            'new_item_name' => __('Новая отметка'),
            'menu_name' => __('Отображение'),
            'not_found' => __('Не найдено'),
        ],
    ]);
}

add_action('init', static function (): void {
    foreach (ug_post_types() as $type => $args) {
        register_post_type($type, $args);
        if ($type === 'client') {
            ug_register_client_taxonomies();
            new client_shortcode_options();
        }
        if (in_array($type, UG_UX_BUILDER_TYPES, true) && function_exists('add_ux_builder_post_type')) {
            add_ux_builder_post_type($type);
        }
    }
});

/**
 * /client/, /client/<slug>/, /client/feed/ … → 301 на /about/clients/.
 * После publicly_queryable => false WP отдал бы на них главную с кодом 200.
 */
add_action('parse_request', static function (WP $wp): void {
    // wp-admin/edit.php?post_type=client тоже проходит через wp() → parse_request.
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    if (preg_match('#^client(/|$)#', $wp->request) || ($_GET['post_type'] ?? '') === 'client') {
        wp_redirect(home_url('/about/clients/'), 301);
        exit;
    }
});

/* ============================================================
 *  Мета-боксы
 * ============================================================ */

/** Направления в выпадающих списках (у каждого типа свой набор, как было). */
const UG_BRANCHES_SPECIALIST = ['-', 'Бухгалтерия', 'Аудит', 'Юридические услуги', 'Лицензирование', 'Регистрация и ликвидация', 'Сделки с недвижимостью'];
const UG_BRANCHES_FEEDBACK   = ['-', 'Бухгалтерия', 'Аудит', 'Юридические услуги', 'Лицензирование', 'Регистрация', 'Ликвидация'];
const UG_BRANCHES_FAQ        = ['-', 'Бухгалтерия', 'Аудит', 'Юридические услуги', 'Лицензирование', 'Регистрация и ликвидация', 'Интеллектуальные права'];
const UG_YES_NO              = ['Нет', 'Да'];

/**
 * Схема мета-боксов: тип => список боксов. Поле:
 *   type     — text | url | textarea | select (по умолчанию text);
 *   sanitize — text (sanitize_text_field) | url (esc_url_raw) | html (wp_kses_post)
 *              | lines (sanitize_textarea_field: текст по строкам, переводы строк целы);
 *              select проверяется по options. Где сейчас в БД лежит HTML или
 *              переводы строк (авторы с <br/>, описания), — html: wp_kses_post
 *              не меняет ни одного сохранённого значения (проверено по всем записям).
 */
function ug_meta_box_schema(): array
{
    return [
        'client' => [
            ['id' => 'umbrella_client_personal_industry_box', 'title' => __('Индивидуальная отрасль'), 'fields' => [
                'umbrella_client_personal_industry' => ['placeholder' => 'Например: Производство кабеля',
                    'description' => 'Заполняется, если стандартной таксономии «Отрасль» недостаточно.'],
            ]],
            ['id' => 'umbrella_link_to_client_website_box', 'title' => __('Ссылка на сайт клиента'), 'fields' => [
                'umbrella_link_to_client_website' => ['type' => 'url', 'sanitize' => 'url', 'placeholder' => 'https://example.com',
                    'description' => 'Адрес внешнего сайта клиента. Используется, когда у клиента нет отзыва.'],
            ]],
            ['id' => 'umbrella_feedback_scan_box', 'title' => __('Скан отзыва (ID вложения)'), 'fields' => [
                'umbrella_feedback_scan' => ['placeholder' => 'ID изображения из медиатеки',
                    'description' => 'ID вложения (изображения) скана отзыва. ID можно увидеть в URL медиафайла в админке.'],
            ]],
            // Таблетки услуг на /about/clients/ ([ug_client_tiles], ug_client_tiles_service_list)
            ['id' => 'umbrella_client_services_box', 'title' => __('Услуги на плитке клиента'), 'fields' => [
                'umbrella_client_services' => ['type' => 'textarea', 'sanitize' => 'lines',
                    'placeholder' => "Юридическое обслуживание | /services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy/\nБухгалтерское обслуживание | /services/bukhgalterskie-uslugi/obsluzhivanie/",
                    'description' => 'По одной услуге на строку: Название | ссылка. Без ссылки — просто текст; «Название | #form» — кнопка, открывающая форму заявки (как «Обсудить задачу»). Пусто — услуги из рубрик «Отображать на».'],
            ]],
        ],
        'akcii' => [
            ['id' => 'akcii_form_metabox', 'title' => 'Контактная форма и блок', 'fields' => [
                'akcii_form_header' => ['label' => 'Заголовок формы'],
                'akcii_form_subheader' => ['label' => 'Подзаголовок формы'],
                'akcii_form_button_text' => ['label' => 'Текст кнопки формы'],
                'akcii_block_icon' => ['label' => 'Иконка блока'],
            ]],
        ],
        'faq' => [
            ['id' => 'faq_form_metabox', 'title' => 'Контактная форма и блок', 'fields' => [
                'faq_branch' => ['label' => 'Направление', 'type' => 'select', 'options' => UG_BRANCHES_FAQ],
                'faq_author' => ['label' => 'Автор', 'type' => 'textarea'],
            ]],
        ],
        'specialist' => [
            ['id' => 'specialist_form_metabox', 'title' => 'Данные', 'fields' => [
                'specialist_branch' => ['label' => 'Направление', 'type' => 'select', 'options' => UG_BRANCHES_SPECIALIST],
                'specialist_name' => ['label' => 'Имя Отчество'],
                'specialist_position' => ['label' => 'Должность'],
                'specialist_experience' => ['label' => 'Опыт'],
                'specialist_description' => ['label' => 'Описание', 'type' => 'textarea', 'sanitize' => 'html'],
                'specialist_education' => ['label' => 'Образование', 'sanitize' => 'html'],
                'specialist_specialization' => ['label' => 'Специализация'],
                'specialist_achievements' => ['label' => 'Достижения', 'sanitize' => 'html'],
                'specialist_office' => ['label' => 'Офис'],
                'specialist_main' => ['label' => 'Ведущий', 'type' => 'select', 'options' => UG_YES_NO],
                // Страница «Команда» ([team_page]): без них — изображение записи.
                'specialist_photo_card' => ['label' => 'Фото для карточки', 'description' => 'ID картинки 800 × 500 из медиатеки — карусель на странице «Команда»'],
                'specialist_photo_full' => ['label' => 'Фото для окна «Подробнее»', 'description' => 'ID картинки 1200 × 1600 из медиатеки'],
                'specialist_team_tags' => ['label' => 'Теги на «Команде»', 'description' => 'Через запятую; без них — направление'],
                'specialist_team_tabs' => ['label' => 'Доп. вкладки на «Команде»', 'description' => 'Названия вкладок через запятую, например: Налоговое право'],
                'specialist_team_only' => ['label' => 'Только для страницы «Команда»', 'type' => 'select', 'options' => ['', 'Да'], 'description' => '«Да» — не показывать в блоках специалистов на страницах услуг; должность на «Команде» — из поля «Должность»'],
            ]],
        ],
        'case' => [
            ['id' => 'case_form_metabox', 'title' => 'Данные', 'fields' => [
                'case_branch' => ['label' => 'Направление', 'type' => 'select', 'options' => UG_BRANCHES_FEEDBACK],
                'case_category' => ['label' => 'Категория', 'sanitize' => 'html'],
                'case_logo_url' => ['label' => 'Ссылка на лого автора / компании клиента', 'sanitize' => 'html'],
                'case_author' => ['label' => 'Компания и автор отзыва', 'sanitize' => 'html'],
                'case_industry' => ['label' => 'Отрасль', 'sanitize' => 'html'],
                'case_team' => ['label' => 'Сотрудники', 'sanitize' => 'html'],
                'case_issue' => ['label' => 'Проблема', 'type' => 'textarea', 'sanitize' => 'html'],
                'case_solution' => ['label' => 'Решение', 'sanitize' => 'html'],
                'case_proof' => ['label' => 'Вещдок', 'sanitize' => 'html'],
                'case_proof_title' => ['label' => 'Подпись вещдока', 'sanitize' => 'html'],
                'case_feedback_text' => ['label' => 'Текст ссылки отзыва'],
                'case_feedback_url' => ['label' => 'Ссылка на отзыв', 'sanitize' => 'html'],
                'case_common' => ['label' => 'Для общего', 'type' => 'select', 'options' => UG_YES_NO],
                // Окно «Подробнее о кейсе» на /about/cases/: без «Что мы сделали» — короткое окно
                'case_service' => ['label' => 'Услуга', 'description' => 'Строка «Услуга» в окне «Подробнее» на странице «Кейсы», например: Аудиторская проверка'],
                'case_steps' => ['label' => 'Что мы сделали', 'type' => 'textarea', 'sanitize' => 'html', 'description' => 'Ровно 3 шага, по шагу в строке'],
                'case_outcome' => ['label' => 'Итог', 'type' => 'textarea', 'sanitize' => 'html', 'description' => 'Одна фраза для плашки в окне «Подробнее»'],
            ]],
        ],
        'feedback' => [
            ['id' => 'feedback_form_metabox', 'title' => 'Данные', 'fields' => [
                'feedback_branch' => ['label' => 'Направление', 'type' => 'select', 'options' => UG_BRANCHES_FEEDBACK],
                'feedback_category' => ['label' => 'Категория'],
                'feedback_logo_url' => ['label' => 'Ссылка на лого автора / компании клиента', 'sanitize' => 'html'],
                'feedback_author' => ['label' => 'Компания и автор отзыва', 'sanitize' => 'html'],
                'feedback_quote' => ['label' => 'Цитата', 'type' => 'textarea', 'sanitize' => 'html'],
                'feedback_proof' => ['label' => 'Картинка-вещдок / отзыв на бланке'],
                'feedback_proof_title' => ['label' => 'Подпись вещдока/картинки', 'sanitize' => 'html'],
                'feedback_url' => ['label' => 'Ссылка на Фламп/Яндекс', 'sanitize' => 'url'],
                'feedback_url_text' => ['label' => 'Текст ссылки отзыва'],
                'feedback_common' => ['label' => 'Для общего', 'type' => 'select', 'options' => UG_YES_NO],
            ]],
        ],
    ];
}

/**
 * Мета-бокс по схеме: вывод на add_meta_boxes, сохранение на save_post_{type}.
 * $boxes — список боксов одного типа (см. ug_meta_box_schema()).
 */
function ug_register_meta_box(string $post_type, array $boxes): void
{
    $nonce_action = 'ug_save_meta_' . $post_type;
    $nonce_name   = 'ug_meta_nonce_' . $post_type;

    add_action('add_meta_boxes_' . $post_type, static function () use ($post_type, $boxes, $nonce_action, $nonce_name): void {
        foreach ($boxes as $i => $box) {
            add_meta_box($box['id'], $box['title'], static function (WP_Post $post) use ($box, $i, $nonce_action, $nonce_name): void {
                if ($i === 0) {
                    wp_nonce_field($nonce_action, $nonce_name);
                }
                ug_render_meta_fields($post, $box['fields']);
            }, $post_type, 'side', 'default');
        }
    });

    add_action('save_post_' . $post_type, static function (int $post_id) use ($boxes, $nonce_action, $nonce_name): void {
        if (!isset($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$nonce_name])), $nonce_action)) {
            return;
        }
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
            return;
        }
        foreach ($boxes as $box) {
            foreach ($box['fields'] as $key => $field) {
                if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
                    continue;
                }
                $raw = wp_unslash($_POST[$key]);
                if ($raw === (string) get_post_meta($post_id, $key, true)) {
                    continue; // не меняли — не трогаем сохранённое значение
                }
                $clean = ug_sanitize_meta_field($raw, $field);
                if ($clean === null) {
                    continue;
                }
                update_post_meta($post_id, $key, $clean);
            }
        }
    });
}

/** Санитайзер поля по схеме; null — значение не принимается (нет в options). */
function ug_sanitize_meta_field(string $raw, array $field): ?string
{
    if (($field['type'] ?? 'text') === 'select') {
        return in_array($raw, $field['options'], true) ? $raw : null;
    }
    return match ($field['sanitize'] ?? 'text') {
        'url'  => esc_url_raw(trim($raw)),
        'html' => wp_kses_post($raw),
        'lines' => sanitize_textarea_field($raw),
        default => sanitize_text_field($raw),
    };
}

function ug_render_meta_fields(WP_Post $post, array $fields): void
{
    echo '<div class="hcf_box" style="display:grid;grid-row-gap:10px;grid-column-gap:20px;">';
    foreach ($fields as $key => $field) {
        $value = (string) get_post_meta($post->ID, $key, true);
        $type  = $field['type'] ?? 'text';
        $id    = esc_attr($key);
        echo '<div class="meta-options hcf_field" style="display:contents;">';
        if (isset($field['label'])) {
            echo '<label for="' . $id . '">' . esc_html($field['label']) . '</label>';
        }
        if ($type === 'select') {
            echo '<select id="' . $id . '" name="' . $id . '">';
            foreach ($field['options'] as $option) {
                echo '<option value="' . esc_attr($option) . '"' . selected($value, $option, false) . '>' . esc_html($option) . '</option>';
            }
            echo '</select>';
        } elseif ($type === 'textarea') {
            echo '<textarea id="' . $id . '" name="' . $id . '" rows="4" style="width:100%;"'
                . (isset($field['placeholder']) ? ' placeholder="' . esc_attr($field['placeholder']) . '"' : '') . '>' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input id="' . $id . '" type="' . ($type === 'url' ? 'url' : 'text') . '" name="' . $id . '" value="' . esc_attr($value) . '" style="width:100%;"'
                . (isset($field['placeholder']) ? ' placeholder="' . esc_attr($field['placeholder']) . '"' : '') . '>';
        }
        if (isset($field['description'])) {
            echo '<p class="description">' . esc_html($field['description']) . '</p>';
        }
        echo '</div>';
    }
    echo '</div>';
}

foreach (ug_meta_box_schema() as $ug_type => $ug_boxes) {
    ug_register_meta_box($ug_type, $ug_boxes);
}
unset($ug_type, $ug_boxes);

/* ============================================================
 *  Колонки списка записей в админке
 * ============================================================ */

/**
 * Тип => [колонка => заголовок]. Колонка — ключ меты или таксономия;
 * особые: is_feedback (есть ли отрывок), umbrella_link_to_client_website (ссылка).
 */
function ug_admin_columns(): array
{
    return [
        'client' => [
            'client_industry' => 'Отрасль',
            'client_category' => 'Отображать на',
            'umbrella_client_personal_industry' => 'Индивидуальная отрасль',
            'umbrella_link_to_client_website' => 'Сайт',
            'is_feedback' => 'Отзыв',
            'umbrella_feedback_scan' => 'Скан отзыва',
        ],
        'akcii' => [
            'akcii_form_header' => 'Заголовок формы',
            'akcii_form_subheader' => 'Подзаголовок формы',
            'akcii_form_button_text' => 'Кнопка формы',
            'akcii_block_icon' => 'Иконка блока',
        ],
        'faq' => ['faq_branch' => 'Направление', 'faq_author' => 'Автор'],
        'specialist' => ['specialist_branch' => 'Направление', 'specialist_name' => 'Имя Отчество'],
        'case' => ['case_branch' => 'Направление', 'case_category' => 'Категория'],
        'feedback' => ['feedback_branch' => 'Направление', 'feedback_category' => 'Категория'],
    ];
}

foreach (ug_admin_columns() as $ug_type => $ug_columns) {
    add_filter('manage_' . $ug_type . '_posts_columns', static function (array $columns) use ($ug_columns): array {
        unset($columns['date']);
        return $columns + $ug_columns;
    });
    add_action('manage_' . $ug_type . '_posts_custom_column', static function (string $column, int $post_id) use ($ug_columns): void {
        if (!isset($ug_columns[$column])) {
            return;
        }
        if (taxonomy_exists($column)) {
            $terms = get_the_term_list($post_id, $column, '', ',', '');
            echo is_string($terms) ? $terms : '';
        } elseif ($column === 'is_feedback') {
            echo get_the_excerpt($post_id) ? 'есть' : '';
        } elseif ($column === 'umbrella_link_to_client_website') {
            $url = get_post_meta($post_id, $column, true);
            if (!empty($url)) {
                echo '<a href="' . esc_url($url) . '" target="_blank">перейти на сайт </a>';
            }
        } else {
            echo esc_html((string) get_post_meta($post_id, $column, true));
        }
    }, 10, 2);
}
unset($ug_type, $ug_columns);

/* ============================================================
 *  Настройки шорткода [umbrella_feedback] (подменю «Клиенты → Шорткод»)
 * ============================================================ */

/**
 * Опция client_shortcode_options['excluded_pages'] — ID страниц, где
 * [umbrella_feedback] не выводится (читает template-parts/shortcodes/umbrella_feedback.php).
 */
class client_shortcode_options
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_submenu_page_to_post_type']);
        add_action('admin_init', [$this, 'sub_menu_page_init']);
    }

    public function add_submenu_page_to_post_type(): void
    {
        add_submenu_page(
            'edit.php?post_type=client',
            'Опции шорткода',
            'Шорткод',
            'manage_options',
            'projects_archive',
            [$this, 'client_options_display']
        );
    }

    public function client_options_display(): void
    {
        echo '<div class="wrap">';
        printf('<h1>%s</h1>', 'Настройки отзывов');
        echo '<form method="post" action="options.php">';
        settings_fields('projects_archive');
        do_settings_sections('client_shortcode_options_page');
        submit_button();
        echo '</form></div>';
    }

    public function sub_menu_page_init(): void
    {
        register_setting('projects_archive', 'client_shortcode_options', [
            'sanitize_callback' => [$this, 'sanitize'],
        ]);

        add_settings_section(
            'header_settings_section',
            'Опции шорткода [umbrella_feedback]',
            '__return_null',
            'client_shortcode_options_page'
        );

        add_settings_field(
            'excluded_pages',
            'Исключить вывод шорткода на',
            [$this, 'excluded_pages_callback'],
            'client_shortcode_options_page',
            'header_settings_section'
        );
    }

    /** ID страниц строками, как они лежат в опции сейчас. */
    public function sanitize($input): array
    {
        $ids = is_array($input) && isset($input['excluded_pages']) ? (array) $input['excluded_pages'] : [];
        $ids = array_values(array_filter(array_map(static fn($id): string => (string) absint($id), $ids)));
        return ['excluded_pages' => $ids];
    }

    public function excluded_pages_callback(): void
    {
        $options  = get_option('client_shortcode_options');
        $excluded = is_array($options) && isset($options['excluded_pages']) ? array_map('strval', (array) $options['excluded_pages']) : [];

        echo '<select multiple="multiple" size="20" class="client_shortcode_options" name="client_shortcode_options[excluded_pages][]">';
        foreach (get_pages() as $page) {
            $id = (string) $page->ID;
            echo '<option value="' . esc_attr($id) . '"' . (in_array($id, $excluded, true) ? ' selected' : '') . '>' . esc_html($page->post_title) . '</option>';
        }
        echo '</select>';
    }
}
