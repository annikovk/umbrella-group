/* Слайдер акций на первом экране главной: стрелки влево/вправо, свайп на телефоне,
   автолистание, сдвиг слайдов как в карусели.
   Скрипт в подвале с defer — DOM к этому моменту уже готов. */
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

        // Автолистание: следующий слайд раз в AUTOPLAY_MS. Курсор над слайдером
        // ставит на паузу, ручное листание начинает отсчёт заново.
        var AUTOPLAY_MS = 7000;
        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var timer = null;
        var hovered = false;

        function restartAutoplay() {
            clearTimeout(timer);
            if (reduceMotion || hovered || document.hidden) {
                return;
            }
            timer = setTimeout(function () {
                show(current + 1, 1);
            }, AUTOPLAY_MS);
        }

        slider.addEventListener('mouseenter', function () {
            hovered = true;
            clearTimeout(timer);
        });
        slider.addEventListener('mouseleave', function () {
            hovered = false;
            restartAutoplay();
        });
        document.addEventListener('visibilitychange', restartAutoplay);

        // Сдвиг, как в карусели: вперёд — уходящий слайд уезжает влево, новый
        // въезжает справа; назад — наоборот. Длительность — в CSS (.promo-slide).
        var SLIDE_MS = 600;
        var cleanup = [];

        function show(index, dir) {
            var target = (index + slides.length) % slides.length;
            restartAutoplay();
            if (target === current) {
                return;
            }
            dir = dir || 1;
            var leaving = slides[current];
            var entering = slides[target];
            current = target;

            // Прошлый сдвиг ещё не доехал — дочищаем его сразу.
            while (cleanup.length) {
                clearTimeout(cleanup.pop());
            }
            for (var i = 0; i < slides.length; i++) {
                if (slides[i] !== leaving && slides[i] !== entering) {
                    slides[i].style.transition = '';
                    slides[i].style.transform = '';
                    slides[i].style.visibility = '';
                }
            }

            // Новый слайд — за краем без анимации, затем въезжает на место.
            entering.style.transition = 'none';
            entering.style.transform = 'translateX(' + (dir * 100) + '%)';
            entering.style.visibility = 'visible';
            void entering.offsetWidth;
            entering.style.transition = '';
            entering.style.transform = '';
            entering.classList.add('is-active');

            // Старый остаётся видимым, пока уезжает.
            leaving.classList.remove('is-active');
            leaving.style.visibility = 'visible';
            leaving.style.transform = 'translateX(' + (-dir * 100) + '%)';

            cleanup.push(setTimeout(function () {
                entering.style.visibility = '';
                leaving.style.visibility = '';
                leaving.style.transition = 'none';
                leaving.style.transform = '';
            }, reduceMotion ? 0 : SLIDE_MS + 50));
        }

        var prev = slider.querySelector('[data-promo-prev]');
        var next = slider.querySelector('[data-promo-next]');

        if (prev) {
            prev.addEventListener('click', function () {
                show(current - 1, -1);
            });
        }
        if (next) {
            next.addEventListener('click', function () {
                show(current + 1, 1);
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
                show(deltaX < 0 ? current + 1 : current - 1, deltaX < 0 ? 1 : -1);
            }
            startX = null;
            startY = null;
        }, {passive: true});

        restartAutoplay();
    }
})();
