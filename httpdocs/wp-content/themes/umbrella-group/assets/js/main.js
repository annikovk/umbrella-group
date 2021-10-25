jQuery(".accordion-title").click(function (){
    jQuery(this).parent().parent().find(".accordion-item").removeClass("active");
    jQuery(this).parent().toggleClass("active");
});
jQuery(document).ready(function (){
    jQuery(".accordion .accordion-item:nth-child(2)").find(".accordion-title").click();
});