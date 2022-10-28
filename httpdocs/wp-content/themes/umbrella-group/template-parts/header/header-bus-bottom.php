<?php if(flatsome_has_bottom_bar()['large_or_mobile']) {
?>
<style>
#umbrella-menu-button>a {
    font-size: 14px;
    color: #fff;
    padding-top: 9px;
    height: 100%;
    width: 100%;
    position: relative;
    display: inline-block;
}
#umbrella-menu-button{
    cursor:pointer;
    position: absolute;
    width: 150px;
    height: 40px;
    background-color: #b2b2b2;
    margin: 0;
    right: 0;
    top: -20px;
    text-align: center;
    font-weight: normal;

background-image: linear-gradient(#ec1d23, #ec1d23);
    background-position: 50% 50%;
    background-repeat: no-repeat;
    background-size: 0% 100%;
    transition: background-size .2s, color .2s;
}
#umbrella-menu-button:hover{
background-size: 100% 100%;
}
div#umbrella-menu-button-open-bus a {
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    line-height: 27px;
}
div#umbrella-menu-button-open-bus {
    background: #EC1C23;
    padding: 8px 18px;
    display: flex;
    align-items: center;
    text-align: center;
    max-width: 222px;
    margin-left: auto;
}
</style>
<div id="wide-nav" class="header-bottom wide-nav <?php header_inner_class('bottom'); ?>">
    <div class="flex-row container">

            <?php if(get_theme_mod('header_elements_bottom_left') || get_theme_mod('header_elements_bottom_right')){ ?>
            <div class="flex-col hide-for-medium flex-left">
                <ul class="nav header-nav header-bottom-nav nav-left <?php flatsome_nav_classes('bottom'); ?>">
                    <?php flatsome_header_elements('header_elements_bottom_left','nav_position_text'); ?>
                </ul>
            </div>
            <?php } ?>

            <?php if(get_theme_mod('header_elements_bottom_center')){ ?>
            <div class="flex-col hide-for-medium flex-center">
                <ul class="nav header-nav header-bottom-nav nav-center <?php flatsome_nav_classes('bottom'); ?>">
                    <?php flatsome_header_elements('header_elements_bottom_center','nav_position_text'); ?>
                </ul>
            </div>
            <?php } ?>

            <?php if(get_theme_mod('header_elements_bottom_right') || get_theme_mod('header_elements_bottom_left')){ ?>
            <div class="flex-col hide-for-medium flex-right flex-grow">
              <ul class="nav header-nav header-bottom-nav nav-right <?php flatsome_nav_classes('bottom'); ?>">
                               <div id="umbrella-menu-button-open-bus">
                                <a href="#header-contact-form-lightbox" >Расчёт открытия  бизнеса</div></a>

              </ul>
            </div>
            <?php } ?>

            <?php if(get_theme_mod('header_mobile_elements_bottom')) { ?>
              <div class="flex-col show-for-medium flex-grow">
                  <ul class="nav header-bottom-nav nav-center mobile-nav <?php flatsome_nav_classes('bottom'); ?>">
                      <?php flatsome_header_elements('header_mobile_elements_bottom'); ?>
                  </ul>
              </div>
            <?php } ?>

    </div>
</div>
<?php } ?>

<?php do_action('flatsome_after_header_bottom'); ?>
