<?php

class SU_welcome_screen
{
    private $css_file = ['/assets/css/blocks/su-welcome-screen.css'];
    public $err = "<ul style='padding: 24px;'> Неправильное использование шорткода: ";

    public $atts;
    private $offer;
    private $background;
    private $icon;
    private $first_advantage = "<strong>Бесплатная</strong> консультация онлайн";
    private $second_advantage;
    private $third_advantage;
    private $button_type;
    private $button_variants = ['free_consulting', 'price_calc', 'ask_question'];
    private $phone='73833731717';
    private $phone_text;
    private $price;
    private $specialist_text;

    // === Список URL, на которых НЕ нужно выводить номер телефона (текст остаётся) ===
    private $hide_phone_urls = [
        '/services/licensing/skidka-15-procentov-na-tovarny-znak/',
    ];


    public function fill_attributes()
    {
        $err_initial_len = strlen($this->err);
        $this->fill_variable('button', $this->button_type, true, 'Тип кнопки', implode('|', $this->button_variants));
        if (!in_array($this->button_type, $this->button_variants)) {
            $this->err .= "<li> Тип кнопки задан неправильно. Выберите один из вариантов кнопки: <span id='codebox'>button=" . implode('|', $this->button_variants) . "</span></li>";
        }
        $this->fill_variable('offer', $this->offer, true, 'Подзаголовок-оффер', 'Устраним все замечания к налоговой отчетности, своевременно представим корректирующие формы, восстановим первичные документы предприятия');
        $this->fill_variable('background', $this->background, true, 'Фоновое изображение', '9108');
        $this->fill_variable('icon', $this->icon, true, 'Иконка', '2252');
        $this->fill_variable('first_advantage', $this->first_advantage, false, 'Первое преимущество', '<strong>Бесплатная</strong> консультация онлайн');
        if (str_contains($this->first_advantage,"Бесплатная") && !str_contains("<strong>",$this->first_advantage)) {
            $this->first_advantage=str_replace("Бесплатная","<strong>Бесплатная</strong>",$this->first_advantage);
        }
        $this->fill_variable('second_advantage', $this->second_advantage, true, 'Второе преимущество', '\'Риски застрахованы на сумму более 50 млн рублей.\'');
        if (str_contains($this->second_advantage,"Гарантия") && !str_contains("<strong>",$this->second_advantage)) {
            $this->second_advantage=str_replace("Гарантия","<strong>Гарантия</strong>",$this->second_advantage);
        }
        $this->fill_variable('third_advantage', $this->third_advantage, true, 'Третье преимущество', '\'Подадим документы за 7 дней\'');
        $this->fill_variable('phone', $this->phone, false, 'Телефон', '73832021582');
        if (!(strlen($this->phone) == 11 && intval($this->phone, 10) > 70000000000)) {
            $this->err .= "<li> Формат телефона задан неверно. Используйте следующий формат: <span id='codebox'>73832021582</span></li>";
        }
        $this->fill_variable('phone_text', $this->phone_text, true, 'Текст перед телефоном', '\'Для бесплатной консультации по услуге отправьте форму с сайта или звоните \'');
        $this->fill_variable('specialist_text', $this->specialist_text, true, 'Текст со специалистом', '\'Вам ответит наш {специалист} с опытом работы более 5 лет\'');
        $this->fill_variable('price', $this->price, false, 'Цена', '');

        if (strlen($this->err) > $err_initial_len) {
            $this->err .= '</ul>';
            return false;
        } else {
            return true;
        }
    }

    private function fill_variable($attribute, &$variable, $obligatory, $name, $sample_value)
    {
        if (isset($this->atts[$attribute])) {
            $variable = $this->atts[$attribute];
        } elseif ($obligatory) {
            $this->err .= <<<EOHTML
                <li> <strong>$name</strong> не задан. Используйте атрибут <span id='codebox'>$attribute</span>. Например <span id='codebox'>$attribute=$sample_value</span> </li>
                EOHTML;
        }
    }


    private function get_button(): string
    {
        return match ($this->button_type) {
            'free_consulting' => '[button text="Бесплатная консультация" link="#leave-request-contact-form-lightbox"]',
            'price_calc' => '[button text="Расчёт стоимости" link="#calculate-price-contact-form-lightbox"]',
            'ask_question' => '[button text="Задать вопрос" link="#ask-question-contact-form-lightbox"]',
            default => '',
        };
    }

    // попап формы для кнопки: один на блок, хотя кнопок две (мобильная сверху, десктопная снизу)
    private function get_lightbox(): string
    {
        switch ($this->button_type) {
            case 'price_calc':
                return <<<EOHTML

                        [lightbox id="calculate-price-contact-form-lightbox" width="400px" padding="0px"]
                            [contact-form-7 id="9965" title="Форма Расчёт стоимости услуги"]
                        [/lightbox]
                       EOHTML;
            case 'ask_question':
                return <<<EOHTML

                        [lightbox id="ask-question-contact-form-lightbox" width="400px" padding="0px"]
                            [contact-form-7 id="9967" title="Форма Задать вопрос"]
                        [/lightbox]
                       EOHTML;
        }
        return '';
    }

    // фон первого экрана — LCP страниц услуг: сразу, с высоким приоритетом и размерами
    private function get_background(): string
    {
        $src = wp_get_attachment_image_src($this->background, 'full');
        $size = ($src && $src[1] && $src[2]) ? ' width="' . $src[1] . '" height="' . $src[2] . '"' : '';
        return ug_eager_img('<img src="' . esc_url($src[0] ?? '') . '"' . $size . ' loading="eager" fetchpriority="high" decoding="async"  />');
    }

    private function get_specialist_text()
    {
        // прежде — strpos(REQUEST_URI, "services/licensing/") и т.п.: со слэшем
        // на конце, поэтому макет licensing-maket1 сюда не попадал
        $specialist = match (ug_current_service_section()) {
            'bukhgalterskie-uslugi' => "ведущий бухгалтер",
            'licensing' => "специалист по лицензированию",
            'services-le' => "юрист",
            default => "специалист",
        };

        return str_replace('{специалист}', $specialist, $this->specialist_text);
    }

    public function generate_shortcode()
    {
        $path = ug_request_path();
        $html = ug_render('su-welcome-screen', [
            'breadcrumbs' => umbrella_draw_header_with_breadcrumbs(),
            'background' => $this->get_background(),
            'icon' => '<img src="' . esc_url(wp_get_attachment_image_src($this->icon, 'full')[0] ?? '') . '"  />',
            // [button] Flatsome на каждый link="#…" ищет страницу get_page_by_title —
            // кнопка рендерится один раз, а выводится в двух местах
            'button' => do_shortcode($this->get_button()),
            'lightbox' => $this->get_lightbox(),
            'flex_button' => $path === '/services/licensing/litsenziya-na-meditsinskuyu-deyatelnost/',
            'offer' => $this->offer,
            'advantages' => [$this->first_advantage, $this->second_advantage, $this->third_advantage],
            'price' => $this->price,
            'phone' => $this->phone,
            'phone_text' => $this->phone_text,
            // на страницах из списка — только текст, без номера
            'show_phone' => !in_array($path, $this->hide_phone_urls, true),
            'specialist_text' => $this->get_specialist_text(),
            'ratings' => [umbrella_get_ratings('expert'), umbrella_get_ratings('header')],
        ]);
        umbrella_add_custom_css_files($this->css_file);
        return $html;
    }
}
function su_welcome_screen_shortcode($atts)
{
    $shortcode = new SU_welcome_screen();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('su_welcome_screen', 'su_welcome_screen_shortcode');

function umbrella_get_ratings($type='')
{
    switch ($type) {
        case 'expert':
            $icon = '/wp-content/uploads/rating-expert-icon.png';
            $header = '2 место рейтинга консалтинговых агенств Сибири, 2009-2013';
            return ['icon'=>$icon,'header'=> $header,'subheader'=> ""];
        case 'expert-header':
            $icon = '/wp-content/uploads/manual_uploads/pravo-300.png';
            $header = 'Топ юридических компаний по версии pravo.ru';
            return ['icon'=>$icon,'header'=> $header,'subheader'=> ""];
        default:
            switch (ug_current_service_section()) {
                case 'audit':
                    $header = 'Финалист конкурса';
                    $icon = '/wp-content/uploads/2019/10/laurel-nsk.svg';
                    $subheader = '«Комплексное предоставление услуг в сфере консалтинга»';
                    break;
                case 'bukhgalterskie-uslugi':
                    $header = '2 место в отрасли «Налоговое право»';
                    $icon = '/wp-content/uploads/2019/10/laurel-2.svg';
                    $subheader = 'Деловой Квартал, Новосибирск, 2021';
                    break;
                case 'services-le':
                    $header = 'ТОП юридических компаний в Сибири';
                    $icon = '/wp-content/uploads/2019/10/laurel-1.svg';
                    $subheader = 'Новосибирск, pravo.ru, 2021';
                    break;
                default: // в т.ч. licensing — та же плашка, что по умолчанию
                    $header = '1 место в отрасли «Корпоративное право»';
                    $icon = '/wp-content/uploads/2019/10/laurel-1.svg';
                    $subheader = 'рейтинг «Делового квартала», Новосибирск, 2021';
            }
    }
    if ($type=='header') {
        $icon = str_replace('laurel-1.svg','laurel-1-gray.svg',$icon);
        $icon = str_replace('laurel-2.svg','laurel-2-gray.svg',$icon);
        $icon = str_replace('laurel-nsk.svg','laurel-nsk-gray.svg',$icon);
    }
    return ['icon'=>$icon,'header'=> $header,'subheader'=> $subheader];
}
/**
 * Preload фона первого экрана: шорткод рендерится в body, а <head> перед ним
 * тянет блокирующие CSS — без preload браузер узнаёт о LCP-картинке поздно
 * (Lighthouse: Load Delay ≈ 3 с на mobile). Атрибут background — ID вложения.
 */
add_action('wp_head', function () {
    if (!is_singular()) {
        return;
    }
    $content = (string) get_post_field('post_content', get_queried_object_id());
    if (!has_shortcode($content, 'su_welcome_screen')
        || !preg_match('/\[su_welcome_screen\s[^\]]*?background=[\'"]?(\d+)/', $content, $m)) {
        return;
    }
    $src = wp_get_attachment_image_src((int) $m[1], 'full');
    if ($src) {
        echo '<link rel="preload" as="image" href="' . esc_url($src[0]) . '" fetchpriority="high">' . "\n";
    }
}, 2);
