jQuery(document).ready(function () {
    if (jQuery(window).width() < 849) {
        jQuery('.main-services-pages').find('.children-list').hide('slide');
    }
});
jQuery('.main-services-guarantee-title').click(function () {
    jQuery(this).children('.service-tile-guarantee-description').slideToggle();
})
jQuery('.main-services-guarantee').click(function () {
    jQuery(this).children('.service-tile-guarantee-description').slideToggle();
})
jQuery('.show-more-pages').click(function () {
    if (jQuery(this).attr('data-before') === '↑ скрыть ') {
        jQuery(this).parent().find(".children-list").hide('slide');
        jQuery(this).attr('data-before', '↓ показать ');
    } else {
        jQuery(this).attr('data-before', '↑ скрыть ')
        jQuery(this).parent().find(".children-list").show('slide');
    }

});
(function (jQuery) {
    jQuery.fn.invisible = function () {
        return this.each(function () {
            jQuery(this).css("visibility", "hidden");
            jQuery(this).css("height", "0px");
        });
    };
    jQuery.fn.visible = function () {
        return this.each(function () {
            jQuery(this).css("visibility", "visible");
            jQuery(this).css("height", "unset");
        });
    };
}(jQuery));