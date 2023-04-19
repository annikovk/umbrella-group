var header = jQuery(".umbrela_sticky_menu");
var scrollChange = 1000;
jQuery(window).scroll(function () {
    var scroll = jQuery(window).scrollTop();

    if (scroll >= scrollChange) {
        header.addClass('vis-menu');
    } else {
        header.removeClass("vis-menu");
    }
});

jQuery('.btn_modal_screen_one').on('click', function (event) {
    jQuery('html').css('overflow', 'hidden');
    event.preventDefault();
    jQuery('#' + jQuery(this).data('modal')).css('display', 'block');
    console.log('wqewe');
})


jQuery('span.close').on('click', function (event) {
    event.preventDefault();
    jQuery('.modal').css('display', 'none');
    jQuery('html').css('overflow', 'visible');
})


jQuery(window).on('click', function (event) {

    if (jQuery.inArray(event.target, jQuery('.modal')) != "-1") {
        jQuery('.modal').css('display', 'none');
        jQuery('html').css('overflow', 'visible');
    }
});

jQuery('.btn_scrolls').on('click', function (e) {

    let href = jQuery(this).attr('href');

    jQuery('html, body').animate({
        scrollTop: jQuery(href).offset().top - 400
    }, {
        duration: 500,   // по умолчанию «400» 
        easing: "linear" // по умолчанию «swing» 
    });

    return false;
});