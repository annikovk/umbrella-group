<?php

class cases
{
    private $css_files = ['/assets/css/blocks/cases.css', '/assets/css/blocks/cases-and-feedback.css'];
    private $js_files = ['/assets/js/blocks/cases.js'];
    private $category = "";
    private $titles = [
        "Бухгалтерия" => ["title" => "Кейсы: проблемы с бухгалтерией и их решения"],
        "Аудит" => ["title" => "Кейсы об аудиторской проверке"],
        "Юридические услуги" => ["title" => "Кейсы: юридическая практика"],
        "Лицензирование" => ["title" => "Кейсы лицензирования"],
        "Регистрация" => ["title" => "Кейсы: истории регистрации наших клиентов"],
        "Ликвидация" => ["title" => "Кейсы: истории о закрытии компаний"],
        "Общий" => ["title" => "Кейсы: истории о клиентах"],
        "cases" => ["title" => ""],
    ];
    public $atts;
    public $err;
    private $all_metas = [];
    private $is_cases_category_page;

    // Доп. фильтры и режим
    private $filter_category = "";   // значение category="..."
    private $filter_ids = [];        // массив ID из ids="..."
    private $is_flat_mode = false;   // без табов, но со слайдером

    public function fill_attributes()
    {
        // Базовое направление (branch) — как раньше
        if (isset($this->atts['branch'])) {
            $this->category = $this->atts['branch'];
        } else {
            $this->category = $this->get_category_by_url();
        }

        // Доп. фильтр по конкретной категории
        if (isset($this->atts['category']) && trim($this->atts['category']) !== "") {
            $this->filter_category = trim($this->atts['category']);
        }

        // Доп. выборка по ID
        if (isset($this->atts['ids']) && trim($this->atts['ids']) !== "") {
            $ids = array_map('intval', explode(',', $this->atts['ids']));
            $this->filter_ids = array_values(array_filter($ids, function ($id) {
                return $id > 0;
            }));
        }

        // Плоский режим (без табов, со слайдером): задан category ИЛИ ids
        $this->is_flat_mode = ($this->filter_category !== "" || !empty($this->filter_ids));

        return true;
    }

    public function generate_shortcode()
    {
        $this->is_cases_category_page = $this->category == "cases";

        // В плоском режиме заголовок по branch не выводим
        $title = $this->is_flat_mode ? "" : $this->get_title($this->category);

        $posts = $this->get_cases_posts($this->category);

        // /about/cases/ — своя вёрстка: первый экран, вкладки, карточки по 3 на страницу
        if ($this->is_cases_category_page) {
            return $this->generate_page($posts);
        }

        // ===== ПЛОСКИЙ РЕЖИМ: без табов, один слайдер =====
        if ($this->is_flat_mode) {
            $posts_tiles = "";
            $posts_count = 0;
            foreach ($posts as $post) {
                $posts_tiles .= $this->get_tile($post);
                $posts_count += 1;
            }

            if ($posts_count < 2) {
                $tiles = "<div class='cases_flat'>" . $posts_tiles . "</div>";
            } else {
                $tiles = '[ux_slider auto_slide="false" draggable="true" hide_nav="true" nav_style="simple" bullet_style="square" class="cases_flat"]'
                    . $posts_tiles
                    . '[/ux_slider]';
            }

            $html = <<<EOHTML
            [section id='umbrella-cases'  padding="0px" class="umbrella-cases-section"]
                [row]
                    [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                        <div class="newcases">
                            $title
                            <div class="content">
                                <div class="tiles" style="padding:0 20px;">$tiles</div>
                            </div>
                        </div>
                    [/col]
                [/row]
            [/section]
            EOHTML;

            foreach ($this->css_files as $rel) {
                ug_enqueue_style($rel);
            }
            foreach ($this->js_files as $rel) {
                ug_enqueue_script($rel, ['jquery']);
            }
            return $html;
        }

        // ===== СТАРАЯ ЛОГИКА: табы + слайдеры =====
        foreach ($posts as $post) {
            $this->concat_metas($post);
        }
        $tabs = $this->get_tabs();
        $tiles = "";
        foreach ($this->all_metas as $meta) {
            $visible = ($this->all_metas[0] == $meta) ? "" : "invisible";
            $posts_tiles = "";
            $posts_count = 0;
            foreach ($posts as $post) {
                if (in_array($meta, $this->getPostmeta($post))) {
                    $posts_tiles .= $this->get_tile($post);
                    $posts_count += 1;
                }
            }
            $tiles .= ($this->is_cases_category_page || $posts_count < 2) ? "<div class='cases_" . transliterate($meta) . ' ' . $visible . "'>" : '[ux_slider auto_slide="false" draggable="true" hide_nav="true" nav_style="simple" bullet_style="square" class="cases_' . transliterate($meta) . ' ' . $visible . '"]';
            $tiles .= $posts_tiles;
            $tiles .= ($this->is_cases_category_page || $posts_count < 2) ? "</div>" : "[/ux_slider]";
        }
        $html = <<<EOHTML
        [section id='umbrella-cases'  padding="0px" class="umbrella-cases-section"]
            [row]
                [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
                    <div class="newcases">
                        $title
                        <div class="content">
                            $tabs
                            <div class="tiles" style="padding:0 20px;">$tiles</div>
                        </div>
                    </div>
                [/col]
            [/row]
        [/section]
        EOHTML;
        foreach ($this->css_files as $rel) {
            ug_enqueue_style($rel);
        }
        foreach ($this->js_files as $rel) {
            ug_enqueue_script($rel, ['jquery']);
        }
        return $html;
    }

    /**
     * Вкладки /about/cases/ по порядку, и в нём же идут «Все кейсы». Вкладка —
     * не case_branch: по нему кейсы выбирают блоки на страницах услуг, поэтому
     * направления сводятся во вкладки здесь, см. page_tab().
     * Вкладка => [ID раздела услуг для ссылки под кейсом, подпись ссылки].
     */
    private const PAGE_TABS = [
        'Аудит' => [34, 'Услуги по аудиту'],
        'Бухгалтерия' => [26, 'Услуги по бухгалтерии'],
        'Корпоративное право' => [30, 'Услуги по корпоративному праву'],
        'Лицензирование' => [28, 'Услуги по лицензированию'],
        'Интеллектуальное право' => [29220, 'Услуги по товарным знакам'],
        'Налоговое право' => [30352, 'Услуги по налоговому праву'],
        'Юридические услуги' => [32, 'Юридические услуги'],
    ];

    /**
     * «Налоговое право»: кейсы по налогам, которые вели специалисты налогового
     * права (Анников, Демиденко, Кислицына). Отдельного направления у кейсов
     * нет — список ведётся здесь.
     */
    private const PAGE_TAX_IDS = [13027, 14749, 14826, 14827];

    /**
     * Кейс видно ещё в одной вкладке, кроме своей; в «Все кейсы» он один раз,
     * в своей вкладке. 12697 — налоги транспортной компании: бухгалтерия вместе
     * с Демиденко.
     */
    private const PAGE_EXTRA_TABS = [12697 => 'Налоговое право'];

    /** Вкладка кейса: регистрация и ликвидация — корпоративное право, товарные знаки — интеллектуальное. */
    private static function page_tab(int $id, string $branch, string $category): string
    {
        if (in_array($id, self::PAGE_TAX_IDS, true)) {
            return 'Налоговое право';
        }
        if (trim($category) === 'Товарный знак') {
            return 'Интеллектуальное право';
        }
        if ($branch === 'Регистрация' || $branch === 'Ликвидация') {
            return 'Корпоративное право';
        }
        return $branch;
    }

    /**
     * Расширенное окно «Подробнее»: строится, если у кейса заполнено «Что мы
     * сделали» (case_steps, по шагу в строке), плюс case_service и case_outcome.
     * «Похожая задача?» — общий текст вкладки: [подпись кнопки, текст после
     * «Этот кейс был решён в рамках услуги «…».»]; ссылка — раздел из PAGE_TABS.
     */
    private const PAGE_SIMILAR = [
        'Аудит' => ['Подробнее об услуге «Аудит»', 'Помогаем выявлять риски, приводить учет в порядок и избегать доначислений.'],
        'Бухгалтерия' => ['Подробнее об услуге «Бухгалтерия»', 'Ведем учет и отчетность, помогаем снизить налоговую нагрузку и избежать штрафов.'],
        'Корпоративное право' => ['Подробнее об услуге «Корпоративное право»', 'Регистрируем, меняем и закрываем компании без лишних визитов и рисков.'],
        'Лицензирование' => ['Подробнее об услуге «Лицензирование»', 'Готовим документы, сопровождаем проверки и получаем лицензии под ключ.'],
        'Интеллектуальное право' => ['Подробнее об услуге «Интеллектуальное право»', 'Проверяем обозначения, регистрируем товарные знаки и защищаем бренд.'],
        'Налоговое право' => ['Подробнее об услуге «Налоговое право»', 'Оспариваем доначисления, сопровождаем проверки и защищаем бизнес в спорах с налоговой.'],
        'Юридические услуги' => ['Подробнее об услуге «Юридические услуги»', 'Защищаем интересы бизнеса в суде и сопровождаем проекты.'],
    ];

    /** Картинка в плашке итога расширенного окна: путь от корня сайта, пусто — заглушка. */
    private const PAGE_OUTCOME_IMAGE = '';

    /** Иконки карточек — содержимое svg 24 × 24, обводка. */
    private const PAGE_ICONS = [
        'case' => '<rect x="3.75" y="7.25" width="16.5" height="12" rx="1.75"/><path d="M9 7.25V5.5c0-.7.55-1.25 1.25-1.25h3.5c.7 0 1.25.55 1.25 1.25v1.75M3.75 12.25h16.5" stroke-linejoin="round"/>',
        'user' => '<circle cx="12" cy="8.25" r="3.75"/><path d="M5 19.75c0-3.6 3.1-6 7-6s7 2.4 7 6" stroke-linecap="round"/>',
        'tag' => '<path d="M4.75 4.75h6.6l8.15 8.15-6.6 6.6-8.15-8.15V4.75Z" stroke-linejoin="round"/><circle cx="8.5" cy="8.5" r="1.25"/>',
        'alert' => '<circle cx="12" cy="12" r="8.75"/><path d="M12 7.5v5.5" stroke-linecap="round"/><circle cx="12" cy="16.25" r=".6" fill="currentColor"/>',
        'check' => '<circle cx="12" cy="12" r="8.75"/><path d="m8.25 12.25 2.5 2.5 5-5" stroke-linecap="round" stroke-linejoin="round"/>',
        'arrow' => '<path d="M4.5 12h15M14 6.5l5.5 5.5-5.5 5.5" stroke-linecap="round" stroke-linejoin="round"/>',
        'doc' => '<path d="M14 3.75H7c-.7 0-1.25.55-1.25 1.25v14c0 .7.55 1.25 1.25 1.25h10c.7 0 1.25-.55 1.25-1.25V8L14 3.75Z" stroke-linejoin="round"/><path d="M13.75 4v4.25H18M9 12.5h6M9 16h6" stroke-linecap="round" stroke-linejoin="round"/>',
        'gear' => '<circle cx="12" cy="12" r="2.75"/><path d="M10.4 3.75h3.2l.45 2.2 1.5.85 2.1-.75 1.6 2.75-1.7 1.5v1.4l1.7 1.5-1.6 2.75-2.1-.75-1.5.85-.45 2.2h-3.2l-.45-2.2-1.5-.85-2.1.75-1.6-2.75 1.7-1.5v-1.4L4.75 8.8l1.6-2.75 2.1.75 1.5-.85.45-2.2Z" stroke-linejoin="round"/>',
        'bars' => '<path d="M6 19.25v-5M10 19.25v-9M14 19.25v-6M18 19.25V5.75" stroke-width="2" stroke-linecap="round"/>',
    ];

    private static function page_icon(string $name, int $size): string
    {
        return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">' . self::PAGE_ICONS[$name] . '</svg>';
    }

    /**
     * /about/cases/: первый экран, вкладки направлений («Все кейсы» первой) и
     * карточки. Все кейсы выводятся сразу, фильтр по вкладке и разбивку по 3 на
     * страницу делает cases_page.js; #Yuridicheskie_uslugi в адресе открывает вкладку.
     */
    private function generate_page(array $posts): string
    {
        // Кейсы по вкладкам в порядке PAGE_TABS, внутри вкладки — menu_order;
        // направление без вкладки в PAGE_TABS — в конец
        $groups = array_fill_keys(array_keys(self::PAGE_TABS), []);
        foreach ($posts as $post) {
            $tab = self::page_tab(
                $post->ID,
                (string) get_post_meta($post->ID, 'case_branch', true),
                (string) get_post_meta($post->ID, 'case_category', true)
            );
            if ($tab !== "") {
                $groups[$tab][] = $post;
            }
        }
        $groups = array_filter($groups);

        // Внутри вкладки сначала кейсы, где есть сотрудник с карточкой специалиста
        // (работает сейчас), потом остальные; usort устойчив — menu_order сохраняется
        foreach ($groups as $tab => $tab_posts) {
            $current = [];
            foreach ($tab_posts as $post) {
                $current[$post->ID] = (bool) array_filter(array_column(self::page_team((string) get_post_meta($post->ID, 'case_team', true)), 2));
            }
            usort($groups[$tab], static fn($a, $b) => $current[$b->ID] <=> $current[$a->ID]);
        }

        $cards = "";
        foreach ($groups as $tab => $tab_posts) {
            foreach ($tab_posts as $post) {
                $cards .= $this->get_page_card($post, $tab);
            }
        }
        // Вкладка, где только кейсы из PAGE_EXTRA_TABS, тоже нужна
        foreach (self::PAGE_EXTRA_TABS as $extra) {
            $groups += [$extra => []];
        }
        $groups = array_replace(array_intersect_key(self::PAGE_TABS, $groups), $groups);

        $tabs = '<button type="button" class="cases-page__tab is-active" data-branch="all">Все кейсы</button>';
        foreach (array_keys($groups) as $branch) {
            $tabs .= '<button type="button" class="cases-page__tab" data-branch="' . esc_attr(transliterate($branch)) . '">' . esc_html($branch) . '</button>';
        }

        $spec_tpls = '';
        foreach (array_keys(self::$page_used_specialists) as $id) {
            $spec_tpls .= self::page_specialist_tpl($id);
        }

        // team_page.css — типографика карточки специалиста (team-modal__*, team-leads__tag)
        ug_enqueue_style('assets/css/blocks/team_page.css');
        ug_enqueue_style('assets/css/blocks/cases_page.css');
        ug_enqueue_script('assets/js/blocks/cases_page.js');

        $hero = ug_cases_hero();
        $cta = self::page_cta();
        return <<<EOHTML
        $hero
        <section class="cases-page">
            <div class="cases-page__inner">
                <div class="cases-page__tabs" role="tablist">$tabs</div>
                <div class="cases-page__list">$cards</div>
                <nav class="cases-page__pager" aria-label="Страницы кейсов"></nav>
                $cta
            </div>
            $spec_tpls
            <dialog class="case-modal">
                <button type="button" class="case-modal__close" aria-label="Закрыть">×</button>
                <div class="case-modal__body" tabindex="-1"></div>
            </dialog>
        </section>
        EOHTML;
    }

    /**
     * Тёмный баннер под кейсами: кнопка открывает форму заявки из шапки.
     * Картинка справа — UG_CASES_CTA_IMAGE, пока пусто — заглушка с размером.
     */
    private static function page_cta(): string
    {
        $image = UG_CASES_CTA_IMAGE
            ? '<img src="' . esc_url(home_url(UG_CASES_CTA_IMAGE)) . '" alt="">'
            : '<span class="cases-cta__stub">Картинка 1400&nbsp;×&nbsp;600</span>';
        $arrow = self::page_icon('arrow', 20);
        return <<<EOHTML
        <div class="cases-cta">
            <div class="cases-cta__image">$image</div>
            <div class="cases-cta__text">
                <p class="cases-cta__eyebrow">Разберём вашу ситуацию</p>
                <h2 class="cases-cta__title">Нужен сильный результат в&nbsp;вашей компании?</h2>
                <p class="cases-cta__desc">Оставьте заявку — подберём решение под вашу задачу.</p>
                <a href="#header-contact-form-lightbox" class="cases-cta__btn">Заказать консультацию$arrow</a>
            </div>
        </div>
        EOHTML;
    }

    /**
     * Опубликованные карточки специалистов: фамилия => ID. Фамилия — первое
     * слово заголовка записи; в дублях (Вящикова) берётся первая по ID.
     */
    private static function page_specialists(): array
    {
        static $map = null;
        if ($map === null) {
            $map = [];
            $team = self::page_team_people();
            $posts = get_posts(['post_type' => 'specialist', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC']);
            foreach ($posts as $p) {
                $surname = strtok(trim($p->post_title), ' ');
                if ($surname === false) {
                    continue;
                }
                $surname = self::page_surname($surname);
                // Дубль (Вящикова заведена дважды) — та запись, что есть на «Команде»
                if (!isset($map[$surname]) || (!isset($team[$map[$surname]]) && isset($team[$p->ID]))) {
                    $map[$surname] = $p->ID;
                }
            }
            foreach (array_keys($team) as $key) {
                if (is_string($key)) {
                    $map[substr($key, 7)] ??= $key;
                }
            }
        }
        return $map;
    }

    /**
     * Блок «Кейс провела / провёл» для окна «Подробнее»: первый из «Сотрудников»
     * кейса («Должность: Имя Фамилия; …»), у кого есть карточка специалиста.
     * Должность и имя — из кейса, фото и «Чем помогу» — из карточки.
     */
    /** Фамилия для сравнения: латиница-двойник → кириллица («Вящиковa» с латинской a), ё → е. */
    private static function page_surname(string $word): string
    {
        return mb_strtolower(strtr($word, ['a' => 'а', 'e' => 'е', 'o' => 'о', 'p' => 'р', 'c' => 'с', 'x' => 'х', 'y' => 'у', 'A' => 'А', 'E' => 'Е', 'O' => 'О', 'P' => 'Р', 'C' => 'С', 'X' => 'Х', 'ё' => 'е']));
    }

    /** «Должность: Имя Фамилия; …» → [[должность, имя, ID карточки или null], …]. */
    private static function page_team(string $team): array
    {
        $map = self::page_specialists();
        $out = [];
        foreach (array_filter(array_map('trim', explode(';', $team))) as $person) {
            [$position, $name] = array_pad(array_map('trim', explode(':', $person, 2)), 2, '');
            if ($name === '') {
                [$position, $name] = ['', $position];
            }
            $words = explode(' ', $name);
            $out[] = [$position, $name, $map[self::page_surname(end($words))] ?? null];
        }
        return $out;
    }

    /** Специалисты, чьи карточки нужны окнам кейсов на этой странице: ID => true. */
    private static array $page_used_specialists = [];

    /** Люди страницы «Команда» (ug_team_page_people) по ID записи specialist. */
    private static function page_team_people(): array
    {
        static $people = null;
        if ($people === null) {
            $people = [];
            foreach (function_exists('ug_team_page_people') ? ug_team_page_people() : [] as $p) {
                // Руководство без записи specialist (Климов, Батурина) — ключ leader-<фамилия>
                $words = explode(' ', trim($p['name']));
                $id = $p['post'] ?? $p['specialist'] ?? ('leader-' . self::page_surname(end($words)));
                if (!isset($people[$id])) {
                    $people[$id] = $p;
                }
            }
        }
        return $people;
    }

    /**
     * Карточка специалиста для окна кейса — та же, что окно «Подробнее» на
     * «Команде»: фото, имя, должность, теги, опыт, цитата, «Чем помогу», «Достижения».
     */
    private static function page_specialist_tpl(int|string $id): string
    {
        $p = self::page_team_people()[$id];
        $id_attr = esc_attr((string) $id);
        $src = !empty($p['img_full']) ? wp_get_attachment_image_url($p['img_full'], 'full') : '';
        if (!$src && !empty($p['img'])) {
            $src = wp_get_attachment_image_url($p['img'], 'medium_large');
        }
        $photo = $src ? '<img src="' . esc_url($src) . '" alt="' . esc_attr($p['name']) . '">' : '';
        $tags = '';
        foreach ($p['tags'] as $tag) {
            $tags .= '<span class="team-leads__tag">' . esc_html($tag) . '</span>';
        }
        $name = esc_html($p['name']);
        $title = esc_html($p['title']);
        $more = ug_team_page_more($p);
        $arrow = self::page_icon('arrow', 18);
        return <<<EOHTML
            <template class="case-spec-tpl" data-specialist="$id_attr">
                <div class="case-spec">
                    <button type="button" class="case-spec__back">← К&nbsp;кейсу</button>
                    <div class="case-spec__inner">
                        <div class="team-modal__photo case-spec__photo">$photo</div>
                        <div class="case-spec__body">
                            <div class="team-modal__name">$name</div>
                            <div class="team-modal__title">$title</div>
                            <div class="team-modal__tags">$tags</div>
                            <div class="team-modal__content">$more</div>
                            <a class="case-full__btn case-full__btn--primary" href="#header-contact-form-lightbox">Получить консультацию$arrow</a>
                        </div>
                    </div>
                </div>
            </template>
        EOHTML;
    }

    private static function page_specialist(string $team): string
    {
        // Сначала руководство (UG_TEAM_LEADERS — у них нет 'post'), дальше по порядку в кейсе
        $people = self::page_team_people();
        $candidates = array_values(array_filter(self::page_team($team), static fn($t) => $t[2] !== null));
        $is_leader = static fn($t) => isset($people[$t[2]]) && !isset($people[$t[2]]['post']);
        usort($candidates, static fn($a, $b) => $is_leader($b) <=> $is_leader($a));
        foreach ($candidates as [$position, $name, $id]) {
            $p = $people[$id] ?? null;
            if (is_string($id)) {
                // Руководитель без записи specialist: фото и «Чем помогу» — из UG_TEAM_LEADERS
                $photo_id = (int) ($p['img_full'] ?? 0) ?: (int) ($p['img'] ?? 0);
                $text = trim((string) ($p['help'] ?? ''));
                $words = explode(' ', trim($p['name']));
                $patronymic = count($words) === 3 ? $words[1] : '';
            } else {
                $photo_id = (int) get_post_meta($id, 'specialist_photo_full', true) ?: get_post_thumbnail_id($id);
                $text = trim(wp_strip_all_tags((string) get_post_meta($id, 'specialist_specialization', true)));
                $patronymic = (string) get_post_meta($id, 'specialist_name', true);
            }
            $photo = $photo_id ? wp_get_attachment_image($photo_id, 'medium', false, ['class' => 'case-full__person-img', 'alt' => esc_attr($name), 'loading' => 'lazy']) : '';
            $text = $text !== '' ? '<p>' . esc_html(mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1)) . '</p>' : '';
            // Род по отчеству («Сергеевна» / «Алексеевич»), без него — нейтрально
            $label = preg_match('/(вна|чна)\s*$/u', $patronymic) ? 'Кейс провела' : (preg_match('/ич\s*$/u', $patronymic) ? 'Кейс провёл' : 'Специалист по кейсу');
            $name_html = esc_html($name);
            // «Посмотреть специалиста» — карточка из «Команды» в этом же окне (page_specialist_tpl)
            $button = isset(self::page_team_people()[$id])
                ? '<button type="button" class="case-full__btn case-full__person-btn" data-specialist="' . esc_attr((string) $id) . '">Посмотреть специалиста' . self::page_icon('arrow', 18) . '</button>'
                : '';
            if ($button !== '') {
                self::$page_used_specialists[$id] = true;
            }
            $arrow = self::page_icon('arrow', 18);
            $pos = $position !== '' ? '<div class="case-full__person-pos">' . esc_html($position) . '</div>' : '';
            return <<<EOHTML
                <div class="case-full__person">
                    <div class="case-full__person-photo">$photo</div>
                    <div class="case-full__person-body">
                        <div class="case-full__person-label">$label</div>
                        <div class="case-full__person-name">$name_html</div>
                        $pos
                        $text
                        $button
                    </div>
                </div>
            EOHTML;
        }
        return '';
    }

    /** Расширенное окно «Подробнее»: $branch — вкладка кейса, page_tab(). */
    private static function page_details(string $branch, array $step_list, string $service, string $outcome, string $title, string $media, string $meta, string $story, string $extras, string $team): string
    {
        $person = self::page_specialist($team);
        $bottom_class = $person !== '' ? 'case-full__bottom case-full__bottom--person' : 'case-full__bottom';
        $service_name = esc_html($service);
        $outcome = esc_html($outcome);
        [$service_page] = self::PAGE_TABS[$branch] ?? [34];
        [$service_btn, $similar_text] = self::PAGE_SIMILAR[$branch] ?? self::PAGE_SIMILAR['Аудит'];
        $similar = ($service_name !== '' ? 'Этот кейс был решён в рамках услуги «' . $service_name . '». ' : '') . $similar_text;
        $service_line = $service_name !== '' ? '<div class="case-card__meta">' . self::page_icon('doc', 20) . '<div><b>Услуга:</b> ' . $service_name . '</div></div>' : '';
        $outcome_block = '';
        $i_gear = self::page_icon('gear', 28);
        $i_bars = self::page_icon('bars', 34);
        $i_arrow = self::page_icon('arrow', 18);
        $i_step = self::page_icon('arrow', 16);

        $steps = "";
        foreach (array_values($step_list) as $n => $text) {
            $steps .= '<li><span class="case-full__num">' . ($n + 1) . '</span><span>' . esc_html($text) . '</span>' . $i_step . '</li>';
        }
        $outcome_img = self::PAGE_OUTCOME_IMAGE
            ? '<img src="' . esc_url(home_url(self::PAGE_OUTCOME_IMAGE)) . '" alt="">'
            : '<span class="case-full__stub">Картинка 600&nbsp;×&nbsp;260</span>';
        $service_url = esc_url(get_permalink($service_page));
        if ($outcome !== '') {
            $outcome_block = <<<EOHTML
                <div class="case-full__outcome">
                    <span class="case-full__outcome-icon">$i_bars</span>
                    <p>$outcome</p>
                    <div class="case-full__outcome-image">$outcome_img</div>
                </div>
            EOHTML;
        }

        return <<<EOHTML
            <div class="case-full">
                <div class="case-full__top">
                    <div class="case-card__media case-full__media">$media</div>
                    <div class="case-full__head">
                        <h3 class="case-modal__title">$title</h3>
                        $meta
                        $service_line
                    </div>
                </div>
                <div class="case-full__story">$story</div>
                <div class="case-full__steps">
                    <h4>$i_gear Что мы сделали</h4>
                    <ol>$steps</ol>
                </div>
                $outcome_block
                $extras
                <div class="$bottom_class">
                    $person
                    <div class="case-full__cta">
                        <h4>Похожая задача?</h4>
                        <p>$similar</p>
                        <div class="case-full__buttons">
                            <a class="case-full__btn case-full__btn--primary" href="#header-contact-form-lightbox">Обсудить вашу задачу$i_arrow</a>
                            <a class="case-full__btn" href="$service_url">$service_btn$i_arrow</a>
                        </div>
                    </div>
                </div>
            </div>
        EOHTML;
    }

    /** Карточка кейса на /about/cases/ и её «Подробнее» в <template> для окна. */
    private function get_page_card($post, string $branch): string // $branch — вкладка, page_tab()
    {
        $meta = static fn(string $key): string => trim((string) get_post_meta($post->ID, $key, true));

        $title = esc_html(get_the_title($post));
        $category = esc_html($meta('case_category'));
        $industry = esc_html($meta('case_industry'));
        // «Должность: Имя Фамилия; Должность: …» — каждый сотрудник с новой строки
        $team = implode('<br>', array_map('esc_html', array_filter(array_map('trim', explode(';', $meta('case_team'))))));
        $issue = esc_html($meta('case_issue'));
        $solution = esc_html($meta('case_solution'));

        $media = has_post_thumbnail($post)
            ? get_the_post_thumbnail($post, 'medium_large', ['class' => 'case-card__img', 'alt' => ''])
            : '<span class="case-card__stub">Фото 600&nbsp;×&nbsp;700</span>';
        $tag = $category !== "" ? '<span class="case-card__tag">' . self::page_icon('tag', 16) . $category . '</span>' : '';

        $service = "";
        if (isset(self::PAGE_TABS[$branch])) {
            [$page_id, $label] = self::PAGE_TABS[$branch];
            $service = '<a class="case-card__service" href="' . esc_url(get_permalink($page_id)) . '">' . $label . self::page_icon('arrow', 16) . '</a>';
        }

        $i_case = self::page_icon('case', 20);
        $i_user = self::page_icon('user', 20);
        $i_alert = self::page_icon('alert', 26);
        $i_check = self::page_icon('check', 26);
        $i_arrow = self::page_icon('arrow', 18);
        $story = <<<EOHTML
            <div class="case-card__issue">$i_alert<div><h4>Проблема</h4><p>$issue</p></div></div>
            <div class="case-card__result">$i_check<div><h4>Результат</h4><p>$solution</p></div></div>
        EOHTML;

        // Окно «Подробнее»: всё, что есть у кейса, плюс клиент, документ и отзыв
        $company = "";
        $logo = $meta('case_logo_url');
        $author = $meta('case_author');
        // Без «Компании и автора» один логотип — чаще всего серая заглушка-аватар
        if ($author !== "") {
            $logo_img = $logo !== "" ? '<img src="' . esc_url($logo) . '" alt="" loading="lazy">' : '';
            // В «Компания и автор» бывает <br/> между компанией и именем — оставить переносом
            $company = '<div class="case-modal__company">' . $logo_img . '<span>' . wp_kses($author, ['br' => []]) . '</span></div>';
        }
        $proof = "";
        $proof_url = $meta('case_proof');
        if ($proof_url !== "") {
            $proof_title = esc_html($meta('case_proof_title'));
            $proof = '<a class="case-modal__proof" href="' . esc_url($proof_url) . '" target="_blank" rel="noopener"><img src="' . esc_url($proof_url) . '" alt="' . $proof_title . '" loading="lazy"><span>' . $proof_title . '</span></a>';
        }
        $feedback = "";
        $feedback_url = $meta('case_feedback_url');
        $feedback_text = $meta('case_feedback_text');
        if ($feedback_url !== "" && $feedback_text !== "") {
            $feedback = '<a class="case-modal__feedback" href="' . esc_url($feedback_url) . '" target="_blank" rel="noopener">' . esc_html($feedback_text) . $i_arrow . '</a>';
        }

        $i_case_meta = <<<EOHTML
                <div class="case-card__meta">$i_case<div><b>Отрасль:</b> $industry</div></div>
                <div class="case-card__meta case-card__meta--team">$i_user<div><b>Сотрудники Umbrella Group:</b><br>$team</div></div>
        EOHTML;
        // /u обязателен: без него \R ловит байт 0x85 внутри кириллицы («х» = D1 85) и режет слова
        $steps = array_filter(array_map('trim', preg_split('/\R/u', $meta('case_steps'))));
        if ($steps) {
            $details = self::page_details($branch, $steps, $meta('case_service'), $meta('case_outcome'), $title, $media . $tag, $i_case_meta, $story, $company . $proof . $feedback, $meta('case_team'));
            $details_attr = ' data-wide="1"';
        } else {
            $details = <<<EOHTML
                <h3 class="case-modal__title">$title</h3>
                $company
                $i_case_meta
                <div class="case-modal__story">$story</div>
                $proof
                $feedback
            EOHTML;
            $details_attr = '';
        }

        $slug = esc_attr(transliterate($branch));
        if (isset(self::PAGE_EXTRA_TABS[$post->ID])) {
            $slug .= ' ' . esc_attr(transliterate(self::PAGE_EXTRA_TABS[$post->ID]));
        }
        return <<<EOHTML
        <article class="case-card" data-branch="$slug" data-id="{$post->ID}">
            <div class="case-card__media">$media$tag</div>
            <div class="case-card__main">
                <h3 class="case-card__title">$title</h3>
                <div class="case-card__meta">$i_case<div><b>Отрасль:</b> $industry</div></div>
                <div class="case-card__meta case-card__meta--team">$i_user<div><b>Сотрудники Umbrella Group:</b><br>$team</div></div>
                <div class="case-card__actions">
                    <button type="button" class="case-card__more">Подробнее о&nbsp;кейсе$i_arrow</button>
                    $service
                </div>
            </div>
            <div class="case-card__story">$story</div>
            <template class="case-card__details"$details_attr>$details</template>
        </article>
        EOHTML;
    }

    private function get_tile($post): string
    {
        $visible = in_array_r($this->all_metas[0] ?? null, get_post_meta($post->ID)) ? "" : "invisible";
        $postmeta = $this->getPostmeta($post);
        $metaclases = "";
        foreach ($postmeta as $post_meta) {
            $metaclases .= transliterate($post_meta) . " ";
        }
        $metaclases = "cases_" . $metaclases;
        $title = get_the_title($post);
        $logo_url = esc_attr(get_post_meta($post->ID, 'case_logo_url', true));
        $author = get_post_meta($post->ID, 'case_author', true);
        $logo_alt = sanitize_text_field($author);
        $industry = esc_attr(get_post_meta($post->ID, 'case_industry', true));
        $team = esc_attr(get_post_meta($post->ID, 'case_team', true));
        $issue = esc_attr(get_post_meta($post->ID, 'case_issue', true));
        $solution = esc_attr(get_post_meta($post->ID, 'case_solution', true));
        $proof = esc_attr(get_post_meta($post->ID, 'case_proof', true));
        $proof_title = esc_attr(get_post_meta($post->ID, 'case_proof_title', true));
        if (strlen($proof) > 0 && strlen($proof_title) > 0) {
            $proof_lightbox_id = $post->ID . "-proof-lightbox";
            $proof_lightbox = "[lightbox id={$proof_lightbox_id}] <img src='{$proof}'> [/lightbox]";
            $proof = " <div class='proof hide-for-small'><a href='#{$proof_lightbox_id}'><img src='{$proof}' alt='{$proof_title}'></a><span>{$proof_title}</span></div> $proof_lightbox";
        }
        $feedback_text = esc_attr(get_post_meta($post->ID, 'case_feedback_text', true));
        $feedback_url = esc_attr(get_post_meta($post->ID, 'case_feedback_url', true));
        if (strlen($logo_url) > 0) {
            $company = <<<EOHTML
                <div class="company">
                    <div class="logo"><img height='100px' alt="$logo_alt" src="$logo_url"></div>
                    <div class="author">$author</div>
                </div>
            EOHTML;
        } else {
            $company = "";
        }
        if (strlen($feedback_text) > 0 && strlen($feedback_url) > 0) {
            if (str_contains($feedback_url, "flamp")) {
                $icon = "<img height='20px' width='20px' src='" . esc_url(ug_flamp_icon_url()) . "' alt='flamp-logo'>";
                $target = "target='_blank'";
                $feedback = "<div class='case-feedback hide-for-small'>$icon <a $target href='{$feedback_url}'>{$feedback_text}</a></div> ";
            } else {
                $icon = "";
                $target = "";
                $feedback_lightbox_id = $post->ID . "-feedback-lightbox";
                $feedback_lightbox = "[lightbox id={$feedback_lightbox_id}] <img src='{$feedback_url}'> [/lightbox]";
                $feedback = "<div class='case-feedback hide-for-small'><a $target href='#{$feedback_lightbox_id}'>$icon {$feedback_text}</a></div> $feedback_lightbox";
            }
        } else {
            $feedback = "";
        }
        $before = ($this->is_cases_category_page) ? "" : '[row_inner style="large" v_align="top"][col_inner span="12" span__sm="12" align="left" margin="0px 0px 0px 0px"] ';
        $after = ($this->is_cases_category_page) ? "" : '[/col_inner][/row_inner]';
        $html = <<<EOHTML
        $before
            <div class="$metaclases tile $visible">
                <div class="title"> $title</div>
                $company
                <div class="industry"><img alt="Briefcase-image" style="height:20px;" src="/wp-content/uploads/manual_uploads/Briefcase_Icon_1.png"><span class="strong">Отрасль: </span>$industry</div>
                <div class="team"><img alt="person-image" style="height:20px;" src="/wp-content/uploads/manual_uploads/User_Icon_1.png"><span class="strong">Сотрудники Umbrella Group: </span>$team</div>
                <div class="timeline">
                    <div class="issue"> <div class="white-background"></div> <div class="title">Проблема </div><div class="content">$issue</div> <div class="arrow show-for-small">→</div></div>
                    <div class="solution"> <div class="title">Результат </div><div class="content"><div class="text">$solution</div> $proof</div></div>
                </div>
                $feedback
            </div>
        $after
        EOHTML;

        return $html;
    }

    private function get_cases_posts(string $category): array
    {
        // ===== ПЛОСКИЙ РЕЖИМ: category + ids (объединение) =====
        if ($this->is_flat_mode) {
            $result = [];
            $seen_ids = [];

            // 1) Сначала — кейсы по branch + category
            if ($this->filter_category !== "") {
                $meta_query = array('relation' => 'AND');
                if ($category !== "" && $category !== "Общий" && $category !== "cases") {
                    $meta_query[] = array(
                        'key' => 'case_branch',
                        'value' => $category,
                        'compare' => '=',
                    );
                }
                $meta_query[] = array(
                    'key' => 'case_category',
                    'value' => $this->filter_category,
                    'compare' => '=',
                );

                $cat_posts = get_posts(array(
                    'numberposts' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'post_type' => 'case',
                    'meta_query' => $meta_query,
                ));

                foreach ($cat_posts as $p) {
                    if (!in_array($p->ID, $seen_ids)) {
                        $result[] = $p;
                        $seen_ids[] = $p->ID;
                    }
                }
            }

            // 2) Потом — добавленные по ids (в порядке из шорткода)
            if (!empty($this->filter_ids)) {
                // посты и их мета одним запросом, а не get_post()/get_post_meta() на каждый id
                _prime_post_caches($this->filter_ids, false, true);
                foreach ($this->filter_ids as $id) {
                    if (in_array($id, $seen_ids)) {
                        continue;
                    }
                    $p = get_post($id);
                    if ($p && $p->post_type === 'case' && $p->post_status === 'publish') {
                        $result[] = $p;
                        $seen_ids[] = $p->ID;
                    }
                }
            }

            return $result;
        }

        // ===== СТАРАЯ ЛОГИКА =====
        if ($category == "Общий") {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'case',
                'meta_key' => 'case_common',
                'meta_query' => array(
                    array('key' => 'case_common', 'value' => 'Да', 'compare' => '=')
                )
            );
        } elseif ($category == "cases") {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'case'
            );
        } else {
            $args = array(
                'numberposts' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_type' => 'case',
                'meta_key' => 'case_branch',
                'meta_query' => array(
                    array('key' => 'case_branch', 'value' => $category, 'compare' => '=')
                )
            );
        }
        return get_posts($args);
    }

    private function get_category_by_url(): string
    {
        if (str_contains(ug_request_path(), "/about/cases/")) {
            return "cases";
        }
        $category = ug_service_category();
        return ($category === null || $category === "Сделки с недвижимостью") ? "Общий" : $category;
    }

    private function get_title(string $category): string
    {
        $title = $this->titles[$category]["title"] ?? "";
        $title = (strlen($title) > 1) ? ' <div class="cases_title"><h2 class="title">' . $title . '</h2></div>' : '';
        return $title;
    }

    private function concat_metas($post)
    {
        $postmeta = $this->getPostmeta($post);
        foreach ($postmeta as $post_meta) {
            if (!in_array($post_meta, $this->all_metas)) {
                array_push($this->all_metas, $post_meta);
            }
        }
    }

    private function get_tabs(): string
    {
        if (sizeof($this->all_metas) < 2) {
            return "";
        }
        sort($this->all_metas);
        $tabs = '<ul class="tabs">';
        foreach ($this->all_metas as $meta) {
            $selected = $meta == $this->all_metas[0] ? "selected" : "";
            $metaclasses = transliterate($meta);
            $metaclasses = "cases_" . $metaclasses;
            $tabs .= "<li class='$metaclasses $selected'>$meta</li>";
        }
        $tabs .= '</ul>';
        return $tabs;
    }

    private function getPostmeta($post): array
    {
        $key = $this->getPostmetaKey();
        $postmeta = get_post_meta($post->ID, $key, false);
        return $postmeta;
    }

    private function getPostmetaKey(): string
    {
        if ($this->category == "Общий" || $this->category == "cases") {
            return 'case_branch';
        } else {
            return 'case_category';
        }
    }

    private function get_posts_by_meta(string $meta): array
    {
        $key = $this->getPostmetaKey();
        $args = array(
            'numberposts' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_type' => 'case',
            'meta_key' => $key,
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $meta,
                    'compare' => '=',
                )
            )
        );
        return get_posts($args);
    }
}

/**
 * Картинка справа в первом экране /about/cases/: путь от корня сайта.
 * Пусто — серая заглушка с размером. Файл 1500 × 740: правый край — там же, где
 * кончается главное меню, левая треть растворяется в фоне; сейчас стоит фото,
 * вырезанное из макета.
 */
const UG_CASES_HERO_IMAGE = '/wp-content/themes/umbrella-group/assets/img/cases-hero-office.jpg';

/**
 * Картинка справа в баннере «Нужен сильный результат…» под кейсами: путь от
 * корня сайта, пусто — заглушка. Файл 1400 × 600, тёмный; левая четверть
 * растворяется в фоне баннера, на телефоне кадр режется по центру.
 */
const UG_CASES_CTA_IMAGE = '';

/**
 * Первый экран /about/cases/: крошки, h1, подзаголовок, абзац и картинка справа.
 * Шапку page.php с крошками и h1 страница не выводит — см. проверку там.
 */
function ug_cases_hero(): string
{
    $breadcrumbs = get_breadcrumbs();
    $image = UG_CASES_HERO_IMAGE
        ? '<img src="' . esc_url(home_url(UG_CASES_HERO_IMAGE)) . '" alt="" loading="eager">'
        : '<span class="cases-hero__stub">Картинка 1500&nbsp;×&nbsp;740</span>';

    ug_enqueue_style('assets/css/blocks/cases_hero.css');

    return <<<EOHTML
    <section class="cases-hero">
        <div class="cases-hero__inner">
            <div class="cases-hero__image">$image</div>
            <div class="breadcrumbs">$breadcrumbs</div>
            <h1 class="cases-hero__title">Кейсы</h1>
            <p class="cases-hero__lead">Реальные задачи. Конкретные решения. Измеримый результат.</p>
            <p class="cases-hero__desc">Показываем, как помогаем бизнесу снижать риски, экономить деньги и&nbsp;наводить порядок в&nbsp;учёте, документах и&nbsp;процессах.</p>
        </div>
    </section>
    EOHTML;
}

function cases_block_shortcode($atts)
{
    $shortcode = new cases();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('cases', 'cases_block_shortcode');
