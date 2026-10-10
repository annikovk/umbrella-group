// Слайдер скриншотов в телефоне (блок «На связи 24/7»).
(function () {
    new Swiper('.swiper-container', {

        direction: 'vertical',
        mousewheel: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        spaceBetween: 30,
        slidesPerView: 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
})();
