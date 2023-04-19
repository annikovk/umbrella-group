jQuery(function() {
	jQuery(".js-problems-title").on("click", function(e) {

		e.preventDefault();
		var $this = jQuery(this);

		if (!$this.hasClass("problems__active")) {
            jQuery(".js-problems-content").slideUp(800);
            jQuery(".js-problems-title").removeClass("problems__active");
            jQuery('.js-problems-rotate').removeClass('problems__rotate');
        }

        $this.toggleClass("problems__active");
        $this.next().slideToggle();
        jQuery('.js-problems-rotate', this).toggleClass('problems__rotate');
    });
});

jQuery(window).bind("resize", function () {
    console.log(jQuery(this).width())
    if (jQuery(this).width() < 500) {
        jQuery('.problems__title').removeClass('problems__active');
        jQuery(".js-problems-content").hide();
    }
}).trigger('resize');

