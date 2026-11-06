// [docs_folder] — «Документы компании»: папка открывается по клику на кнопку,
// обложку или ярлык; листы перелистываются стрелками, клавишами ← → и
// перетаскиванием (мышь, палец — pointer events). Угол листа идёт за курсором,
// при отпускании лист доворачивается или возвращается по порогу и скорости.
// «Увеличить» и клик по листу — лайтбокс <dialog> с полным сканом.
//
// Для снимков: el.ugDocs.open() / .lift(градусы) / .go(i) / .freeze(from, to, доля) / .zoom()
// на секции [data-docs-folder]; класс is-instant гасит переходы.
(function () {
    'use strict';

    var LIFT_MS = 380;      // подъём обложки на картинке до смены на открытую папку
    var OPEN_MS = 900;      // проявление открытой папки
    var FLIP_MS = 720;
    var reduce = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : {matches: false};

    function clamp(v, a, b) {
        return v < a ? a : v > b ? b : v;
    }

    function ease(t) {
        return t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    function easeOut(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    // Flatsome lazy-load переписывает src в data-src — возвращаем, когда лист нужен.
    function loadImg(img) {
        if (img && img.dataset.src && img.getAttribute('src') !== img.dataset.src) {
            img.src = img.dataset.src;
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }
            img.classList.remove('lazy-load');
        }
    }

    function init(root) {
        var $ = function (s) { return root.querySelector(s); };
        var sheets = [].slice.call(root.querySelectorAll('[data-docs-sheet]'));
        var n = sheets.length;
        if (!n) {
            return;
        }
        var sheetsBox = $('[data-docs-sheets]');
        var inside = $('[data-docs-inside]');
        var photo = $('[data-docs-photo]');
        var panel = $('[data-docs-panel]');
        var toggle = $('[data-docs-toggle]');
        var toggleLabel = $('[data-docs-toggle-label]');
        var lb = $('[data-docs-lightbox]');
        var lbImg = $('[data-docs-lightbox-img]');
        var lbCaption = $('[data-docs-lightbox-caption]');
        var cur = 0;
        var isOpen = false;
        var openTimer = 0;
        var liftTimer = 0;
        var drag = null;        // жест
        var suppressClick = false;

        var inert = function (el, on) {
            if (!el) {
                return;
            }
            el.inert = on;
            if (on) {
                el.setAttribute('inert', '');
            } else {
                el.removeAttribute('inert');
            }
        };

        // ── состояние «открыто / закрыто» ─────────────────────────────────
        function setOpen(v, focus) {
            if (v === isOpen) {
                return;
            }
            isOpen = v;
            clearTimeout(openTimer);
            clearTimeout(liftTimer);
            stopAnim();
            root.classList.remove('is-opened');
            toggle.setAttribute('aria-expanded', v ? 'true' : 'false');
            toggleLabel.textContent = v ? 'Закрыть папку' : 'Открыть папку';
            inert(inside, !v);
            inert(panel, !v);
            inert(sheetsBox, !v);
            inert(photo, v);
            var quick = reduce.matches;
            if (v) {
                sheets.forEach(function (s) { s.querySelectorAll('img').forEach(loadImg); });
                // 1) обложка на картинке поднимается, 2) картинка гаснет, проявляется открытая папка
                root.classList.add('is-lifting');
                liftTimer = setTimeout(function () {
                    root.classList.add('is-open');
                    reveal();
                    openTimer = setTimeout(function () {
                        root.classList.remove('is-lifting');
                        root.classList.add('is-opened');
                    }, quick ? 0 : OPEN_MS);
                }, quick ? 0 : LIFT_MS);
                if (focus) {
                    $('[data-docs-next]').focus({preventScroll: true});
                }
            } else {
                // обратно: картинка проявляется с поднятой обложкой, и та ложится
                root.classList.add('is-lifting');
                root.classList.remove('is-open');
                liftTimer = setTimeout(function () {
                    root.classList.remove('is-lifting');
                }, quick ? 0 : 420);
                render();
                if (focus) {
                    toggle.focus({preventScroll: true});
                }
            }
        }

        // Открытая папка с подписью и кнопками должна поместиться в экран:
        // докручиваем так, чтобы низ панели был виден, но верх папки (и ярлыки
        // на телефоне) не уехали под липкую шапку сайта.
        function reveal() {
            var header = document.querySelector('.header-wrapper.stuck, #header .stuck');
            var top = (header ? header.getBoundingClientRect().height : 70) + 12;
            var tabs = window.innerWidth < 850 ? $('.docs-folder__tabs').offsetHeight : 0;
            var binderTop = $('.docs-folder__binder').getBoundingClientRect().top - tabs;
            var need = panel.getBoundingClientRect().bottom + 16 - window.innerHeight;
            var dy = Math.min(need, binderTop - top);
            if (dy > 0) {
                window.scrollBy({top: dy, behavior: reduce.matches ? 'auto' : 'smooth'});
            }
        }

        // ── отрисовка текущего листа ──────────────────────────────────────
        // Лист i < cur перевёрнут и лежит слева оборотом (−180°), остальные —
        // справа (0°). Стопки: слева сверху последний перевёрнутый, справа —
        // текущий. Листы, которые сейчас крутятся, render() не трогает.
        function staticAngle(i) {
            return i < cur ? -180 : 0;
        }

        function placeSheet(s, i) {
            s.style.transform = staticAngle(i) ? 'rotateY(-180deg)' : '';
            s.classList.toggle('is-back', i < cur);
            s.style.zIndex = i < cur ? 10 + i : 10 + n - i;
            s.style.removeProperty('--shade');
            s.classList.remove('is-flipping');
        }

        function render() {
            sheets.forEach(function (s, i) {
                if (!s._flip) {
                    placeSheet(s, i);
                }
                s.classList.toggle('is-current', i === cur);
                s.setAttribute('aria-hidden', i === cur ? 'false' : 'true');
            });
            var s = sheets[cur].dataset;
            $('[data-docs-title]').innerHTML = s.title;
            $('[data-docs-date]').innerHTML = s.date;
            $('[data-docs-index]').textContent = cur + 1;
            $('[data-docs-prev]').disabled = cur === 0;
            $('[data-docs-next]').disabled = cur === n - 1;
            $('[data-docs-zoom-link]').href = s.scan;
            $('[data-docs-download]').href = s.download;
            $('[data-docs-download-label]').textContent = s.downloadLabel;
            root.classList.toggle('has-left', cur > 0);
            // ярлык группы: последний, чей первый лист не дальше текущего
            var tabs = root.querySelectorAll('.docs-folder__tab');
            var active = null;
            tabs.forEach(function (t) {
                if (+t.dataset.docsGo <= cur) {
                    active = t;
                }
            });
            tabs.forEach(function (t) {
                t.classList.toggle('is-current', t === active);
            });
        }

        // ── переворот листа ───────────────────────────────────────────────
        // Один шаг: вперёд — лист from уходит справа налево (0 → −180°),
        // назад — лист to возвращается слева направо (−180° → 0).
        var flips = [];     // идущие перевороты
        var zTop = 100;

        function makeFlip(from, to) {
            var fwd = to > from;
            var f = {fwd: fwd, to: to, sheet: sheets[fwd ? from : to], p: 0, raf: 0};
            f.sheet._flip = f;
            f.sheet.classList.add('is-flipping');
            // пока лист ждёт своей очереди — его место в стопке; поверх всех — когда тронется
            var idx = fwd ? from : to;
            f.sheet.style.zIndex = fwd ? 10 + n - idx : 10 + idx;
            [from, to].forEach(function (i) {
                sheets[i].querySelectorAll('img').forEach(loadImg);
            });
            return f;
        }

        function applyFlip(f, p) {
            f.p = p;
            var a = f.fwd ? -180 * p : -180 * (1 - p);
            f.sheet.style.transform = 'rotateY(' + a.toFixed(2) + 'deg)';
            // какая сторона видна — сами, а не backface-visibility: Chrome после
            // анимации на композитном слое иногда показывает лицо листа зеркально
            f.sheet.classList.toggle('is-back', a < -90);
            f.sheet.style.setProperty('--shade', (Math.sin(-a * Math.PI / 180) * .16).toFixed(3));
        }

        function endFlip(f) {
            cancelAnimationFrame(f.raf);
            clearTimeout(f.timer);
            f.sheet._flip = null;
            flips = flips.filter(function (x) { return x !== f; });
        }

        function stopAnim() {
            flips.slice().forEach(endFlip);
            zTop = 100;
            render();
        }

        // Довести переворот до target (1 — до конца, 0 — назад), с задержкой.
        // cur меняется, когда лист лёг (для одиночного) или заранее (для серии).
        function runFlip(f, target, ms, easing, delay, done) {
            flips.push(f);
            var go = function () {
                f.sheet.style.zIndex = ++zTop;
                var p0 = f.p;
                var dur = Math.max(ms * Math.abs(target - p0), 1);
                var t0 = performance.now();
                var step = function (now) {
                    var t = clamp((now - t0) / dur, 0, 1);
                    applyFlip(f, p0 + (target - p0) * easing(t));
                    if (t < 1) {
                        f.raf = requestAnimationFrame(step);
                        return;
                    }
                    endFlip(f);
                    if (done) {
                        done(target === 1);
                    }
                    if (!flips.length) {
                        zTop = 100;
                    }
                    render();
                };
                f.raf = requestAnimationFrame(step);
            };
            if (delay) {
                f.timer = setTimeout(go, delay);
            } else {
                go();
            }
        }

        // К листу i. Соседний — один переворот; дальний — серия быстрых
        // переворотов внахлёст: следующий лист трогается, когда предыдущий
        // прошёл примерно треть пути, вся серия укладывается в ~1 с.
        function go(i) {
            i = clamp(i, 0, n - 1);
            if (!isOpen || i === cur || drag) {
                return;
            }
            if (flips.length) {     // клик во время переворота — сначала доводим всё до места
                stopAnim();
            }
            if (reduce.matches) {
                cur = i;
                render();
                return;
            }
            var steps = Math.abs(i - cur);
            var fwd = i > cur;
            var ms = steps === 1 ? FLIP_MS : Math.max(420, 1000 / (1 + (steps - 1) * .35));
            var gap = steps === 1 ? 0 : ms * .35;
            var list = [];
            for (var k = 0; k < steps; k++) {
                var from = fwd ? cur + k : cur - k;
                list.push(makeFlip(from, fwd ? from + 1 : from - 1));
            }
            cur = i;
            render();   // подпись и ярлык — сразу к цели; крутящиеся листы стоят, где стояли
            list.forEach(function (f, k) {
                applyFlip(f, 0);
                runFlip(f, 1, ms, steps === 1 ? ease : easeInOutSoft, k * gap);
            });
        }

        function easeInOutSoft(t) {
            return t < .5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
        }

        // ── перетаскивание ────────────────────────────────────────────────
        sheetsBox.addEventListener('pointerdown', function (e) {
            if (!isOpen || flips.length || (e.pointerType === 'mouse' && e.button !== 0)) {
                return;
            }
            drag = {id: e.pointerId, x: e.clientX, y: e.clientY, t0: e.timeStamp, flip: null, dead: false, v: 0, lx: e.clientX, lt: e.timeStamp};
        });

        sheetsBox.addEventListener('pointermove', function (e) {
            if (!drag || e.pointerId !== drag.id || drag.dead) {
                return;
            }
            var dx = e.clientX - drag.x;
            var dy = e.clientY - drag.y;
            if (!drag.flip) {
                if (Math.abs(dx) < 7 && Math.abs(dy) < 7) {
                    return;
                }
                var to = dx < 0 ? cur + 1 : cur - 1;
                if (Math.abs(dy) > Math.abs(dx) || to < 0 || to >= n) {
                    drag.dead = true;   // вертикальный жест — это прокрутка, крайний лист — некуда
                    return;
                }
                drag.flip = makeFlip(cur, to);
                drag.flip.sheet.style.zIndex = ++zTop;
                root.classList.add('is-dragging');
                try {
                    sheetsBox.setPointerCapture(e.pointerId);
                } catch (err) { /* указатель уже отпущен */ }
            }
            var w = sheetsBox.clientWidth * 1.1;
            var p = clamp((drag.flip.fwd ? -dx : dx) / w, 0, 1);
            if (reduce.matches) {
                drag.flip.p = p;
            } else {
                applyFlip(drag.flip, p);
            }
            var dt = e.timeStamp - drag.lt;
            if (dt > 0) {
                drag.v = drag.v * .6 + ((e.clientX - drag.lx) / dt) * .4;
            }
            drag.lx = e.clientX;
            drag.lt = e.timeStamp;
        });

        function endDrag(e) {
            if (!drag || e.pointerId !== drag.id) {
                return;
            }
            var d = drag;
            drag = null;
            root.classList.remove('is-dragging');
            if (!d.flip) {
                return;
            }
            suppressClick = true;
            setTimeout(function () { suppressClick = false; }, 60);
            var f = d.flip;
            // скорость в сторону переворота, px/мс; короткий взмах — средняя за жест
            var span = d.lt - d.t0;
            var vx = span > 0 && span < 300 ? (d.lx - d.x) / span : d.v;
            var v = f.fwd ? -vx : vx;
            var done = e.type !== 'pointercancel' && (f.p > .4 || (v > .35 && f.p > .04)) && v > -.35;
            if (reduce.matches) {
                endFlip(f);
                if (done) {
                    cur = f.to;
                }
                render();
                return;
            }
            runFlip(f, done ? 1 : 0, FLIP_MS * .8, easeOut, 0, function (ok) {
                if (ok) {
                    cur = f.to;
                }
            });
        }

        sheetsBox.addEventListener('pointerup', endDrag);
        sheetsBox.addEventListener('pointercancel', endDrag);
        sheetsBox.addEventListener('dragstart', function (e) { e.preventDefault(); });

        // ── лайтбокс ──────────────────────────────────────────────────────
        function zoom() {
            var s = sheets[cur].dataset;
            if (!lb || typeof lb.showModal !== 'function') {
                window.open(s.scan, '_blank', 'noopener');
                return;
            }
            lbImg.src = s.scan;
            lbImg.alt = sheets[cur].querySelector('img').alt;
            lbCaption.innerHTML = s.title + ' · ' + s.date;
            lb.showModal();
        }

        if (lb) {
            lb.addEventListener('click', function (e) {
                if (e.target === lb || e.target.closest('[data-docs-lightbox-close]') || e.target.classList.contains('docs-folder__lightbox-body')) {
                    lb.close();
                }
            });
        }

        // ── клики ─────────────────────────────────────────────────────────
        root.addEventListener('click', function (e) {
            var t = e.target;
            if (t.closest('[data-docs-lightbox]')) {
                return;
            }
            // Карточка «Что хранится в папке»: к папке, и сразу на нужный лист
            var card = t.closest('[data-docs-card]');
            if (card) {
                e.preventDefault();
                var to = +card.dataset.docsCard;
                var header = document.querySelector('.header-wrapper.stuck, #header .stuck');
                var top = root.getBoundingClientRect().top + window.pageYOffset - (header ? header.getBoundingClientRect().height : 70);
                window.scrollTo({top: Math.max(0, top), behavior: reduce.matches ? 'auto' : 'smooth'});
                setTimeout(function () {
                    if (!isOpen) {
                        cur = to;
                        render();
                        setOpen(true);
                    } else {
                        go(to);
                    }
                }, reduce.matches ? 0 : 450);
                return;
            }
            var goBtn = t.closest('[data-docs-go]');
            if (goBtn) {
                var i = +goBtn.dataset.docsGo;
                if (!isOpen) {
                    cur = i;
                    render();
                    setOpen(true);
                } else {
                    go(i);
                }
                return;
            }
            if (t.closest('[data-docs-prev]')) {
                go(cur - 1);
                return;
            }
            if (t.closest('[data-docs-next]')) {
                go(cur + 1);
                return;
            }
            if (t.closest('[data-docs-close]')) {
                setOpen(false, true);
                return;
            }
            if (t.closest('[data-docs-toggle]')) {
                e.preventDefault();
                setOpen(!isOpen, e.detail === 0);
                return;
            }
            if (t.closest('[data-docs-zoom]') && isOpen) {
                e.preventDefault();
                if (!suppressClick) {
                    zoom();
                }
                return;
            }
            if (!isOpen && t.closest('[data-docs-photo-hit]')) {
                e.preventDefault();
                setOpen(true, e.detail === 0);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (!isOpen || (lb && lb.open) || e.altKey || e.ctrlKey || e.metaKey) {
                return;
            }
            if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                return;
            }
            var el = document.activeElement;
            if (el && (/^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName) || el.isContentEditable)) {
                return;
            }
            var r = root.getBoundingClientRect();
            if (r.bottom < 0 || r.top > window.innerHeight) {
                return;
            }
            e.preventDefault();
            go(cur + (e.key === 'ArrowRight' ? 1 : -1));
        });

        inert(inside, true);
        inert(panel, true);
        inert(sheetsBox, true);
        render();

        root.ugDocs = {
            open: function () { setOpen(true); },
            lift: function (deg) {      // кадр подъёма обложки для снимков
                root.classList.add('is-lifting');
                photo.style.setProperty('--lift', deg + 'deg');
            },
            close: function () { setOpen(false); },
            go: go,
            zoom: zoom,
            freeze: function (from, to, p) {     // лист посередине переворота (to = from ± 1)
                stopAnim();
                cur = Math.min(from, to) + (to > from ? 0 : 1);
                render();
                var f = makeFlip(from, to);
                f.sheet.style.zIndex = ++zTop;
                applyFlip(f, p);
            }
        };
    }

    document.querySelectorAll('[data-docs-folder]').forEach(init);
})();
