jQuery(".partial_payments_header_hint").hover(
    function (e) {
        if (window.innerWidth <= 425) {
            leftTooltipPos = "5vh"
            topTooltipPos = e.pageY - jQuery(window).scrollTop() + 15 + "px";
        } else {
            leftTooltipPos = e.pageX - 30 + "px";
            topTooltipPos = e.pageY - jQuery(window).scrollTop() + 15 + "px";
        }
        a = jQuery(this).find(".partial_payments_header_tooltip");
        a.css({
            left: leftTooltipPos,
            top: topTooltipPos,
        })
            .stop()
            .show(100);
    },
    function () {
        jQuery(this).find(".partial_payments_header_tooltip").hide();
    }
);