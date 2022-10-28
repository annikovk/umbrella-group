var header = jQuery(".umbrela_sticky_menu");
var scrollChange = 1000;
jQuery(window).scroll(function() {
    var scroll = jQuery(window).scrollTop();

    if (scroll >= scrollChange) {
        header.addClass('vis-menu');
    } else {
        header.removeClass("vis-menu");
    }
});