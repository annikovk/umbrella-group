// Cache selectors
var lastId, topMenu = jQuery("#side-menu"),
    topMenuHeight = topMenu.outerHeight() + 200,
    // All list items
    menuItems = topMenu.find("a"),
    // Anchors corresponding to menu items
    scrollItems = menuItems.map(function () {
        var searchSnippet = jQuery(this).attr("href");
        var item = jQuery(`[data-link=${searchSnippet}]`);
        if (item.length) {
            return item;
        }
    });
// Bind to scroll
jQuery(window).scroll(function () {
    // Get container scroll position
    var fromTop = jQuery(this).scrollTop() + topMenuHeight;
    // Get id of current scroll item
    var cur = scrollItems.map(function () {
        if (jQuery(this).offset().top < fromTop)
            return this;
    });
    // Get the id of the current element
    cur = cur[cur.length - 1];
    var id = cur && cur.length ? cur[0].getAttribute("data-link") : "";
    if (lastId !== id) {
        lastId = id;
        // Set/remove active class
        menuItems.parent().removeClass("active");
        jQuery(menuItems).filter(jQuery(`[href=${id}]`)).parent().addClass("active");
    }
});