jQuery(".accordion-title").click(function (){
    jQuery(this).parent().parent().find(".accordion-item").removeClass("active");
    jQuery(this).parent().toggleClass("active");
});