<li class="header-contact-wrapper">

    <?php
    $class = 'has-icon';
    $icon_size = flatsome_option('contact_icon_size');
    $class_link = 'tooltip';
    $nav = 'nav-divided nav-uppercase';
    $label = true;

    if (flatsome_option('contact_style') == 'icons') {
        $label = false;
    }

    if (flatsome_option('contact_style') == 'top') {
        $class .= ' icon-top';
    }
    ?>
    <div id="header-contact-mobile" class="nav <?php echo $nav; ?> header-contact hide-for-medium">
        <div></div>
        <div class="mobile-header-divider" ></div>
        <div class="has-icon">
            <i class="icon-phone"></i>
            <a href="tel:+73833731717" class="<?php echo $class_link; ?>">
                +7 (383) 373-17-17
            </a>
        </div>
        <div class="has-icon">
             <i class="icon-envelop"></i>
             <a href="mailto:contact@taxlab.ru" class="<?php echo $class_link; ?>">
                contact@taxlab.ru
            </a>
        </div>
        <div class="mobile-header-divider"></div>
        <div class="has-icon">

            <p><i class="icon-clock"></i> 09:00-18:00</p>
        </div>
        <div class="has-icon">
            <div class="header-contact-location">
                <div class="header-contact-location-city"> <i class="icon-map-pin-fill"></i> Новосибирск</div>
                <div class="header-contact-location-addresses">
                    — Максима Горького, 34<br/>
                    — Площадь Труда, 1 - офис 102
                </div>
            </div>

        </div>
        <div id="umbrella-menu-button"><a href="#header-contact-form-lightbox" >Обратный звонок</div></a>
    </div>

</li>