jQuery('#tabs-nav li:first-child').addClass('active');
jQuery('.tab-content').hide();
jQuery('.tab-content:first').show();

// Click function
jQuery('#tabs-nav li').click(function(){
  jQuery('#tabs-nav li').removeClass('active');
  jQuery(this).addClass('active');
  jQuery('.tab-content').hide();
  
  var activeTab = jQuery(this).find('a').attr('href');
  jQuery(activeTab).fadeIn();
  return false;
});

jQuery('a.item_directions').click(function () {
    jQuery('#tabs-nav li a[href=' + jQuery(this).attr('href') + ']').click();
})
var swiper = new Swiper(".step_slider_busnisse", {
    slidesPerView: "auto",
    spaceBetween: 40,
    allowTouchMove: true,
    autoHeight: false,
    breakpoints: {
        300: {
            autoHeight: true,
        },
        768: {
            autoHeight: false,
        },

    },
    pagination: {
        el: ".swiper-pagination",
        clickable: false,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});

jQuery(document).ready(function(){
  jQuery('.btn_modal_step').on('click', function(event){
    jQuery('html').css('overflow', 'hidden');
    event.preventDefault();
    jQuery('#'+jQuery(this).data('modal')).css('display','block');
  })


  jQuery('span.close').on('click', function(event){
    event.preventDefault(); 
    jQuery('.modal').css('display','none');
     jQuery('html').css('overflow', 'visible');
  })


    jQuery(window).on('click', function (event) {

        if (jQuery.inArray(event.target, jQuery('.modal')) != "-1") {
            jQuery('.modal').css('display', 'none');
            jQuery('html').css('overflow', 'visible');
        }
    })
});
