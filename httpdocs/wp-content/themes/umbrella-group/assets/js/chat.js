document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function() {
        jQuery.getScript("//api.venyoo.ru/wnew.js?wc=venyoo/default/science&widget_id=6755342139789607", function() {
            console.log("Script loaded but not necessarily executed.");
        });
    }, 0);
});