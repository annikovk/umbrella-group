var tabs = document.querySelectorAll("li.tab a");
for (var i = 0; i < tabs.length; i++) {
    var tab = tabs[i];
    console.log(tab.outerHTML);
    tab.onclick = function () {
        console.log(tab.outerHTML);
        if (tab.outerHTML === "Рейтинги") {
            console.log("tab");
        }
    };

}