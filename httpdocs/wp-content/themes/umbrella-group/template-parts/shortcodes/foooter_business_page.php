<?php
function business_footer_shortcode($atts)
{
    $html = <<<EOTHTML
            [section bg_color="#d7d7d7" dark="true" class="footer-bottom footer_bus"]
                [row style="collapse" padding="20px 0px 0px 0px"]
                    [col span__sm="12"]
                        [row_inner width="full-width" h_align="center"]
                            [col_inner span="4" span__sm="10"]
                            <p><img src="https://taxlab.ru/wp-content/uploads/logo-2020-new.svg" alt="Umbrella Group" class="int-logo"></p>
                                [gap height="39px"]
                            <p><span itemscope itemtype="http://schema.org/Organization"><br /><meta itemprop="name" content="Taxlab"></p>
                                <p class="footer-sub-text" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="postalCode">630099</span>, г. <span itemprop="addressLocality">Новосибирск</span>, <br /> <span itemprop="streetAddress">ул. Максима Горького, д. 34</span><br />ул. площадь Труда, д. 1</p>
                                <p><meta itemprop="telephone" content="+7 (383) 230-97-45" ><br /><meta itemprop="email" content="contact@taxlab.ru" ><br />
                                </span></p>
                            [gap height="39px"]<p class="footer-sub-text">1990 - 2022 © Umbrella Group</p>[/col_inner]
                            [col_inner span="2" span__sm="12" visibility="hide-for-medium"]
                                [wbcr_php_snippet id="701" name="top-bar-menu" class="footer-menu"]
                            [/col_inner]
                            [col_inner span="3" span__sm="12" visibility="hide-for-medium"]
                                [wbcr_php_snippet id="701" name="main-menu" class="footer-menu"]
                            [/col_inner]
                            [col_inner span="3" span__sm="10"]
                                [ux_text class="int-dark-text"]
                                    <p><a href="tel:+73833731717">+7 (383) 373-17-17 </a></p>
                                    <p><a href="mailto:main.contact@taxlab.ru">main.contact@<span class="mailShow" mailvalue="main.contact@taxlab.ru" style="cursor:pointer; border-bottom: 1px dashed;">Показать</span></a></p>
                                    <div class="social-icons">
                                        <a href="https://www.facebook.com/umbrellagroup.nsk"rel="noopener noreferrer nofollow" class="umbrella-follow-icon facebook " data-label="Facebook"><i class="icon-facebook"></i></a>
                                    </div>
                                [/ux_text]
                            [/col_inner]
                        [/row_inner]
                    [/col]
                [/row]
            [/section]
            [divider width="100%" height="1px" margin="0px" color="rgb(68, 69, 69)"]
        EOTHTML;
    umbrella_add_custom_css_files(['/assets/css/blocks/footer_business_page.css']);
    umbrella_add_custom_js_files(["/assets/js/blocks/footer_business_page.js"]);
    return $html;
}

add_shortcode('business_footer', 'business_footer_shortcode');