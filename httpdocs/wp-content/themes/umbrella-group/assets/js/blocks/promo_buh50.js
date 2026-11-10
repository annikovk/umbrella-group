// Акция «−50% на первый месяц бухгалтерского обслуживания» — template-parts/shortcodes/promo_buh50.php.
//
// Окно [data-buh50-popup] (<dialog>) открывается через data-delay мс после load, если:
//  - акция не кончилась (data-until — страховка для страницы из кэша WP Rocket);
//  - посетитель не закрывал окно последние сутки (localStorage);
//  - не открыт другой лайтбокс (magnificPopup Flatsome, fancybox, другой <dialog>)
//    и посетитель не печатает в поле формы.
// Закрывается крестиком, кликом по подложке, Esc. Кнопка «Получить скидку»
// закрывает окно, а форму открывает обработчик Flatsome на a[href="#…-lightbox"].
//
// Для снимков: document.querySelector('[data-buh50-popup]').ugOpen() — открыть сразу.
(function () {
    'use strict';

    var KEY = 'ug-buh50-popup-closed';
    var DAY = 24 * 60 * 60 * 1000;

    function expired(el) {
        var until = Date.parse(el.getAttribute('data-until') || '');
        return !isNaN(until) && Date.now() > until;
    }

    function recentlyClosed() {
        try {
            var t = Number(localStorage.getItem(KEY));
            return t > 0 && Date.now() - t < DAY;
        } catch (e) {
            return false;
        }
    }

    function remember() {
        try {
            localStorage.setItem(KEY, String(Date.now()));
        } catch (e) { /* приватный режим — покажем ещё раз, не страшно */ }
    }

    function otherOverlayOpen(self) {
        var mfp = window.jQuery && window.jQuery.magnificPopup;
        if (mfp && mfp.instance && mfp.instance.isOpen) {
            return true;
        }
        if (document.querySelector('.mfp-wrap, .fancybox__container, .fancybox-container')) {
            return true;
        }
        var dialogs = document.querySelectorAll('dialog[open]');
        for (var i = 0; i < dialogs.length; i++) {
            if (dialogs[i] !== self) {
                return true;
            }
        }
        var a = document.activeElement;
        return !!(a && /^(INPUT|TEXTAREA|SELECT)$/.test(a.tagName));
    }

    function loadImages(root) {
        [].forEach.call(root.querySelectorAll('[data-srcset]'), function (s) {
            s.srcset = s.getAttribute('data-srcset');
            s.removeAttribute('data-srcset');
        });
        [].forEach.call(root.querySelectorAll('img[data-src]'), function (img) {
            img.src = img.getAttribute('data-src');
            img.removeAttribute('data-src');
            img.classList.remove('lazy-load');
        });
    }

    function initPopup(dlg) {
        if (typeof dlg.showModal !== 'function' || expired(dlg)) {
            return;
        }
        var lastFocus = null;

        function open() {
            if (dlg.open) {
                return;
            }
            loadImages(dlg);
            lastFocus = document.activeElement;
            dlg.showModal();
            document.documentElement.classList.add('ug-buh50-lock');
            // Фокус — на окно, а не на крестик: читалка начнёт с заголовка.
            dlg.focus();
        }

        function close() {
            if (dlg.open) {
                dlg.close();
            }
        }

        dlg.addEventListener('close', function () {
            remember();
            document.documentElement.classList.remove('ug-buh50-lock');
            if (lastFocus && typeof lastFocus.focus === 'function' && document.contains(lastFocus)) {
                lastFocus.focus({preventScroll: true});
            }
        });

        // Клик по подложке: у модального <dialog> он приходит на сам элемент.
        dlg.addEventListener('click', function (e) {
            if (e.target === dlg) {
                close();
            }
        });
        [].forEach.call(dlg.querySelectorAll('[data-buh50-close]'), function (b) {
            b.addEventListener('click', close);
        });
        [].forEach.call(dlg.querySelectorAll('[data-buh50-cta]'), function (a) {
            a.addEventListener('click', function (e) {
                var id = (a.getAttribute('href') || '').replace(/^#/, '');
                // Формы на странице нет — ведём на страницу услуги с блоком акции.
                if (!id || !document.getElementById(id)) {
                    e.preventDefault();
                    remember();
                    location.href = '/services/bukhgalterskie-uslugi/obsluzhivanie/';
                    return;
                }
                close();
            });
        });

        dlg.ugOpen = open;

        if (recentlyClosed()) {
            return;
        }
        var delay = Number(dlg.getAttribute('data-delay')) || 4000;
        var start = function () {
            setTimeout(function () {
                if (!recentlyClosed() && !expired(dlg) && !otherOverlayOpen(dlg)) {
                    open();
                }
            }, delay);
        };
        if (document.readyState === 'complete') {
            start();
        } else {
            window.addEventListener('load', start, {once: true});
        }
    }

    function init() {
        var dlg = document.querySelector('[data-buh50-popup]');
        if (dlg) {
            initPopup(dlg);
        }
        // Блок в контенте из кэша, пережившего акцию, — прячем.
        [].forEach.call(document.querySelectorAll('[data-buh50-block]'), function (b) {
            if (expired(b)) {
                b.hidden = true;
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
