/* [tz_packages] — переключатель «с отчётом о проверке / без отчёта», подпись
   выбранного варианта в попапе формы расчёта (CF7 9965) и её скрытое поле
   tz-package (письмо менеджеру). Кнопки попапа из других мест поле очищают. */
(function () {
    'use strict';

    var FORM = '#calculate-price-contact-form-lightbox';

    function setMode(root, mode) {
        root.setAttribute('data-tz-mode', mode);
        root.querySelectorAll('.tzp-switch__opt').forEach(function (b) {
            var on = b.getAttribute('data-tz-go') === mode;
            b.classList.toggle('is-active', on);
            b.setAttribute('aria-checked', on ? 'true' : 'false');
        });
    }

    function flash(card) {
        card.classList.add('is-flash');
        setTimeout(function () { card.classList.remove('is-flash'); }, 1200);
    }

    function init() {
        document.querySelectorAll('.tzp').forEach(function (root) {
            root.classList.add('tzp--js');
            setMode(root, 'check');

            root.addEventListener('click', function (e) {
                var go = e.target.closest('[data-tz-go]');
                if (!go || !root.contains(go)) {
                    return;
                }
                setMode(root, go.getAttribute('data-tz-go'));
                var pkg = go.getAttribute('data-tz-pkg');
                if (pkg === null) {
                    return;
                }
                var card = root.querySelector('.tzp-card[data-tz-pkg="' + pkg + '"]');
                if (card) {
                    var top = card.getBoundingClientRect().top + window.pageYOffset - 90;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                    flash(card);
                }
            });
        });

        // Подпись «Пакет: …» в попапе расчёта — как у таблицы пакетов бухуслуг.
        // Любая другая кнопка того же попапа подпись стирает.
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href="' + FORM + '"]');
            if (!a) {
                return;
            }
            var pkg = a.getAttribute('data-tz-package') || '';
            var box = document.querySelector(FORM + ' .leave-request-contact-form-service-name');
            if (box) {
                box.textContent = pkg ? 'Вариант: ' + pkg : '';
            }
            // Скрытое поле tz-package формы 9965 — уходит в письмо менеджеру и во Flamingo
            var field = document.querySelector(FORM + ' input[name="tz-package"]');
            if (field) {
                field.value = pkg;
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
