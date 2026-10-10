<?php
class new_main_first_screen
{
    public $atts;

    /**
     * Слайды первого экрана (акции). Чтобы добавить ещё одну акцию — допишите
     * элемент массива: разметка, стрелки и переключение подхватятся сами.
     * Порядок элементов = порядок листания слева направо.
     */
    public static function get_slides()
    {
        return [
            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Ваш бренд работает<br>на вас.<span class="accent">А кому принадлежат<br>права на него?</span>',
                'descriptions' => [
                    'Название и логотип могут стать узнаваемыми, но без регистрации товарного знака защитить права на них сложнее.',
                    'Проверим обозначение, подготовим и подадим заявку в Роспатент, сопроводим процедуру регистрации до получения свидетельства.',
                ],
                'features' => [
                    [
                        'icon' => 'search',
                        'title' => 'Проверим<br>обозначение',
                        'text' => 'Убедимся, что ваш знак уникален',
                    ],
                    [
                        'icon' => 'document',
                        'title' => 'Подготовим<br>и подадим заявку',
                        'text' => 'Соберём документы и направим в Роспатент',
                    ],
                    [
                        'icon' => 'shield',
                        'title' => 'Сопроводим<br>регистрацию',
                        'text' => 'Контролируем процесс до получения свидетельства',
                    ],
                ],
                'button_text' => 'Зарегистрировать знак со скидкой 15%',
                'button_link' => '/akcii/registraciya-tovarnogo-znaka-skidka-15/',
                'image' => '/wp-content/uploads/registraciya-tz-banner.png',
                'image_webp' => '/wp-content/uploads/registraciya-tz-banner.webp',
                'image_alt' => 'Свидетельство на товарный знак — скидка на регистрацию',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Паспорт<br>безопасности объекта<span class="accent">со&nbsp;скидкой 10&nbsp;000&nbsp;&#8381;</span>',
                'descriptions' => [
                    'Для медицинских центров, торговых объектов, аптек и других организаций действуют требования антитеррористической защищённости. Проверим, распространяются ли они на ваш объект, и оформим необходимые документы.',
                ],
                'features' => [
                    [
                        'icon' => 'medical',
                        'title' => 'Для<br>медицинских<br>центров',
                    ],
                    [
                        'icon' => 'shop',
                        'title' => 'Для торговых<br>центров<br>и магазинов',
                    ],
                    [
                        'icon' => 'pill',
                        'title' => 'Для аптек<br>и прочих объектов',
                    ],
                ],
                'button_text' => 'Проверить объект и получить скидку',
                'button_link' => '/akcii/pasport-bezopasnosti-obekta-skidka-10000/',
                'image' => '/wp-content/uploads/pasport-bezopasnosti-banner-2.png',
                'image_webp' => '/wp-content/uploads/pasport-bezopasnosti-banner-2.webp',
                'image_alt' => 'Паспорт антитеррористической защищённости объекта — скидка 10 000 рублей',
                'modifier' => 'promo-slide-pasport',
            ],

            /* Слайд «Сибирский налоговый семинар 2027» — снят с главной 22.09.2026,
               оставлен на случай, если акцию вернут в ротацию.
            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Сибирский налоговый семинар 2027',
                'descriptions' => [
                    '2026 год был только началом. И уже кардинально изменил налоговую реальность российского бизнеса. Но на этом изменения не заканчиваются. На семинаре эксперты разберут, с чем бизнес входит в 2027 год, какие изменения уже известны и как они повлияют на работу компаний.',
                ],
                'list_header' => 'Спикеры:',
                'list_items' => [
                    'Софья Гладкова - председатель Общественного совета России при УФНС по Новосибирской области',
                    'Никита Демиденко - ведущий налоговый юрист по Новосибирской области, директор ООО "Юрсервис" (Входит в ГК Umbrella Group)',
                ],
                'button_text' => 'Подробнее о семинаре',
                'button_link' => '/blog/nalogovi-seminar-2027/',
                'image' => 'https://taxlab.ru/wp-content/uploads/spikery-banner-4.png',
                'image_alt' => 'Спикеры семинара',
            ],
            */
        ];
    }

    /** [ширина, высота] картинки слайда из uploads по её пути или null (внешний URL, нет файла). */
    private static function image_size($src)
    {
        if (!is_string($src) || !str_starts_with($src, '/wp-content/uploads/')) {
            return null;
        }
        $size = wp_getimagesize(ABSPATH . ltrim($src, '/'));
        return $size ? [(int) $size[0], (int) $size[1]] : null;
    }

    /** Иконки блока преимуществ — инлайном, чтобы не зависеть от медиатеки. */
    private function get_icon($name)
    {
        $icons = [
            'search' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="11" cy="11" r="6.25" stroke="currentColor" stroke-width="1.8"/><path d="M15.7 15.7 20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'document' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M14 3.75H7A1.25 1.25 0 0 0 5.75 5v14A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V8L14 3.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M13.75 4v4.25H18" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8.75 12.5h6.5M8.75 16h4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'shield' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'medical' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9.5 3.75h5V9.5h5.75v5H14.5v5.75h-5V14.5H3.75v-5H9.5V3.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'shop' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4.75 8.4h14.5v9.85a2.5 2.5 0 0 1-2.5 2.5h-9.5a2.5 2.5 0 0 1-2.5-2.5V8.4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8.5 8.4V6.9a3.5 3.5 0 0 1 7 0v1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'pill' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><g transform="rotate(-45 12 12)"><rect x="2.4" y="8.4" width="19.2" height="7.2" rx="3.6" stroke="currentColor" stroke-width="1.8"/><path d="M12 8.4v7.2" stroke="currentColor" stroke-width="1.8"/></g></svg>',
        ];
        return isset($icons[$name]) ? $icons[$name] : '';
    }

    private function render_button($slide, $extra_class)
    {
        $text = $slide['button_text'];
        $link = $slide['button_link'];
        $arrow = '<svg class="banner-button-arrow" width="24" height="12" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 6h21M17 1l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        return <<<EOT
            <a href="$link" class="$extra_class button primary lowercase banner-button">$text $arrow</a>
            EOT;
    }

    private function render_slide($slide, $index)
    {
        // h1 на странице должен быть один: он у первого слайда, у остальных — абзац.
        $header_tag = $index === 0 ? 'h1' : 'p';
        $active = $index === 0 ? ' is-active' : '';
        // Класс акции — на случай, когда картинка или заголовок просят своих размеров.
        $modifier = empty($slide['modifier']) ? '' : ' ' . $slide['modifier'];
        $sub_header = $slide['sub_header'];
        $title = $slide['title'];

        $descriptions = '';
        foreach ($slide['descriptions'] as $description) {
            $descriptions .= '<p class="description">' . $description . '</p>';
        }

        $list = '';
        if (!empty($slide['list_items'])) {
            $list_header = isset($slide['list_header']) ? '<p>' . $slide['list_header'] . '</p>' : '';
            $list = $list_header . '<ul class="description">';
            foreach ($slide['list_items'] as $list_item) {
                $list .= '<li>' . $list_item . '</li>';
            }
            $list .= '</ul>';
        }

        $features = '';
        if (!empty($slide['features'])) {
            $features = '<div class="promo-features">';
            foreach ($slide['features'] as $feature) {
                $icon = $this->get_icon($feature['icon']);
                // Пояснение под заголовком есть не у всех акций.
                $text = empty($feature['text']) ? '' : '<div class="promo-feature-text">' . $feature['text'] . '</div>';
                $features .= <<<EOT
                    <div class="promo-feature">
                        <div class="promo-feature-icon">$icon</div>
                        <div class="promo-feature-title">{$feature['title']}</div>
                        $text
                    </div>
                    EOT;
            }
            $features .= '</div>';
        }

        $button_desktop = $this->render_button($slide, 'hide-for-small');
        $button_mobile = $this->render_button($slide, 'show-for-small');
        $image = $slide['image'];
        $image_alt = $slide['image_alt'];
        // Первый слайд — LCP главной: грузится сразу и первым (мимо lazy-load
        // Flatsome, см. ug_eager_img), остальные скрыты до клика — в конец очереди.
        $loading = $index === 0 ? ' fetchpriority="high"' : ' loading="lazy" fetchpriority="low"';
        // width/height — пропорции до загрузки картинки (CSS всё равно width:100%; height:auto).
        $size = self::image_size($image);
        $dims = $size ? ' width="' . $size[0] . '" height="' . $size[1] . '"' : '';
        // webp весит в семь раз меньше png, но png оставляем запасным вариантом.
        $picture = '<img src="' . $image . '" alt="' . $image_alt . '"' . $dims . $loading . '>';
        if ($index === 0) {
            $picture = ug_eager_img($picture);
        }
        if (!empty($slide['image_webp'])) {
            $picture = '<picture><source srcset="' . $slide['image_webp'] . '" type="image/webp">' . $picture . '</picture>';
        }

        return <<<EOT
            <div class="promo-slide$active$modifier" data-promo-slide="$index">
                <div class="main-banner-content">
                    <div class="left">
                        <$header_tag class="sub-header">$sub_header</$header_tag>
                        <p class="text-like-h1">$title</p>
                        $descriptions
                        $list
                        $features
                        $button_desktop
                    </div>
                    <div class="right">
                        $picture
                    </div>
                    $button_mobile
                </div>
            </div>
            EOT;
    }

    private function render_slider()
    {
        $slides = self::get_slides();
        $slides_html = '';
        foreach ($slides as $index => $slide) {
            $slides_html .= $this->render_slide($slide, $index);
        }

        // Стрелки показываем всегда — место под вторую акцию видно сразу.
        $arrow_left = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 4l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $arrow_right = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 4l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $arrows = <<<EOT
            <button type="button" class="promo-slider-arrow promo-slider-prev" data-promo-prev aria-label="Предыдущая акция">$arrow_left</button>
            <button type="button" class="promo-slider-arrow promo-slider-next" data-promo-next aria-label="Следующая акция">$arrow_right</button>
            EOT;

        return <<<EOT
            <div class="promo-slider" data-promo-slider>
                <div class="promo-slider-viewport">
                    $slides_html
                </div>
                $arrows
            </div>
            EOT;
    }

    public function generate_shortcode()
    {
        $slider = $this->render_slider();
        $html = <<<EOT
         [section id='new_main_first_screen' class='new_main_first_screen'  padding="0px"]
            <div class="first-screen-services-tabs show-for-small">
                <ul class="tabs">
                    <li><a href="/services/audit/">Аудит</a></li>
                    <li><a href="/services/bukhgalterskie-uslugi/">Бухгалтерия</a></li>
                    <li><a href="/services/licensing/">Лицензирование</a></li>
                    <li><a href="/services/register-elimination/">Регистрация</a></li>
                    <li><a href="/services/register-elimination/likvidatsiya-ooo/">Ликвидация</a></li>
                    <li><a href="/services/licensing/registracija-tovarnogo-znaka/">Товарный знак</a></li>
                    <li><a href="/services/services-le/">Юридические услуги</a></li>
                </ul>
            </div>
            <div class="first-screen">
                <div class="main-banner">
                    <div class="row">
                        <div class="col">
                            $slider
                        </div>
                    </div>
                </div>
                <div class="ratings">
                    [row style="collapse" width="full-width"]
                    [col span__sm="12"]
                    [row_inner_1 style="collapse" v_align="middle"]
                    [col_inner_1 span="4" span__sm="12" padding="0px 0px 0px 0px" margin="0px 0px 0px 0px"]
                    [featured_box img="13631" inline_svg="0" img_width="95" pos="left"]
                    <p class="laurel-place">ТОП-5</p>
                    <p class="laurel-icon-header">юридических компаний Новосибирска<sup style="font-size: 6pt;"> 3</sup></p>
                    <p class="laurels-icons-year">июнь, 2025</p>
                    [/featured_box]
                    [/col_inner_1]
                    [col_inner_1 span="4" span__sm="12"]
                    [featured_box img="13809" inline_svg="0" img_width="95" pos="left" ]
                    <p class="laurel-place">1-ое место</p>
                    <p class="laurel-icon-header">среди бухгалтерских компаний Новосибирска<sup style="font-size: 6pt;"> 2</sup></p>
                    <p class="laurels-icons-year">2023</p>
                    [/featured_box]
                    [/col_inner_1]
                    [col_inner_1 span="4" span__sm="12"]
                    [featured_box img="13809" inline_svg="0" img_width="95" pos="left"]
                    <p class="laurel-place">1-ое место</p>
                     <p class="laurel-icon-header">в номинации <br>«Корпоративное право»<sup style="font-size: 6pt;"> 1</sup></p>
                    <p class="laurels-icons-year">июнь, 2025</p>
                    [/featured_box]
                    [/col_inner_1]
            <div class="laurel-footnote">1 — по версии рейтинга журнала «Деловой квартал» 2 — по итогам рейтинга «Планка» (planka.ru) 3 — по версии журнала «Деловой квартал»;</div>
                    [/row_inner_1]

                    [/col]
                    [/row]
                </div>
            </div>
            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox"]
            [contact-form-7 id="13619" title="Форма на главной (узнать стоимость услуг)"]
            [/lightbox]

            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox-seminar"]
            [contact-form-7 id="080e72d" title="Форма для семинара"]
            [/lightbox]

            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightboxx"]
            [contact-form-7 id="4af4748" title="Форма на главной (узнать стоимость услуг)_copy"]
            [/lightbox]




         [/section]
        EOT;
        ug_enqueue_style('assets/css/blocks/new_main_first_screen.css');
        ug_enqueue_script('assets/js/blocks/main-first-screen-slider.js', ['jquery'], ['in_footer' => true, 'strategy' => 'defer']);
        return $html;
    }

}

function new_main_first_screen_shortcode($atts)
{
    $shortcode = new new_main_first_screen();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('new_main_first_screen', 'new_main_first_screen_shortcode');

/**
 * Preload картинки первого слайда: шорткод рендерится в body после
 * блокирующих CSS в <head> — без preload браузер узнаёт о LCP-картинке поздно.
 * Браузер берёт из <picture> webp, его и грузим.
 *
 * Только на ширине от 850px (граница hide-for-medium Flatsome): там LCP —
 * эта картинка. На телефоне LCP — текст первого экрана, а картинка 280px
 * и так грузится тегом <img fetchpriority="high"> из разметки; ранний
 * preload там только отнимает канал у CSS и шрифтов до первой отрисовки.
 */
add_action('wp_head', function () {
    if (!is_singular() || !has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'new_main_first_screen')) {
        return;
    }
    $slide = new_main_first_screen::get_slides()[0] ?? null;
    if (!$slide) {
        return;
    }
    $webp = !empty($slide['image_webp']);
    printf(
        '<link rel="preload" as="image" href="%s"%s media="(min-width: 850px)" fetchpriority="high">' . "\n",
        esc_url($webp ? $slide['image_webp'] : $slide['image']),
        $webp ? ' type="image/webp"' : ''
    );
}, 2);
