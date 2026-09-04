jQuery(function() {
	jQuery(".js-teams-title").on("click", function(e) {

		e.preventDefault();
		var $this = jQuery(this);

		if (!$this.hasClass("teams__active")) {
			jQuery(".js-teams-content").slideUp(800);
			jQuery(".js-teams-title").removeClass("teams__active");
			jQuery('.js-teams-rotate').removeClass('teams__rotate');
		}

		$this.toggleClass("teams__active");
		$this.next().slideToggle();
		jQuery('.js-teams-rotate',this).toggleClass('teams__rotate');
	});
});

jQuery(window).bind("resize", function () {
    console.log(jQuery(this).width())
    if (jQuery(this).width() < 768) {
        jQuery('.teams__title').removeClass('opn');
    } else {
        jQuery('.teams__title').addClass('opn');
    }
}).trigger('resize');


  jQuery('.btn_modal_teams').on('click', function(event){
    jQuery('html').css('overflow', 'hidden');
    event.preventDefault();
    jQuery('#'+jQuery(this).data('modal')).css('display','block');
    console.log('wqewe');
  })


  jQuery('span.close').on('click', function(event){
    event.preventDefault(); 
    jQuery('.modal').css('display','none');
     jQuery('html').css('overflow', 'visible');
  })


  jQuery(window).on('click', function(event){

    if (jQuery.inArray( event.target, jQuery('.modal') ) != "-1") {
          jQuery('.modal').css('display','none');
             jQuery('html').css('overflow', 'visible');
      }
  });

  jQuery('.btn_scrolls').on('click', function() {

    let href = jQuery(this).attr('href');

    jQuery('html, body').animate({
        scrollTop: jQuery(href).offset().top
    }, {
        duration: 370,   // по умолчанию «400» 
        easing: "linear" // по умолчанию «swing» 
    });

    return false;
});