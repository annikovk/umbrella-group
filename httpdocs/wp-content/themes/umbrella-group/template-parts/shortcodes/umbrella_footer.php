<?php

class umbrella_footer
{
    public $atts;

    public function generate_shortcode()
    {
        $why_choose_us = do_shortcode('[why_choose_us]');
        $html = <<<EOHTML
            [section label="Feedback" padding="0px"]
                [umbrella_feedback ab_test="umbrella_ab_test9_variant2"]
            [/section]
            $why_choose_us
            [section label="contact-us" padding="0px" class="footer-top-section"]
                [row style="collapse" padding="86px 0px 0px 0px"]
                    [col span__sm="12"]
                        [row_inner width="full-width" h_align="center"]
                            [col_inner span="4" span__sm="12" align="left"]
                                [wbcr_php_snippet id="9123"]
                            [/col_inner]
                            [col_inner span="4" span__sm="12" align="left" class="footer-form-column"]
                                [contact-form-7 id="639"]
                            [/col_inner]
                            [col_inner span="4" span__sm="12" padding="0px 0px 0px 70px" align="left"]
                                <div class="footer-top-right hide-for-medium">
                                    <p>Оставьте заявку или звоните — <br />с 09:00 до 18:00 на связи:</p>
                                    <p class="footer-h3 sub-header"><a class="footer-a" href="tel: +73833731717">+7 (383) 373-17-17</a></p>
                                    <p>Круглосуточно на почту:</p>
                                    <p class="footer-h3 sub-header"><a class="footer-a" href="mailto:contact@taxlab.ru" > contact@taxlab.ru</a></p>
                                </div>
                            [/col_inner]
                        [/row_inner]
                    [/col]
                [/row]
            [/section]
            [section bg_color="#d7d7d7" dark="true" class="footer-bottom"]
                [row style="collapse" padding="20px 0px 0px 0px"]
                    [col span__sm="12"]
                        [row_inner width="full-width" h_align="center"]
                            [col_inner span="4" span__sm="10"]
                            <p><img src="https://taxlab.ru/wp-content/uploads/logo-2020-new.svg" alt="Umbrella Group" class="int-logo"></p>
                                [gap height="39px"]
                            <div>
                                <span itemscope itemtype="http://schema.org/Organization"><br />
                                    <meta itemprop="name" content="Taxlab">
                                    <meta itemprop="telephone" content="+7 (383) 373-17-17" >
                                    <meta itemprop="email" content="contact@taxlab.ru" >
                                    <p class="footer-sub-text" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                                        <span itemprop="postalCode">630099</span>, г. 
                                        <span itemprop="addressLocality">Новосибирск</span>, <br /> 
                                        <span itemprop="streetAddress">ул. Максима Горького, д. 34</span><br />ул. площадь Труда, д. 1
                                    </p>
                                </span>
                              </div>
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
                                    <p><a href="mailto:contact@taxlab.ru" >contact@taxlab.ru </a></p>
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
        EOHTML;
        if (get_the_ID() == 13514 || get_the_ID() == 13516) {
            $html = <<<EOHTML
                [section bg_color="#d7d7d7" dark="true" class="footer-bottom"]
                [row style="collapse" padding="20px 0px 0px 0px"]
                    [col span__sm="12"]
                        [footer_map]
                        [row_inner width="full-width" h_align="center"]
                            [col_inner span="3" span__sm="10"]
                            <p><img src="https://taxlab.ru/wp-content/uploads/logo-2020-new.svg" alt="Umbrella Group" class="int-logo"></p>
                            <p class="footer-sub-text">1990 - 2022 © Umbrella Group</p>
                            [/col_inner]
                            [col_inner span="2" span__sm="12" visibility="hide-for-medium"]
                                [wbcr_php_snippet id="701" name="top-bar-menu" class="footer-menu"]
                            [/col_inner]
                            [col_inner span="3" span__sm="12" visibility="hide-for-medium"]
                                [wbcr_php_snippet id="701" name="main-menu" class="footer-menu"]
                            [/col_inner]
                            [col_inner span="4" span__sm="10"]
                                [ux_text class="int-dark-text"]
                                <p style="font-size: 14px">
                                    Общество с ограниченной ответственностью ОБЪЕДИНЕНИЕ «РЕГИСТРАЦИЯ»<br/>
                                    <br/>
                                    Генеральный директор: Климов Александр Владимирович<br/>
                                    Юридический адрес: 630108, г. Новосибирск,<br/>
                                    ул. площадь Труда, д. 1, офис 102<br/>
                                    Расчетный счет, ПАО Сбербанк: 40702810844050032212<br/>
                                    Корреспондентский счет: 30101810500000000641<br/>
                                    БИК: 045004641<br/>
                                    ИНН/КПП 5404032783/540401001<br/>
                                    ОГРН: 1165476088777<br/>
                                </p>
                                [/ux_text]
                            [/col_inner]
                        [/row_inner]
                    [/col]
                [/row]
            [/section]
            EOHTML;


        }

//        umbrella_add_custom_css_files(['/assets/css/blocks/umbrella_footer.css']);
//        umbrella_add_custom_js_files(["/assets/js/blocks/umbrella_footer.js"]);
        return $html;
    }
}

function umbrella_footer_shortcode($atts)
{
    $shortcode = new umbrella_footer();
    $shortcode->atts = $atts;
    return do_shortcode($shortcode->generate_shortcode());
}

add_shortcode('umbrella_footer', 'umbrella_footer_shortcode'); ?>