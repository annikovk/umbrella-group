/* Слайдер акций на первом экране главной: стрелки влево/вправо, свайп на телефоне.
   Скрипт инлайнится в футер, поэтому DOM к этому моменту уже готов. */
(function () {
    var sliders = document.querySelectorAll('[data-promo-slider]');

    for (var s = 0; s < sliders.length; s++) {
        initPromoSlider(sliders[s]);
    }

    function initPromoSlider(slider) {
        var slides = slider.querySelectorAll('[data-promo-slide]');
        if (slides.length < 2) {
            return;
        }

        var current = 0;

        function show(index) {
            current = (index + slides.length) % slides.length;
            for (var i = 0; i < slides.length; i++) {
                if (i === current) {
                    slides[i].classList.add('is-active');
                } else {
                    slides[i].classList.remove('is-active');
                }
            }
        }

        var prev = slider.querySelector('[data-promo-prev]');
        var next = slider.querySelector('[data-promo-next]');

        if (prev) {
            prev.addEventListener('click', function () {
                show(current - 1);
            });
        }
        if (next) {
            next.addEventListener('click', function () {
                show(current + 1);
            });
        }

        // Свайп по горизонтали — только если жест не вертикальная прокрутка.
        var startX = null;
        var startY = null;

        slider.addEventListener('touchstart', function (event) {
            startX = event.changedTouches[0].clientX;
            startY = event.changedTouches[0].clientY;
        }, {passive: true});

        slider.addEventListener('touchend', function (event) {
            if (startX === null) {
                return;
            }
            var deltaX = event.changedTouches[0].clientX - startX;
            var deltaY = event.changedTouches[0].clientY - startY;
            if (Math.abs(deltaX) > 50 && Math.abs(deltaX) > Math.abs(deltaY)) {
                show(deltaX < 0 ? current + 1 : current - 1);
            }
            startX = null;
            startY = null;
        }, {passive: true});
    }
})();
