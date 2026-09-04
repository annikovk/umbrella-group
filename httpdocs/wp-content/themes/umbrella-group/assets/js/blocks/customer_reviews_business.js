  var swiper_reviews = new Swiper(".swiper-reviews_bus", {
        spaceBetween: 1,
    slidesPerView: "auto",
    centeredSlides: true,
        loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  navigation: {
    nextEl: ".reviews-swiper-button-next",
    prevEl: ".reviews-swiper-button-prev",
  },
  });