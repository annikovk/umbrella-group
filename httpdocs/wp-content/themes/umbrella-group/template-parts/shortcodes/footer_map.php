<?php

class footer_map
{
    public $atts;

    public function generate_shortcode()
    {
        $html = <<<EOHTML
                <div class="position_footer_map">
                <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ad94c7e4f49b0ec7735c9c04c0c8709989b68443c6f4cb399f58b87b289e084b5&amp;source=constructor" width="100%" height="453" frameborder="0"></iframe>
                <div class="contact-info_footer_map card-shadow-common_footer_map">
                <div>
                    <div class="main-text-common">C 09:00 до 18:00 на связи:</div>
                    <a  class="contact_link" href=”tel:+7383111111”>+ 7 (383) xx xx xx</a>
                </div>
                <div>
                    <div class="main-text-common">Круглосуточно по почте:</div>
                    <a  class="contact_link" href="mailto:info.contact@gmail.com"> info.contact@gmail.com</a>
                </div>
                  <div>
                        г. Новосибирск, 
                        <ul>
                            <li>ул. Максима Горького, д. 34</li>
                            <li>ул. площадь Труда, д. 1</li>
                        </ul>
                    </div>
                </div>
                </div>
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