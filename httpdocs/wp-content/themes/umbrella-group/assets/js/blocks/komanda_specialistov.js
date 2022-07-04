jQuery(document).ready(function () {
    let section = jQuery(".section.komanda_specialistov");
    section.find(" .tabs li").click(function () {
        section.find(" .tabs li").removeClass("active");
        let classtohide = jQuery(this).attr('class');
        section.find(".specialist-tile").hide();
        section.find(".specialist-tile." + classtohide).show()
        jQuery(this).addClass("active");
    })
    section.find(" .tabs li").first().click();
});