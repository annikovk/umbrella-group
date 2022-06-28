jQuery('.main-services-guarantee-title').click(function () {
    jQuery(this).children('.service-tile-guarantee-description').slideToggle();
})
jQuery('.main-services-guarantee').click(function () {
    jQuery(this).children('.service-tile-guarantee-description').slideToggle();
})
jQuery('.show-more-pages').click(function () {
    if (jQuery(this).attr('data-before') === '↑ скрыть ') {
        jQuery(this).parent().find(".children-list").hide('slide');
        jQuery(this).attr('data-before', '↓ показать ');
    } else {
        jQuery(this).attr('data-before', '↑ скрыть ')
        jQuery(this).parent().find(".children-list").show('slide');
    }

});
(function (jQuery) {
    jQuery.fn.invisible = function () {
        return this.each(function () {
            jQuery(this).css("visibility", "hidden");
            jQuery(this).css("height", "0px");
        });
    };
    jQuery.fn.visible = function () {
        return this.each(function () {
            jQuery(this).css("visibility", "visible");
            jQuery(this).css("height", "unset");
        });
    };
}(jQuery));


jQuery('.main-services .main-services-tile-grid').on('scroll', function () {
    var x = 50
    var y = jQuery(this).visibleInScroll().top
    var elem = document.elementFromPoint(x, y)
    jQuery(".main-services .tabs li").each(function () {
        if (jQuery(this).attr('target-block') === jQuery(elem)[0].id) {
            if (!jQuery(this).hasClass("active")) {
                jQuery(this).addClass("active");
                var tile = jQuery(this)
                var container = jQuery(this).parent()
                // container.animate({'scrollLeft': 0}, 0);
                var scrollTo = container.scrollLeft() + tile.position().left - 30;
                console.log(scrollTo)
                container.animate({'scrollLeft': scrollTo}, 300);
            }

        } else {
            jQuery(this).removeClass("active");
        }
    })
})
jQuery('.main-services .tabs li').on('click', function () {
    var divIdx = jQuery(this).attr('target-block');
    var tile = jQuery('#' + divIdx)
    var container = jQuery('.main-services .main-services-tile-grid')
    var scrollTo = container.scrollLeft() + tile.position().left;
    container.animate({'scrollLeft': scrollTo}, 300);
});

/**
 * Determines whether an element is contained in the visible viewport of its scrolled parents
 *
 * @param goDeep If false or undefined, just check the immediate scroll parent.
 *               If truthy, check all scroll parents up to the Document.
 * @return Object A bounding box representing the element's visible portion:
 *         left: left edge of the visible portion of the element relative to the screen
 *         top: top edge of the visible portion of the element relative to the screen
 *         right: right edge of the visible portion of the element relative to the screen
 *         bottom: bottom edge of the visible portion of the element relative to the screen
 *         width: width of the element
 *         height: height of the element
 *         isVisible: whether any part of the element can be seen
 *         isContained: whether all of the element can be seen
 *         visibleWidth: width of the visible portion of the element
 *         visibleHeight: width of the visible portion of the element
 */

jQuery.fn.visibleInScroll = function (goDeep) {
    var parent = jQuery(this[0]).scrollParent()[0],
        elRect = this[0].getBoundingClientRect(),
        rects = [parent.getBoundingClientRect()];
    elRect = {
        left: elRect.left,
        top: elRect.top,
        right: elRect.right,
        bottom: elRect.bottom,
        width: elRect.width,
        height: elRect.height,
        visibleWidth: elRect.width,
        visibleHeight: elRect.height,
        isVisible: true,
        isContained: true
    };
    var elWidth = elRect.width,
        elHeight = elRect.height;
    if (parent === this[0].ownerDocument) {
        return elRect;
    }

    while (parent !== this[0].ownerDocument && parent !== null) {
        if (parent.scrollWidth > parent.clientWidth || parent.scrollHeight > parent.clientHeight) {
            rects.push(parent.getBoundingClientRect());
        }
        if (rects.length && goDeep) {
            break;
        }
        parent = jQuery(parent).scrollParent()[0];
    }
    if (!goDeep) {
        rects.length = 1;
    }
    for (var i = 0; i < rects.length; i += 1) {
        var rect = rects[i];
        elRect.left = Math.max(elRect.left, rect.left);
        elRect.top = Math.max(elRect.top, rect.top);
        elRect.right = Math.min(elRect.right, rect.right);
        elRect.bottom = Math.min(elRect.bottom, rect.bottom);
    }
    elRect.visibleWidth = Math.max(0, elRect.right - elRect.left);
    elRect.visibleHeight = elRect.visibleWidth && Math.max(0, elRect.bottom - elRect.top);
    if (!elRect.visibleHeight) {
        elRect.visibleWidth = 0;
    }
    elRect.isVisible = elRect.visibleWidth > 0 && elRect.visibleHeight > 0;
    elRect.isContained = elRect.visibleWidth === elRect.width && elRect.visibleHeight === elRect.height;
    return elRect;
};

jQuery.fn.scrollParent = function (includeHidden) {
    var position = this.css("position"),
        excludeStaticParent = position === "absolute",
        overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/,
        scrollParent = this.parents().filter(function () {
            var parent = jQuery(this);
            if (excludeStaticParent && parent.css("position") === "static") {
                return false;
            }
            return overflowRegex.test(parent.css("overflow") + parent.css("overflow-y") +
                parent.css("overflow-x"));
        }).eq(0);

    return position === "fixed" || !scrollParent.length ?
        jQuery(this[0].ownerDocument || document) :
        scrollParent;
};
