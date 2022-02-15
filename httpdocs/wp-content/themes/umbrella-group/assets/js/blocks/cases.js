jQuery(document).ready(function () {
    jQuery(".newcases>.content>.tabs li").click(function () {
        jQuery(this).closest('.content').find(" li").removeClass("selected");
        jQuery(this).closest('.content').find(" .tiles>div").addClass("invisible");
        jQuery(this).addClass("selected");
        jQuery('.' + jQuery(this).attr('class').split(' ')[0]).removeClass("invisible");
    });

    //If cases block stays right after the feedback block, remove 100px margin-bot from feedback to keep 100px margin between them
    jQuery(".newcases").closest('section').prev().find(".feedback").addClass("margin-bot-minus-thirty");
});
