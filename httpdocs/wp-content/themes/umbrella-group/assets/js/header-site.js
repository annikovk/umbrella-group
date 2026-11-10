/**
 * Шапка сайта (template-parts/header/header-site.php).
 *
 *  - выпадашки [data-ugh-dropdown] — «Связаться в мессенджере» и карточка
 *    офисов у «Новосибирска» (кнопка [data-ugh-toggle], список
 *    [data-ugh-list]); открыта всегда одна. По клику, закрывается кликом мимо,
 *    Esc и выбором пункта; у мыши (hover: hover) открывается и наведением,
 *    закрывается через 200 мс после ухода курсора (зазор до списка закрыт
 *    мостиком ::before у списка в CSS). Клик по кнопке, открытой
 *    наведением, её не закрывает, а закрепляет: тогда уход курсора уже не
 *    закрывает, только клик мимо / Esc. Тач и клавиатура — как раньше;
 *  - «+7 (383)...Показать» и «contact@...Показать» (#masthead): раскрывает и
 *    считает цели тег подмены GTM — его обработчики висят на span.phoneShow /
 *    span.mailShow, которые он сам вставляет с подменным значением, и на их
 *    ссылке; отправляет send_events → «other_click_showphoneclass» /
 *    «other_click_showmailclass». Здесь: (1) пока «Показать» скрыто, клик по
 *    ссылке не звонит и не открывает почту (preventDefault, без
 *    stopPropagation — обработчики тега должны сработать); (2) если тег ещё
 *    не подменил (свой span с data-ugh-fallback — GTM грузится по первому
 *    действию, inc/perf.php), клик ждёт подмены до 3 с и кликает уже по span
 *    тега — раскрывается подменное значение и уходит цель; не дождались
 *    (блокировщик, источник «proverka») — раскрываем своё; (3) пробел, который
 *    тег ставит перед span у телефона, убирается — многоточие рисует CSS.
 */
(function () {
    'use strict';

    var OPEN = 'is-open';
    var HOVER = 'ughHover';   // data-ugh-hover: открыта наведением, не кликом
    var CLOSE_DELAY = 200;

    function close(drop) {
        var btn = drop.querySelector('[data-ugh-toggle]');
        var list = drop.querySelector('[data-ugh-list]');
        clearTimeout(drop._ughTimer);
        delete drop.dataset[HOVER];
        drop.classList.remove(OPEN);
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (list) list.hidden = true;
    }

    function open(drop) {
        var btn = drop.querySelector('[data-ugh-toggle]');
        var list = drop.querySelector('[data-ugh-list]');
        document.querySelectorAll('[data-ugh-dropdown].' + OPEN).forEach(function (other) {
            if (other !== drop) close(other);
        });
        drop.classList.add(OPEN);
        if (btn) btn.setAttribute('aria-expanded', 'true');
        if (list) list.hidden = false;
    }

    document.addEventListener('click', function (e) {
        var target = e.target instanceof Element ? e.target : null;
        if (!target) return;

        var toggle = target.closest('[data-ugh-dropdown] [data-ugh-toggle]');
        document.querySelectorAll('[data-ugh-dropdown].' + OPEN).forEach(function (drop) {
            if (!drop.contains(target)) close(drop);   // клик мимо; внутри — ниже
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
        var item = target.closest('[data-ugh-dropdown] [data-ugh-list] a');
        if (item) close(item.closest('[data-ugh-dropdown]'));

        var link = target.closest(REVEAL);
        var hidden = link && link.querySelector(SHOW);
        if (hidden) {
            e.preventDefault();
            if (hidden.hasAttribute('data-ugh-fallback')) waitForTag(link, hidden);
        }
    }, true);

    var REVEAL = '#masthead a.ugh-phone, #masthead .ugh-mail a';
    var SHOW = '.phoneShow, .mailShow';
    var WAIT = 3000;

    // «+7 (383) <span>» тега → «+7 (383)<span>»: многоточие перед «Показать» — из CSS
    function tidy(link) {
        var span = link.querySelector(SHOW);
        var text = span && span.previousSibling;
        if (text && text.nodeType === 3 && /\s$/.test(text.nodeValue)) {
            text.nodeValue = text.nodeValue.replace(/\s+$/, '');
        }
    }

    function revealOwn(link, span) {
        var full = span.getAttribute('phonevalue') || span.getAttribute('mailvalue');
        if (full) link.textContent = full;
    }

    // клик до подмены: дождаться span тега и кликнуть по нему — раскроет и посчитает тег
    function waitForTag(link, own) {
        if (link.dataset.ughWait) return;
        link.dataset.ughWait = '1';
        link.classList.add('is-pending');
        var done = function (span) {
            clearTimeout(timer);
            observer.disconnect();
            delete link.dataset.ughWait;
            link.classList.remove('is-pending');
            span ? span.click() : revealOwn(link, own);
        };
        var observer = new MutationObserver(function () {
            var span = link.querySelector(SHOW);
            if (span && !span.hasAttribute('data-ugh-fallback')) done(span);
        });
        var timer = setTimeout(function () {
            if (link.querySelector('[data-ugh-fallback]')) done(null); else done(link.querySelector(SHOW));
        }, WAIT);
        observer.observe(link, {childList: true});
    }

    document.querySelectorAll(REVEAL).forEach(function (link) {
        tidy(link);
        new MutationObserver(function () { tidy(link); }).observe(link, {childList: true});
    });

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
            var btn = drop.querySelector('[data-ugh-toggle]');
            if (btn) btn.focus();
        });
    });
})();
