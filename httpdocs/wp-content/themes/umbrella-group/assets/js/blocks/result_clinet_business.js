var swiper = new Swiper(".swiper-result", {
  direction: "horizontal",
        slidesPerView: "2.26",
        spaceBetween: 40,
  breakpoints: {
    300: {
      slidesPerView:"1.187",
      spaceBetween: 30,
    },
    768: {
      slidesPerView:"2.26",
      spaceBetween: 40,
    }
  },
  // effect: "slide",
  mousewheel: {
    eventsTarged: ".swiper-slide",
    forceToAxis:true,
  },
  keyboard: {
    enabled: false,
    onlyInViewport: true
  },
  scrollbar: {
    el: ".swiper-scrollbar",
    hide: false,
    draggable: true
  },
});