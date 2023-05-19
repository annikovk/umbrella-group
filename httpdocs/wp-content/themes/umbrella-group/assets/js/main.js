
jQuery(".accordion-title").click(function (){
    if (jQuery(this).parent().hasClass("active")) {
        jQuery(this).parent().removeClass("active");
    } else {
        jQuery(this).parent().parent().find(".accordion-item").removeClass("active");
        jQuery(this).parent().addClass("active");
    }
});
jQuery(".accordion button.toggle").click(function () {
    jQuery(this).parent().find(".accordion-inner").toggle(200);
});

