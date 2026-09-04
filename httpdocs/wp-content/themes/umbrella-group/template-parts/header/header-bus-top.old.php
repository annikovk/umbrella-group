<style>
.umbrela_sticky_menu {
    position: fixed;
    top: -43px;
    z-index: 99;
    left: 0;
    right: 0;
    background: #fff;
    transition: all 0.3s linear;
}
.umbrela_sticky_menu.vis-menu {
    top: 0;
}
ul.umbrela_sticky_list {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    list-style: none;
   margin-right: 41px;
}
ul.umbrela_sticky_list li {
  margin-bottom: 0;
}
ul.umbrela_sticky_list li a {
    font-weight: 700;
    font-size: 14px;
    line-height: 140%;
    color: #1A1A1A;
    display: block;
    padding: 8px 12px;
}
.wrap_sticky_menu_top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sticky_menu-phone-top-bus a {
    font-weight: 700;
    font-size: 18px;
    line-height: 27px;
    color: #1A1A1A;
}

@media (max-width: 768px) {
    .umbrela_sticky_menu {
        display: none;
    }
}
</style>
<div class="umbrela_sticky_menu">
    <div class="container">
        <div class="wrap_sticky_menu_top">
            <ul class="umbrela_sticky_list">
                <li>
                    <a href="#">Как помогаем</a>
                </li>
                <li>
                    <a href="#">Сколько стоит открыть дело</a>
        </li>
        <li>
          <a href="#">Кейсы</a>
        </li>
        <li>
          <a href="#">Отзывы</a>
        </li>
        <li>
          <a href="#">Команда</a>
        </li>
        <li>
          <a href="#">Контакты</a>
        </li>
      </ul>
      <div class="sticky_menu-phone-top-bus">
        <a href="tel:+73832021582" class="">+7 (383) <span class="phoneShow" phonevalue="+7 (383) 202-15-82" style="cursor:pointer; border-bottom: 1px dashed;">Показать</span></a>
      </div>
      <div id="umbrella-menu-button-open-bus">
        <a href="#header-contact-form-lightbox">Расчёт открытия  бизнеса</a>
      </div>
    </div>
  </div>
</div>
<?php if(flatsome_has_top_bar()['large_or_mobile']){ ?>
<div id="top-bar" class="header-top <?php header_inner_class('top'); ?>">
    <div class="flex-row container">
      <div class="flex-col hide-for-medium flex-left">
        <ul class="nav nav-left medium-nav-center nav-small  nav-">
          <li id="menu-item-10546" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-10546 menu-item-design-default has-dropdown"><a href="https://taxlab.ru/about/" class="nav-top-link">О компании<i class="icon-angle-down"></i></a>
            <ul class="sub-menu nav-dropdown nav-dropdown-default" style="">
              <li id="menu-item-10559" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10559"><a href="https://taxlab.ru/about/team/">Команда</a></li>
              <li id="menu-item-10558" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10558"><a href="https://taxlab.ru/about/cases/">Кейсы</a></li>
              <li id="menu-item-10560" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10560"><a href="https://taxlab.ru/about/feedback/">Отзывы</a></li>
              <li id="menu-item-10561" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10561"><a href="https://taxlab.ru/about/clients/">Клиенты</a></li>
              <li id="menu-item-10562" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10562"><a href="https://taxlab.ru/blog/">Блог</a></li>
              <li id="menu-item-10557" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10557"><a href="https://taxlab.ru/about/dokumenty-kompanii/">Документы компании</a></li>
              <li id="menu-item-10563" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10563"><a href="https://taxlab.ru/about/contacts/">Контакты</a></li>
            </ul>
          </li>
          <li id="menu-item-10551" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10551 menu-item-design-default"><a href="https://taxlab.ru/about/contacts/" class="nav-top-link">Контакты</a></li>
        </ul>
      </div>
    </div>
</div>
<?php } ?>
