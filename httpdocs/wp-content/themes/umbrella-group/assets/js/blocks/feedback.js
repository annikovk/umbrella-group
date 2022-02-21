jQuery(document).ready(function () {
    jQuery(".feedback>.content>.tabs li").click(function () {
        jQuery(this).closest('.content').find(" li").removeClass("selected");
        jQuery(this).closest('.content').find(" .tiles>div").addClass("invisible");
        jQuery(this).addClass("selected");
        jQuery('.' + jQuery(this).attr('class').split(' ')[0]).removeClass("invisible");
    });

    //If feedback block stays right after the cases block, remove 100px margin-bot from cases to keep 100px margin between them
    jQuery(".feedback").closest('section').prev().find(".newcases").addClass("margin-bot-zero");
});
