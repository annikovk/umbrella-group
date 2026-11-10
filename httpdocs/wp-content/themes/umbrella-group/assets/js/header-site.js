/**
 * Шапка сайта (template-parts/header/header-site.php).
 *
 *  - «Связаться в мессенджере»: выпадашка по клику, закрывается кликом мимо,
 *    Esc и выбором пункта; у мыши (hover: hover) открывается и наведением,
 *    закрывается через 200 мс после ухода курсора (зазор до списка закрыт
 *    мостиком .ugh-msg__list::before в CSS). Клик по кнопке, открытой
 *    наведением, её не закрывает, а закрепляет: тогда уход курсора уже не
 *    закрывает, только клик мимо / Esc. Тач и клавиатура — как раньше;
 *  - «+7 (383) Показать»: клик раскрывает номер из phonevalue и не звонит.
 *    Делегировано на документ: тег подмены номеров в GTM перерисовывает
 *    ссылку своим span.phoneShow (и раскрывает его сам) — обработчик
 *    работает с любым из двух вариантов разметки. Второй клик — уже tel:.
 *    Фаза захвата — чтобы preventDefault стоял раньше обработчиков тега:
 *    они всё равно отработают (путь события уже построен) и отправят своё
 *    событие «показал телефон».
 */
(function () {
    'use strict';

    var OPEN = 'is-open';
    var HOVER = 'ughHover';   // data-ugh-hover: открыта наведением, не кликом
    var CLOSE_DELAY = 200;

    function close(drop) {
        var btn = drop.querySelector('[aria-expanded]');
        var list = drop.querySelector('.ugh-msg__list');
        clearTimeout(drop._ughTimer);
        delete drop.dataset[HOVER];
        drop.classList.remove(OPEN);
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (list) list.hidden = true;
    }

    function open(drop) {
        var btn = drop.querySelector('[aria-expanded]');
        var list = drop.querySelector('.ugh-msg__list');
        drop.classList.add(OPEN);
        if (btn) btn.setAttribute('aria-expanded', 'true');
        if (list) list.hidden = false;
    }

    document.addEventListener('click', function (e) {
        var target = e.target instanceof Element ? e.target : null;
        if (!target) return;

        var toggle = target.closest('[data-ugh-dropdown] .ugh-msg__toggle');
        document.querySelectorAll('[data-ugh-dropdown].' + OPEN).forEach(function (drop) {
            if (!toggle || !drop.contains(toggle)) close(drop);
        });
        if (toggle) {
            var drop = toggle.closest('[data-ugh-dropdown]');
            if (drop.dataset[HOVER]) {
                delete drop.dataset[HOVER];   // открыта наведением — закрепить
            } else {
                drop.classList.contains(OPEN) ? close(drop) : open(drop);
            }
            return;
        }
        var item = target.closest('[data-ugh-dropdown] .ugh-msg__item');
        if (item) close(item.closest('[data-ugh-dropdown]'));

        var phone = target.closest('a.ugh-phone');
        var hidden = phone && phone.querySelector('.phoneShow');
        if (hidden) {
            e.preventDefault();
            var full = hidden.getAttribute('phonevalue');
            if (full) phone.textContent = full;
            phone.classList.add('is-shown');
        }
    }, true);

    if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        document.querySelectorAll('[data-ugh-dropdown]').forEach(function (drop) {
            drop.addEventListener('pointerenter', function (e) {
                if (e.pointerType !== 'mouse') return;
                clearTimeout(drop._ughTimer);
                if (!drop.classList.contains(OPEN)) {
                    open(drop);
                    drop.dataset[HOVER] = '1';
                }
            });
            drop.addEventListener('pointerleave', function (e) {
                if (e.pointerType !== 'mouse' || !drop.dataset[HOVER]) return;
                clearTimeout(drop._ughTimer);
                drop._ughTimer = setTimeout(function () { close(drop); }, CLOSE_DELAY);
            });
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('[data-ugh-dropdown].' + OPEN).forEach(function (drop) {
            close(drop);
            var btn = drop.querySelector('.ugh-msg__toggle');
            if (btn) btn.focus();
        });
    });
})();
