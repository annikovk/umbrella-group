jQuery(document).on('click', '.dynamic_tariff_sub_menu li', function(){
  jQuery('.dynamic_tariff_sub_menu li').removeClass('active');
    jQuery('.dynamic_tariff_menu li').removeClass('current-menu-parent');
    jQuery(this).parents('li').addClass('current-menu-parent');
    jQuery('ul.dynamic_tariff_menu').toggleClass('expanded');
    jQuery(this).addClass('active');
    var tab_id = jQuery(this).attr('data-tab');
    jQuery('.tab-content').removeClass('current');
    jQuery(this).addClass('current');
    jQuery('#' + tab_id).addClass('current');
});

var amout_tariff_1 = jQuery('.num_1').html();
var amout_tariff_2 = jQuery('.num_2').html();
var sale = parseFloat(amout_tariff_1.replace(",", ".").replace(/[^0-9.]/gim, "")) - parseFloat(amout_tariff_2.replace(",", ".").replace(/[^0-9.]/gim, ""));
jQuery('.tab-content.current ').find('.sale_amount').text(sale.toLocaleString('ru'));

jQuery(document).on('click', '.dynamic_tariff_sub_menu li', function () {
    var amout_tariff_1 = jQuery('.tab-content.current ').find('.num_1').html();
    var amout_tariff_2 = jQuery('.tab-content.current ').find('.num_2').html();
    var sale = parseFloat(amout_tariff_1.replace(",", ".").replace(/[^0-9.]/gim, "")) - parseFloat(amout_tariff_2.replace(",", ".").replace(/[^0-9.]/gim, ""));
    jQuery('.tab-content.current ').find('.sale_amount').text(sale.toLocaleString('ru'));
});


jQuery(document).on('click', '.dynamic_tariff_sub_menu li', function () {
    jQuery('.dynamic_tariff_sub_menu li').removeClass('active');
    jQuery('.dynamic_tariff_menu li').removeClass('current-menu-parent');
    jQuery(this).parents('li').addClass('current-menu-parent');
    jQuery('ul.dynamic_tariff_menu').toggleClass('expanded');
    jQuery(this).addClass('active');
    var tab_id = jQuery(this).attr('data-tab');
    jQuery('.tab-content_mob').removeClass('current');
    jQuery(this).addClass('current');
    jQuery('#' + tab_id).addClass('current');
});


const tab = document.querySelectorAll(".tab");
const toggleTab = function (element) {
    const tabBtn = element.querySelectorAll(".tab-btn");
    const tabContent = element.querySelectorAll(".tab-content");
    tabBtn[0].classList.add("tab-open");
    tabContent[0].classList.add("tab-open");

    const removeTab = function (element) {
        for (const i of element) {
            i.classList.remove("tab-open");
        }
    };
    const openTab = function (index) {
        removeTab(tabBtn);
        removeTab(tabContent);
        tabBtn[index].classList.add("tab-open");
        tabContent[index].classList.add("tab-open");
    };
    tabBtn.forEach((el, i) => (el.onclick = () => openTab(i)));
};
[...tab].forEach((el) => toggleTab(el));

jQuery(document).ready(function () {
    jQuery('.modal_tarif').on('click', function (event) {
        jQuery('html').css('overflow', 'hidden');
        event.preventDefault();
        jQuery('#' + jQuery(this).data('modal')).css('display', 'block');
    })


    jQuery('.dynamic_tariff_sub_menu li').on('click', function (event) {
        event.preventDefault();
        jQuery('.modal').css('display', 'none');
        jQuery('html').css('overflow', 'visible');
    })


    jQuery(window).on('click', function (event) {

        if (jQuery.inArray(event.target, jQuery('.modal')) != "-1") {
            jQuery('.modal').css('display', 'none');
            jQuery('html').css('overflow', 'visible');
        }
    })
});


jQuery('.close').on('click', function (event) {
    event.stopImmediatePropagation();
    jQuery(this).parents('.desc_box_suprise').removeClass('open_box_suprise');
});

jQuery('.box_suprise').on('click', function (event) {
    event.preventDefault();
    jQuery(this).find('.desc_box_suprise').addClass('open_box_suprise');
})

jQuery('ul.dynamic_tariff_menu>li>a').hover(function () {
    var text_new = jQuery(this).text();
    if (text_new == 'Красота и здоровье ↓') {
        text_new == jQuery(this).text();
        text_new = 'сфере красоты и здоровья';
    } else if (text_new == 'Торговля ↓') {
        text_new == jQuery(this).text();
        text_new = 'сфере торговли';
    } else if (text_new == 'Общественное питание ↓') {
        text_new == jQuery(this).text();
        text_new = 'сфере общественного питания';
    } else if (text_new == 'Образовательные услуги ↓') {
        text_new == jQuery(this).text();
        text_new = 'сфере образовательных услуг';
    } else {
        text_new = 'открытию бизнеса';
    }

    jQuery('div#calc_form .leave-request-contact-form-sub-header .title_sfera').text(text_new);
    console.log(text_new);
});

jQuery('.dynamic_tariff_sub_menu li').click(function () {
    var bus_title = jQuery(this).data('title');
    jQuery('.title_bus_dir').text(bus_title);
    jQuery('div#calc_form .leave-request-contact-form-header').text();
    jQuery('div#calc_form .leave-request-contact-form-header .title_dir').text(bus_title);

});

jQuery('ul.dynamic_tariff_menu>li>a').click(function (event) {
    event.preventDefault();
});


// jQuery('ul.dynamic_tariff_sub_menu li').click(function(){
//    var dataatr = jQuery(this).data('tab');
//    var $div = jQuery("#"+dataatr+""); 
//    console.log($div);
//     var height = $div.height();
//  jQuery( '.tariff_col_three' ).height(0);
//     jQuery( '.tariff_col_three' ).height(height);
// });

