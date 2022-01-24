jQuery(document).ready(function () {
    jQuery(".feedback>.content>.tabs li").click(function () {
        jQuery(this).closest('.content').find(" li").removeClass("selected");
        jQuery(this).closest('.content').find(" .tiles>div").addClass("invisible");
        jQuery(this).addClass("selected");
        jQuery('.' + jQuery(this).attr('class').split(' ')[0]).removeClass("invisible");
    });
});
