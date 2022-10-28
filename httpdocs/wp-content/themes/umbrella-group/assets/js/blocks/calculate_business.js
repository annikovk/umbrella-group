jQuery(document).on('click', '.dynamic_tariff_sub_menu li', function(){
  jQuery('.dynamic_tariff_sub_menu li').removeClass('active');
  jQuery('.dynamic_tariff_menu li').removeClass('current-menu-parent');
  jQuery(this).parents('li').addClass('current-menu-parent');
  jQuery('ul.dynamic_tariff_menu').toggleClass('expanded');
  jQuery(this).addClass('active');
  var tab_id = jQuery(this).attr('data-tab');
  jQuery('.tab-content').removeClass('current');
  jQuery(this).addClass('current');
  jQuery('#'+tab_id).addClass('current');
});

