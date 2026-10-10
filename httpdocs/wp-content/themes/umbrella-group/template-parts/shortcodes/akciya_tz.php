<?php
/**
 * Блоки страницы акции «Регистрация товарного знака со скидкой 15%»
 * (/akcii/registraciya-tovarnogo-znaka-skidka-15/).
 *
 * Каждый блок — отдельный шорткод, в контенте записи они идут по порядку
 * сверху вниз. Тексты живут здесь, а не в админке: так же, как в остальных
 * блоках темы.
 */
class akciya_tz
{
    public $atts;

    /**
     * Первый экран акции. Заголовок здесь — h1 страницы: стандартный заголовок
     * записи шаблон акции в этом случае не выводит (umbrella_akciya_has_own_hero).
     */
    public function render_hero()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-tz.css']);

        return <<<EOT
            <section class="akciya-tz-hero">
                <div class="akciya-tz-hero__text">
                    <div class="akciya-tz-hero__badge">Акция &minus;15%</div>
                    <h1 class="akciya-tz-hero__title">Ваш бренд работает<br>на вас.<span class="akciya-tz-hero__title-accent">А кому принадлежат<br>права на него?</span></h1>
                    <p class="akciya-tz-hero__lead">Закрепите права на название и логотип. Проверим обозначение, подготовим заявку и сопроводим регистрацию в Роспатенте.</p>
                </div>
                <div class="akciya-tz-hero__promo">
                    <p class="akciya-tz-hero__kicker">Надёжная правовая защита вашего бизнеса</p>
                    <div class="akciya-tz-hero__discount">&minus;15%</div>
                    <p class="akciya-tz-hero__caption">на регистрацию товарного знака</p>
                </div>
            </section>
            EOT;
    }

    /** Иконки карточек — инлайном, чтобы не заводить картинки в медиатеке. */
    private function get_icons()
    {
        return [
            'shield' => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M14 3.5 5.6 6.8v6.5c0 4.9 3.4 8.9 8.4 10.7 5-1.8 8.4-5.8 8.4-10.7V6.8L14 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'document' => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M16.3 3.8H8.4A1.4 1.4 0 0 0 7 5.2v17.6a1.4 1.4 0 0 0 1.4 1.4h11.2a1.4 1.4 0 0 0 1.4-1.4V8.5l-4.7-4.7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M16 4.2V8.8h4.7" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10.3 13.3h7.4M10.3 17h5.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'chart' => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="4.2" y="17" width="5" height="7" rx="1.2" fill="currentColor"/><rect x="11.5" y="11.5" width="5" height="12.5" rx="1.2" fill="currentColor"/><rect x="18.8" y="4.5" width="5" height="19.5" rx="1.2" fill="currentColor"/></svg>',
            'diamond' => '<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8.4 4.9h11.2l4.2 6.3L14 23.8 2.2 11.2l4.2-6.3h2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M2.6 11.2h22.8M10.5 4.9 8.4 11.2 14 23.4l5.6-12.2-2.1-6.3" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
        ];
    }

    /** Карточки «Ваши возможности» — порядок массива = порядок на странице. */
    private function get_benefits()
    {
        return [
            [
                'icon' => 'shield',
                'title' => 'Защитить название<br>и логотип',
                'text' => 'Закрепить исключительное право на ваше обозначение.',
            ],
            [
                'icon' => 'document',
                'title' => 'Противодействовать<br>копированию',
                'text' => 'Законно защищать бренд от недобросовестных конкурентов.',
            ],
            [
                'icon' => 'chart',
                'title' => 'Развивать франшизу',
                'text' => 'Передавать право использования знака партнёрам и масштабировать бизнес.',
            ],
            [
                'icon' => 'diamond',
                'title' => 'Создать ценный актив',
                'text' => 'Товарный знак&nbsp;— это нематериальный актив, который повышает стоимость вашей компании.',
            ],
        ];
    }

    /** Зачем регистрировать знак: шапка блока и карточки возможностей. */
    public function render_benefits()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-tz.css']);

        return ug_render('akciya-section', [
            'block'  => 'akciya-tz-benefits',
            'kicker' => 'Ваши возможности',
            'title'  => 'Зачем регистрировать товарный знак?',
            'lead'   => 'Регистрация позволяет закрепить исключительное право на обозначение и получить инструменты для развития и защиты бизнеса.',
            'layout' => 'cards',
            'items'  => $this->get_benefits(),
            'icons'  => $this->get_icons(),
        ]);
    }

    /** Пункты «Кому актуальна регистрация» — нумерация считается по порядку. */
    private function get_audience()
    {
        return [
            [
                'title' => 'Магазины<br>и производители',
                'text' => 'Собственный бренд, торговая марка, уникальное название или логотип.',
            ],
            [
                'title' => 'Рестораны, клиники,<br>школы, услуги',
                'text' => 'Узнаваемое название&nbsp;— важная часть репутации и доверия ваших клиентов.',
            ],
            [
                'title' => 'Компании, которые<br>масштабируются',
                'text' => 'Планируете новые точки, выход в другие регионы или франшизу.',
            ],
        ];
    }

    /** Кому актуальна регистрация: нумерованные пункты через разделители. */
    public function render_audience()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-tz.css']);

        return ug_render('akciya-section', [
            'block'  => 'akciya-tz-audience',
            'kicker' => 'Для кого',
            'title'  => 'Кому актуальна регистрация?',
            'layout' => 'numbered',
            'items'  => $this->get_audience(),
        ]);
    }

    /** Шаги регистрации — порядок массива задаёт нумерацию и стрелки. */
    private function get_steps()
    {
        return [
            [
                'title' => 'Проверка обозначения',
                'text' => 'Анализируем уникальность и возможность регистрации.',
            ],
            [
                'title' => 'Подготовка и подача заявки',
                'text' => 'Готовим документы и направляем в Роспатент.',
            ],
            [
                'title' => 'Сопровождение',
                'text' => 'Ведём коммуникацию с экспертами и отвечаем на запросы.',
            ],
            [
                'title' => 'Получение свидетельства',
                'text' => 'Доводим процесс до результата и передаём вам документы.',
            ],
        ];
    }

    /**
     * Как проходит регистрация: шаги сеткой 2×2. Стрелка ведёт к соседнему шагу
     * в том же ряду, у последнего в ряду её прячет CSS.
     */
    public function render_steps()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-tz.css']);

        return ug_render('akciya-section', [
            'block'  => 'akciya-tz-steps',
            'kicker' => 'Как это работает',
            'title'  => 'Как проходит регистрация?',
            'layout' => 'steps',
            'items'  => $this->get_steps(),
            'arrow'  => '<svg width="18" height="10" viewBox="0 0 18 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h15M12 1l4 4-4 4" stroke="#ec1c23" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        ]);
    }

    /** Контурные иконки правой колонки баннера. */
    private function get_cta_icon($name)
    {
        $icons = [
            'gift' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4.5 9.5h15v10.3a.9.9 0 0 1-.9.9H5.4a.9.9 0 0 1-.9-.9V9.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M3.3 6.4h17.4v3.1H3.3V6.4ZM12 6.4v14.3" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M12 6.4S10.9 3 8.9 3C7.6 3 7 3.8 7 4.7c0 1 .9 1.7 2 1.7h3ZM12 6.4S13.1 3 15.1 3c1.3 0 1.9.8 1.9 1.7 0 1-.9 1.7-2 1.7h-3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
            'clock' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="8.6" stroke="currentColor" stroke-width="1.5"/><path d="M12 7.3V12l3.4 2.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'shield' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3.1 4.8 5.9v5.6c0 4.2 2.9 7.6 7.2 9.1 4.3-1.5 7.2-4.9 7.2-9.1V5.9L12 3.1Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
        ];

        return isset($icons[$name]) ? $icons[$name] : '';
    }

    /**
     * Баннер с призывом оставить заявку. Кнопка открывает всплывающую форму
     * «Расчёт стоимости услуги» — ту же, что кнопка «Получить консультацию»
     * на странице услуги о регистрации товарного знака.
     */
    public function render_cta()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-tz.css']);

        $features = [
            ['icon' => 'gift', 'text' => 'Выгодные условия<br>до конца акции'],
            ['icon' => 'clock', 'text' => 'Профессиональное<br>сопровождение'],
            ['icon' => 'shield', 'text' => 'Надёжная правовая<br>защита'],
        ];

        $features_html = '';
        foreach ($features as $feature) {
            $icon = $this->get_cta_icon($feature['icon']);
            $features_html .= <<<EOT
                <div class="akciya-tz-cta__feature">
                    <div class="akciya-tz-cta__feature-icon">$icon</div>
                    <p class="akciya-tz-cta__feature-text">{$feature['text']}</p>
                </div>
                EOT;
        }

        $arrow = '<svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h17M14 1l4 4-4 4" stroke="#ec1c23" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        // Лайтбокс рендерим сразу: возвращённые шорткоды второй раз не разбираются.
        $lightbox = do_shortcode('[lightbox id="calculate-price-contact-form-lightbox" width="400px" padding="0px"][contact-form-7 id="9965" title="Форма Расчёт стоимости услуги"][/lightbox]');

        return <<<EOT
            <section class="akciya-tz-cta">
                <div class="akciya-tz-cta__main">
                    <p class="akciya-tz-cta__kicker">Специальное предложение</p>
                    <p class="akciya-tz-cta__title">Ваш бренд заслуживает защиты.<br>Регистрация со скидкой 15%</p>
                    <a class="akciya-tz-cta__button" href="#calculate-price-contact-form-lightbox">Получить скидку 15% $arrow</a>
                </div>
                <div class="akciya-tz-cta__features">
                    $features_html
                </div>
                $lightbox
            </section>
            EOT;
    }
}

/**
 * Есть ли у страницы акции свой первый экран. Если есть — шаблон не печатает
 * заголовок записи, лид из описания и строку «Специальное предложение»:
 * всё это уже нарисовано внутри блока.
 */
function umbrella_akciya_has_own_hero($post = null)
{
    $post = get_post($post);

    return $post && (has_shortcode($post->post_content, 'akciya_tz_hero')
        || has_shortcode($post->post_content, 'akciya_pb_hero'));
}

/** Крошки без заголовка — для страниц акций со своим первым экраном. */
function umbrella_akciya_print_breadcrumbs()
{
    echo '<header class="umbrella-header-and-breadcrumbs container"><div class="breadcrumbs">'
        . get_breadcrumbs()
        . '</div></header>';
}

/**
 * Форма заявки со страницы акции (CF7 «Форма на боковой панели акции»).
 * Заголовок, подзаголовок и текст кнопки у каждой акции свои — из полей записи.
 * Выводится в боковой колонке и в нижнем блоке акций, где он есть.
 */
function umbrella_akcii_contact_form($post_id = null)
{
    $post_id = $post_id ?: get_the_ID();
    $contact_form = do_shortcode('[contact-form-7 id="9878" title="Форма на боковой панели акции"]', true);
    $contact_form = str_replace("_akcii_form_header_", get_post_meta($post_id, 'akcii_form_header', true), $contact_form);
    $contact_form = str_replace("_akcii_form_subheader_", get_post_meta($post_id, 'akcii_form_subheader', true), $contact_form);
    $contact_form = str_replace("_akcii_form_button_text_", get_post_meta($post_id, 'akcii_form_button_text', true), $contact_form);

    return $contact_form;
}

// [akciya_tz_hero], [akciya_tz_benefits], [akciya_tz_audience], [akciya_tz_steps], [akciya_tz_cta]
foreach (['hero', 'benefits', 'audience', 'steps', 'cta'] as $akciya_tz_block) {
    add_shortcode('akciya_tz_' . $akciya_tz_block, static fn($atts) => (new akciya_tz())->{'render_' . $akciya_tz_block}());
}
unset($akciya_tz_block);
