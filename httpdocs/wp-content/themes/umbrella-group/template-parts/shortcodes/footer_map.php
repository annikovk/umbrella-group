<?php

class footer_map
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
                <div class="position_footer_map" itemscope="" itemtype="http://schema.org/Organization">
            <meta itemprop="name" content="Группа компаний «Umbrella Group»">
            <meta itemprop="telephone" content="+73833731717">
            <meta itemprop="email" content="contact@taxlab.ru">
                <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ad94c7e4f49b0ec7735c9c04c0c8709989b68443c6f4cb399f58b87b289e084b5&amp;source=constructor" width="100%" height="453" frameborder="0">
                </iframe>
            <div class="contact-info_footer_map card-shadow-common_footer_map" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                <div>
                <div class="main-text-common">C 09:00 до 18:00 на связи:</div>
                <a  class="contact_link" href=”tel:+73833731717”>+ 7 (383) xx xx xx</a>
                </div>
                <div>
                <div class="main-text-common">Круглосуточно по почте:</div>
                <a  class="contact_link" mailvalue="contact@taxlab.ru" href="contact@taxlab.ru"> contact@taxlab.ru</a>
                </div>
                <div class="main-text-common">
                <span itemprop="addressLocality">г. Новосибирск </span> ,
                <ul>
                <li test1 itemprop="streetAddress">ул. Максима Горького, д. 34</li>
                <li>ул. Площадь Труда, д. 1</li>
                </ul>
                </div>
            </div>
        </div>

        <script type="application/ld+json"> {
        "@context": "http://schema.org",
        "@type": "LocalBusiness",
        "name": "Группа компаний «Umbrella Group»",
        "telephone": "+73833731717",
        "email": "contact@taxlab.ru",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "ул. Максима Горького, д. 34",
            "addressLocality": "Новосибирск",
            "addressRegion": "Новосибирская область",
            "addressCountry": "Россия",
            "postalCode": "630099"
        },
        "openingHoursSpecification": [{
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "09:00",
            "closes": "18:00"
            }]
        } 
        </script>
        EOHTML;
        umbrella_add_custom_css_files(['/assets/css/blocks/footer_map.css']);
        umbrella_add_custom_js_files(['/assets/js/blocks/footer_map.js']);
        return $html;
    }

}

function footer_map_shortcode($atts)
{
    $shortcode = new footer_map();
    $shortcode->atts = $atts;
    return do_shortcode($shortcode->generate_shortcode());
}

add_shortcode('footer_map', 'footer_map_shortcode');

?>