<?php

class why_umbrella
{
    public $atts;

    private const RUTUBE_ID = '8a3e704fa59f88bb08442e29a291fba9';
    // постер ролика из API rutube (/api/video/<id>/ → thumbnail_url)
    private const POSTER = 'https://pic.rtbcdn.ru/video/2025-07-24/ed/53/ed535f661cf9f5699340fc0757e17f20.jpg';

    /**
     * Фасад видео: тот же <iframe> с теми же размерами, но вместо плеера rutube
     * (≈ 730 КБ сразу при загрузке страницы) — srcdoc с постером и кнопкой;
     * клик переводит фрейм на плеер. Бокс не меняется — разметка вокруг та же.
     */
    private function get_video(): string
    {
        $player = 'https://rutube.ru/play/embed/' . self::RUTUBE_ID . '/?autoplay=true';
        $srcdoc = '<style>*{margin:0;padding:0}html,body{height:100%;overflow:hidden;background:#000}'
            . 'a{position:relative;display:block;height:100%}img{width:100%;height:100%;object-fit:cover}'
            . 'span{position:absolute;top:50%;left:50%;width:80px;height:56px;margin:-28px 0 0 -40px;border-radius:12px;background:rgba(0,0,0,.7)}'
            . 'span:after{content:"";position:absolute;left:32px;top:16px;border-style:solid;border-width:12px 0 12px 20px;border-color:transparent transparent transparent #fff}'
            . 'a:hover span,a:focus span{background:#EC1C23}</style>'
            . '<a href="' . esc_url($player) . '" aria-label="Смотреть видео"><img src="' . esc_url(self::POSTER) . '" alt=""><span></span></a>';
        // ядро считает iframe без loading в счётчик медиа контента (порог loading=lazy
        // для картинок ниже); у фасада loading задан явно — счёт тот же, что был
        wp_increase_content_media_count();
        return '<iframe width="1200" height="405" title="Почему ГК Umbrella Group — рассказывает Александр Климов" loading="lazy" srcdoc="'
            . esc_attr($srcdoc) . '" frameBorder="0" allow="clipboard-write; autoplay" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
    }

    public function generate_shortcode()
    {
        $video = $this->get_video();
        // Блок только для десктопа: раньше на мобильных UA (isMobile()) шорткод
        // возвращал пусто, теперь один DOM для всех и hide-for-medium (≤ 849 px).
        $html = <<<EOHTML
         [section id='why_umbrella'  padding="0px" class="hide-for-medium"]
           [row]
            [col  span="12" span__sm="12" margin="0px 0px 0px 0px"]
            <div class="block-padding_why-umbrella main-container_why-umbrella">
            <div class="vide_ceo">
               $video
                </div>
                <div class="card_why-umbrella card-shadow-common card-shadow_none_why-umbrella">
                    <div class="title-h1-text-common title_why_umbrella">Почему ГК <span class="show-for-small"><br/></span>Umbrella Group?</div>
                    <div class="citation_why-umbrella">
                        <div class="layout-author-quote_why-umbrella">
                            <img src="https://taxlab.ru/wp-content/uploads/Author_quote.png" 
                                 alt="Author_quote.png">
                            <div class="main-text-common">
                            <div class="time-speak">3 минуты</div>
                                Рассказывает <span class="main-text-accent-common">Александр Климов,</span>
                                <br>генеральный директор Umbrella Group
                            </div>
                        </div>
                        <div class="layout-quote_why-umbrella">
                            <div class="quote_why-umbrella">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.31527 8.64L15.0753 0.439999L16.0753 1.2L11.8353 8.36L16.3153 15.92L15.3553 16.72L8.31527 8.64ZM0.555267 8.64L9.07527 0.359999L10.1153 1.16L4.51527 8.4L10.3553 15.96L9.31527 16.8L0.555267 8.64Z" fill="#EC1C23"/>
                                </svg>
                            </div>
                            <div class="main-text-italic-common title_why_umbrella">
                                Помогая российскому бизнесу родиться, жить, расти и&nbsp;развиваться,
                                мы&nbsp;помогаем России в&nbsp;целом, улучшая качество жизни каждого в&nbsp;отдельности.
                            </div>
                            <div class="quote_why-umbrella position-down_why-umbrella">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.65574 16.72L0.695736 15.92L5.17574 8.56L0.895736 1.2L1.89574 0.439999L8.69574 8.64L1.65574 16.72ZM7.69574 16.8L6.65574 15.96L12.4557 8.6L6.85574 1.16L7.89574 0.359999L16.4157 8.64L7.69574 16.8Z" fill="#EC1C23"/>
                                </svg>
                            </div>
                        </div>
                   </div>
                   <div class="layout-thesis_why-umbrella">
                       <div class="number-thesis_why-umbrella">1</div>
                       <div class="main-text-common">Оправдываем доверие наших клиентов, ведь в&nbsp;нём&nbsp;&mdash; ключ к&nbsp;успеху.</div>
                       <div class="number-thesis_why-umbrella">2</div>
                       <div class="main-text-common">Мы&nbsp;знаем свое дело и&nbsp;любим свою работу.</div>
                       <div class="number-thesis_why-umbrella">3</div>
                       <div class="main-text-common">Более 35&nbsp;лет помогаем предпринимателям развивать бизнес и&nbsp;делать его более эффективным.</div>
                   </div>
                </div>
            </div>
            [/col]
           [/row]
         [/section]
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/why_umbrella.css']);
        umbrella_add_custom_js_files(["/assets/js/blocks/why_umbrella.js"]);
        return $html;
    }

}

function why_umbrella_shortcode($atts)
{
    $shortcode = new why_umbrella();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('why_umbrella', 'why_umbrella_shortcode');
