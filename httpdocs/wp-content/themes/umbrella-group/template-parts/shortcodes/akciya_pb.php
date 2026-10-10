<?php
/**
 * Блоки страницы акции «Паспорт безопасности объекта со скидкой 10 000 ₽»
 * (/akcii/pasport-bezopasnosti-obekta-skidka-10000/).
 *
 * Устроено как akciya_tz: каждый блок — отдельный шорткод, тексты живут здесь.
 */
class akciya_pb
{
    public $atts;

    /**
     * Первый экран акции. Заголовок здесь — h1 страницы: стандартный заголовок
     * записи шаблон акции в этом случае не выводит (umbrella_akciya_has_own_hero).
     * Кнопка открывает всплывающую форму «Расчёт стоимости услуги».
     */
    public function render_hero()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-pb.css']);

        $arrow = '<svg width="22" height="10" viewBox="0 0 22 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h19M16 1l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        // Лайтбокс рендерим сразу: возвращённые шорткоды второй раз не разбираются.
        $lightbox = do_shortcode('[lightbox id="calculate-price-contact-form-lightbox" width="400px" padding="0px"][contact-form-7 id="9965" title="Форма Расчёт стоимости услуги"][/lightbox]');

        return <<<EOT
            <section class="akciya-pb-hero">
                <div class="akciya-pb-hero__text">
                    <div class="akciya-pb-hero__badge">Акция до 31.10.2026</div>
                    <h1 class="akciya-pb-hero__title">Паспорт<br>безопасности объекта<span class="akciya-pb-hero__title-accent">со&nbsp;скидкой 10&nbsp;000&nbsp;₽</span></h1>
                    <p class="akciya-pb-hero__lead">Проверим, распространяются ли требования антитеррористической защищённости на&nbsp;ваш объект, и&nbsp;оформим необходимые документы.</p>
                    <a class="akciya-pb-hero__button" href="#calculate-price-contact-form-lightbox">Проверить объект и&nbsp;получить скидку $arrow</a>
                </div>
                <div class="akciya-pb-hero__price">
                    <p class="akciya-pb-hero__price-label">Стоимость услуги</p>
                    <p class="akciya-pb-hero__price-new"><span class="akciya-pb-hero__price-from">от</span> 70&nbsp;000&nbsp;₽</p>
                    <p class="akciya-pb-hero__price-old"><s>вместо 80&nbsp;000&nbsp;₽</s></p>
                    <div class="akciya-pb-hero__discount">&minus;10&nbsp;000&nbsp;₽</div>
                    <p class="akciya-pb-hero__caption">Акция действует до&nbsp;31&nbsp;октября 2026&nbsp;года</p>
                </div>
                $lightbox
            </section>
            EOT;
    }

    /** Иконки карточек — инлайном, чтобы не заводить картинки в медиатеке. */
    private function get_icons()
    {
        return [
            'cross' => '<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11.5 4.5h7v7h7v7h-7v7h-7v-7h-7v-7h7v-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',
            'bag' => '<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6.5 10.5h17l-1.2 14.2a1.5 1.5 0 0 1-1.5 1.3H9.2a1.5 1.5 0 0 1-1.5-1.3L6.5 10.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M11 13.5V9a4 4 0 0 1 8 0v4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
            'pill' => '<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="2.9" y="10.2" width="24.2" height="9.6" rx="4.8" transform="rotate(-45 15 15)" stroke="currentColor" stroke-width="2"/><path d="m11.6 11.6 6.8 6.8" stroke="currentColor" stroke-width="2"/></svg>',
        ];
    }

    /** Карточки «Кому стоит проверить требования» — порядок массива = порядок на странице. */
    private function get_audience()
    {
        return [
            [
                'icon' => 'cross',
                'title' => 'Медицинские<br>центры',
                'text' => 'Частные и&nbsp;государственные медицинские организации, клиники, диагностические центры.',
            ],
            [
                'icon' => 'bag',
                'title' => 'Торговые центры<br>и&nbsp;магазины',
                'text' => 'Торговые комплексы, магазины, объекты розничной торговли и&nbsp;другие коммерческие объекты.',
            ],
            [
                'icon' => 'pill',
                'title' => 'Аптеки и&nbsp;другие<br>объекты',
                'text' => 'Аптечные организации, а&nbsp;также иные объекты, которые могут подпадать под установленные требования.',
            ],
        ];
    }

    /** Кому стоит проверить требования: шапка, карточки объектов и сноска. */
    public function render_audience()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-pb.css']);

        $alert = '<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="13" cy="13" r="11.5" stroke="currentColor" stroke-width="1.8"/><path d="M13 7.5v7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="13" cy="18.3" r="1.2" fill="currentColor"/></svg>';

        return ug_render('akciya-section', [
            'block'  => 'akciya-pb-audience',
            'kicker' => 'Для кого',
            'title'  => 'Кому стоит проверить требования?',
            'lead'   => 'Требования антитеррористической защищённости применяются к&nbsp;различным объектам. Мы&nbsp;поможем определить, распространяются&nbsp;ли они на&nbsp;ваш.',
            'layout' => 'cards',
            'items'  => $this->get_audience(),
            'icons'  => $this->get_icons(),
            'after'  => <<<EOT
                <div class="akciya-pb-audience__note">
                    <div class="akciya-pb-audience__note-icon">$alert</div>
                    <p class="akciya-pb-audience__note-text">Необходимость паспорта зависит от&nbsp;категории объекта и&nbsp;применимых требований.</p>
                </div>
                EOT,
        ]);
    }

    /** Этапы сопровождения — порядок массива задаёт нумерацию и стрелки. */
    private function get_steps()
    {
        return [
            [
                'title' => 'Анализ объекта<br>и&nbsp;требований',
                'text' => 'Изучаем особенности вашего объекта и&nbsp;применимые требования.',
            ],
            [
                'title' => 'Подготовка<br>документов',
                'text' => 'Формируем комплект необходимых документов с&nbsp;учётом актуальных нормативных требований.',
            ],
            [
                'title' => 'Согласование<br>в&nbsp;установленном порядке',
                'text' => 'Сопровождаем подачу и&nbsp;взаимодействие с&nbsp;ответственными органами.',
            ],
            [
                'title' => 'Передача<br>готового паспорта',
                'text' => 'Вы получаете оформленный паспорт безопасности объекта.',
            ],
        ];
    }

    /**
     * Что входит в сопровождение: этапы в ряд и красная полоса с экономией.
     * Кнопка полосы открывает ту же всплывающую форму, что и кнопка первого
     * экрана: сам лайтбокс уже выведен там, второй с тем же id не нужен.
     */
    public function render_steps()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-pb.css']);

        $arrow = '<svg width="18" height="10" viewBox="0 0 18 10" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 5h15M12 1l4 4-4 4" stroke="#ec1c23" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $percent = '<svg width="36" height="36" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M22 8 8 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="9.5" cy="9.5" r="2.8" stroke="currentColor" stroke-width="1.8"/><circle cx="20.5" cy="20.5" r="2.8" stroke="currentColor" stroke-width="1.8"/></svg>';

        $steps = ug_render('akciya-section', [
            'block'  => 'akciya-pb-steps',
            'kicker' => 'Как мы работаем',
            'title'  => 'Что входит в&nbsp;сопровождение',
            'lead'   => 'Берём на&nbsp;себя весь процесс&nbsp;— от&nbsp;анализа до&nbsp;получения готового документа.',
            'head'   => 'head-row',
            'layout' => 'steps',
            'items'  => $this->get_steps(),
            'arrow'  => $arrow,
        ]);

        return $steps . <<<EOT
            <div class="akciya-pb-saving">
                <div class="akciya-pb-saving__icon">$percent</div>
                <div class="akciya-pb-saving__text">
                    <p class="akciya-pb-saving__title">Экономия <span class="akciya-pb-saving__sum">10&nbsp;000&nbsp;₽</span></p>
                    <p class="akciya-pb-saving__caption">при оформлении до&nbsp;<b>31&nbsp;октября</b> 2026&nbsp;года</p>
                </div>
                <a class="akciya-pb-saving__button" href="#calculate-price-contact-form-lightbox">Оставить заявку $arrow</a>
            </div>
            EOT;
    }

    /**
     * Заявка внизу страницы: текст слева, справа та же форма акции, что
     * в боковой колонке, — вёрстка под ширину блока в akciya-pb.css.
     */
    public function render_request()
    {
        umbrella_add_custom_css_files(['/assets/css/blocks/akciya-pb.css']);

        $form = umbrella_akcii_contact_form();

        return <<<EOT
            <section class="akciya-pb-request">
                <div class="akciya-pb-request__text">
                    <p class="akciya-pb-request__kicker">Оставьте заявку</p>
                    <h2 class="akciya-pb-request__title">Узнайте, нужен&nbsp;ли паспорт вашему объекту</h2>
                    <p class="akciya-pb-request__lead">Наши специалисты бесплатно проконсультируют и&nbsp;подскажут, какие требования действуют в&nbsp;вашем случае.</p>
                </div>
                <div class="akciya-pb-request__form">
                    $form
                </div>
            </section>
            EOT;
    }
}

// [akciya_pb_hero], [akciya_pb_audience], [akciya_pb_steps], [akciya_pb_request]
foreach (['hero', 'audience', 'steps', 'request'] as $akciya_pb_block) {
    add_shortcode('akciya_pb_' . $akciya_pb_block, static fn($atts) => (new akciya_pb())->{'render_' . $akciya_pb_block}());
}
unset($akciya_pb_block);
