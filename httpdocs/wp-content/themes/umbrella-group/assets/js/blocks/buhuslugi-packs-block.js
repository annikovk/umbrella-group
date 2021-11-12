jQuery(document).ready(function () {
    jQuery("input[type=radio][name=typeCompany]").change(function () {
        const valueList = document.querySelectorAll("#buh-table .price .value");
        valueList.forEach((item) => {
            if (this.value == "ip") {
                jQuery(item).text(jQuery(item).data("ip"));
            } else if (this.value == "company") {
                jQuery(item).text(jQuery(item).data("company"));
            }
        });
    });

    function elementInViewport(el) {
        var bounds = el.getBoundingClientRect();
        return (
            bounds.left >= 0 &&
            window.innerWidth - bounds.left - bounds.width + 5 >= 0
        );
    }
    function activateColumn() {
        const itemList = document.querySelectorAll(".rightBlock .item");
        itemList.forEach((item) => {
            if (elementInViewport(item)) {
                const activeList = document.querySelectorAll(
                    `[data-option='${jQuery(item).data("option")}']`
                );
                itemList.forEach((element) => {
                    jQuery(element).removeClass("active");
                });
                activeList.forEach((element) => {
                    jQuery(element).addClass("active");
                });
                jQuery(".tooltip").hide()
                return;
            }
        });
    }

    if (window.innerWidth <= 425) {
        activateColumn();
        jQuery("#buh-table .rightBlock").on("scroll", activateColumn);
    }

    if (window.innerWidth > 425) {
        jQuery(".item").hover(function () {
            const itemList = document.querySelectorAll(
                `[data-option='${jQuery(this).data("option")}']`
            );
            itemList.forEach((item) => {
                jQuery(item).toggleClass("active");
            });
        });
    }
    jQuery("#buh-table button").click(
        function (e){
            service = jQuery(`.item[data-option='${jQuery(this).closest(".item").data("option")}'] .name`)[0].innerText.replace(/[?]/gi, '')
            jQuery("#leave-request-contact-form div.leave-request-contact-form-service-name").text("Услуга: " + service);
            jQuery("#leave-request-contact-form  textarea.leave-request-contact-form-service-name").val(service);
        }
    );
    jQuery(".hint, .sale-flag, .hit-flag").hover(
        function (e) {
            if (window.innerWidth <= 425) {
                leftTooltipPos = "5vh"
                topTooltipPos = e.pageY - jQuery(window).scrollTop() + 15 + "px";
            } else {
                 leftTooltipPos = e.pageX + 15 + "px";
                 topTooltipPos =  e.pageY - jQuery(window).scrollTop() + "px";
            }
            jQuery(jQuery(this).data("tooltip"))
                .css({
                    left: leftTooltipPos,
                    top: topTooltipPos,
                })
                .stop()
                .show(100);
        },
        function () {
            jQuery(jQuery(this).data("tooltip")).hide();
        }
    );

});
