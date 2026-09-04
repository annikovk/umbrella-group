  var swiper4 = new Swiper(".case_business_slider", {
    slidesPerView: "auto",
    spaceBetween: 40,
    pagination: {
      el: ".swiper-pagination",
      clickable: false,
    },
  navigation: {
    nextEl: ".case-swiper-button-next",
    prevEl: ".case-swiper-button-prev",
  },
  });

  jQuery('.btn_history_modal').on('click', function(event){
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