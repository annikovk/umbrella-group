/**
 * Стоимость /price/ — разметка из [ug_price_page] (inc/price.php).
 *
 * Весь прайс уже в HTML. Здесь: одно направление за раз (адрес — #<id>,
 * старые якоря #section-… тоже работают), по 6 услуг на страницу с
 * пагинацией (data-p у строк и заголовков групп считает сервер),
 * «Подробнее» раскрывает «Что входит», поиск «Найти услугу» — по всем
 * направлениям сразу, «Рассчитать» открывает форму поп-апом (Flatsome lightbox #ugp-calc) с подставленной услугой.
 */
(function () {
    'use strict';

    var root = document.getElementById('ugp');
    if (!root) {
        return;
    }
    root.classList.add('is-js');

    var dirs = Array.prototype.slice.call(root.querySelectorAll('.ugp-dir'));
    var navItems = Array.prototype.slice.call(root.querySelectorAll('.ugp-nav__item'));
    var found = root.querySelector('.ugp-found');
    var input = root.querySelector('.ugp-search__input');
    var clear = root.querySelector('.ugp-search__clear');
    var current = dirs[0] ? dirs[0].getAttribute('data-dir') : '';
    var pages = {};

    function byId(id) {
        for (var i = 0; i < dirs.length; i++) {
            if (dirs[i].getAttribute('data-dir') === id) {
                return dirs[i];
            }
        }
        return null;
    }

    /** #lic, #section-licensing → lic */
    function dirFromHash() {
        var h = decodeURIComponent(location.hash.replace(/^#/, ''));
        if (!h) {
            return '';
        }
        for (var i = 0; i < navItems.length; i++) {
            if (navItems[i].getAttribute('data-dir') === h || navItems[i].getAttribute('data-alias') === h) {
                return navItems[i].getAttribute('data-dir');
            }
        }
        return '';
    }

    function renderPager(dir) {
        var nav = dir.querySelector('.ugp-pager');
        var total = parseInt(dir.getAttribute('data-pages'), 10) || 1;
        var id = dir.getAttribute('data-dir');
        var cur = pages[id] || 1;
        if (total < 2) {
            nav.innerHTML = '';
            return;
        }
        var html = '';
        var link = function (p, text, label) {
            return '<a class="page-numbers" href="#' + id + '" data-page="' + p + '"' + (label ? ' aria-label="' + label + '"' : '') + '>' + text + '</a>';
        };
        if (cur > 1) {
            html += link(cur - 1, '←', 'Предыдущая страница');
        }
        for (var p = 1; p <= total; p++) {
            html += p === cur ? '<span class="page-numbers current" aria-current="page">' + p + '</span>' : link(p, String(p));
        }
        if (cur < total) {
            html += link(cur + 1, '→', 'Следующая страница');
        }
        nav.innerHTML = html;
    }

    function setPage(dir, page) {
        pages[dir.getAttribute('data-dir')] = page;
        var els = dir.querySelectorAll('.ugp-row, .ugp-grp');
        for (var i = 0; i < els.length; i++) {
            els[i].classList.toggle('is-cur', parseInt(els[i].getAttribute('data-p'), 10) === page);
        }
        renderPager(dir);
    }

    function showDir(id) {
        var dir = byId(id) || dirs[0];
        if (!dir) {
            return;
        }
        current = dir.getAttribute('data-dir');
        dirs.forEach(function (d) {
            d.classList.toggle('is-active', d === dir);
        });
        navItems.forEach(function (a) {
            var on = a.getAttribute('data-dir') === current;
            a.classList.toggle('is-active', on);
            if (on) {
                a.setAttribute('aria-current', 'true');
                var list = a.closest('.ugp-nav__list');
                if (list && list.scrollWidth > list.clientWidth) {
                    list.scrollTo({left: a.parentNode.offsetLeft - 15, behavior: 'smooth'});
                }
            } else {
                a.removeAttribute('aria-current');
            }
        });
        setPage(dir, pages[current] || 1);
    }

    /** Прокрутить к началу списка, если он выше экрана. */
    function scrollToMain() {
        var main = root.querySelector('.ugp-layout');
        var top = main.getBoundingClientRect().top;
        if (top < 0) {
            window.scrollTo({top: window.pageYOffset + top - 150, behavior: 'smooth'});
        }
    }

    // ── Поиск ────────────────────────────────────────────────────────────
    function norm(s) {
        return s.toLowerCase().replace(/ё/g, 'е').replace(/\s+/g, ' ').trim();
    }

    function plural(n) {
        var m10 = n % 10, m100 = n % 100;
        return m10 === 1 && m100 !== 11 ? 'услуга' : (m10 >= 2 && m10 <= 4 && (m100 < 12 || m100 > 14) ? 'услуги' : 'услуг');
    }

    function escapeHtml(s) {
        return s.replace(/[&<>"]/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'}[c];
        });
    }

    function search(q) {
        var words = norm(q).split(' ').filter(Boolean);
        clear.hidden = q === '';
        if (norm(q).length < 2) {
            found.hidden = true;
            dirs.forEach(function (d) {
                d.classList.remove('is-search');
                Array.prototype.forEach.call(d.querySelectorAll('.is-miss'), function (el) {
                    el.classList.remove('is-miss');
                });
            });
            showDir(current);
            return;
        }
        var total = 0;
        dirs.forEach(function (d) {
            var hits = 0;
            var group = null;
            // строки и заголовки групп — в порядке документа; заголовок группы
            // виден, если в ней есть найденная строка (повторы --cont скрыты CSS)
            Array.prototype.forEach.call(d.querySelectorAll('.ugp-row, .ugp-grp'), function (el) {
                if (el.classList.contains('ugp-grp')) {
                    el.classList.add('is-miss');
                    if (!el.classList.contains('ugp-grp--cont')) {
                        group = el;
                    }
                    return;
                }
                var text = el.getAttribute('data-search') || '';
                var ok = words.every(function (w) {
                    return text.indexOf(w) !== -1;
                });
                el.classList.toggle('is-miss', !ok);
                if (ok) {
                    hits++;
                    if (group) {
                        group.classList.remove('is-miss');
                    }
                }
            });
            d.classList.add('is-search');
            d.classList.toggle('is-active', hits > 0);
            total += hits;
        });
        navItems.forEach(function (a) {
            a.classList.remove('is-active');
        });
        found.hidden = false;
        found.innerHTML = total
            ? 'По запросу «' + escapeHtml(q.trim()) + '» — ' + total + ' ' + plural(total)
            : 'По запросу «' + escapeHtml(q.trim()) + '» ничего не нашлось. <a href="#ugp-form">Опишите задачу — рассчитаем<span class="ugp-arr" aria-hidden="true">→</span></a>';
    }

    function resetSearch() {
        if (input.value !== '') {
            input.value = '';
        }
        search('');
    }

    input.addEventListener('input', function () {
        search(input.value);
    });
    input.form.addEventListener('submit', function (e) {
        e.preventDefault();
        search(input.value);
    });
    clear.addEventListener('click', function () {
        resetSearch();
        input.focus();
    });

    // ── Форма: поп-ап #ugp-calc (та же CF7, что внизу) ───────────────────
    var popup = document.getElementById('ugp-calc');
    var opener = root.querySelector('.ugp-pop__open');
    var select = popup ? popup.querySelector('select[name="price-service"]') : null;
    var task = popup ? popup.querySelector('textarea[name="price-task"]') : null;
    var hint = null;
    var taskPlaceholder = task ? task.getAttribute('placeholder') : '';

    Array.prototype.forEach.call(document.querySelectorAll('.ugp-cf7box select[name="price-service"]'), function (sel) {
        sel.addEventListener('change', function () {
            sel.classList.toggle('is-filled', sel.value !== '');
            if (sel === select && hint) {
                hint.hidden = true;
            }
        });
    });
    if (select) {
        hint = document.createElement('p');
        hint.className = 'ugp-fld__hint';
        hint.hidden = true;
        select.closest('.wpcf7-form-control-wrap').insertAdjacentElement('afterend', hint);
    }

    // reCAPTCHA v3 изредка отдаёт в браузере токен с ошибкой (siteverify:
    // browser-error) — CF7 считает заявку спамом, письмо не уходит. Такую
    // отправку повторяем один раз с новым токеном (wpcf7.submit обёрнут
    // модулем reCAPTCHA: он сам запросит токен). Спам писем не шлёт, так что
    // дубля заявки не будет.
    document.addEventListener('wpcf7spam', function (e) {
        var f = e.target;
        if (!f || !f.closest || !f.closest('.ugp-cf7box') || f.getAttribute('data-ugp-retried') === '1') {
            return;
        }
        f.setAttribute('data-ugp-retried', '1');
        var out = f.querySelector('.wpcf7-response-output');
        if (out) {
            out.textContent = 'Отправляем ещё раз…';
        }
        setTimeout(function () {
            if (window.wpcf7 && typeof window.wpcf7.submit === 'function') {
                window.wpcf7.submit(f);
            }
        }, 800);
    });
    document.addEventListener('wpcf7mailsent', function (e) {
        if (e.target && e.target.removeAttribute) {
            e.target.removeAttribute('data-ugp-retried');
            // CF7 очистил форму — подсказка «Подставили из таблицы» и подсветка выбора уже не к месту
            Array.prototype.forEach.call(e.target.querySelectorAll('select.is-filled'), function (sel) {
                sel.classList.remove('is-filled');
            });
            Array.prototype.forEach.call(e.target.querySelectorAll('.ugp-fld__hint'), function (h) {
                h.hidden = true;
            });
        }
    });

    /** Подставить услугу (или сбросить выбор) и открыть поп-ап. */
    function openCalc(option, dirId) {
        if (!select || !opener) {
            return false;
        }
        var has = option !== '' && Array.prototype.some.call(select.options, function (o) {
            return o.value === option;
        });
        select.value = has ? option : '';
        select.classList.toggle('is-filled', has);
        var dir = dirId ? byId(dirId) : null;
        if (has && dir) {
            hint.innerHTML = 'Подставили из таблицы · <b>' + escapeHtml(dir.getAttribute('data-name')) + '</b> — можно выбрать другую';
            hint.hidden = false;
        } else {
            hint.hidden = true;
        }
        if (task && !task.value) {
            var ex = has && dir && dir.getAttribute('data-example');
            task.setAttribute('placeholder', ex || taskPlaceholder);
        }
        // после прошлой отправки в окне остаётся «Спасибо…» — убираем
        var out = popup.querySelector('.wpcf7-response-output');
        var wrap = popup.querySelector('.wpcf7-form');
        if (wrap && wrap.getAttribute('data-status') === 'sent') {
            wrap.setAttribute('data-status', 'init');
            wrap.classList.remove('sent');
            wrap.classList.add('init');
            if (out) {
                out.textContent = '';
            }
        }
        opener.click();
        return true;
    }

    // ── Клики ────────────────────────────────────────────────────────────
    root.addEventListener('click', function (e) {
        var a = e.target.closest('a, button');
        if (!a || !root.contains(a)) {
            return;
        }
        if (a.classList.contains('ugp-nav__item')) {
            e.preventDefault();
            resetSearch();
            showDir(a.getAttribute('data-dir'));
            history.replaceState(null, '', '#' + current);
            scrollToMain();
            return;
        }
        if (a.hasAttribute('data-page')) {
            e.preventDefault();
            // ссылка пагинации пересоздаётся в setPage — направление берём до
            var pageDir = a.closest('.ugp-dir');
            setPage(pageDir, parseInt(a.getAttribute('data-page'), 10));
            var head = pageDir.querySelector('.ugp-tbl');
            var top = head.getBoundingClientRect().top;
            if (top < 0) {
                window.scrollTo({top: window.pageYOffset + top - 150, behavior: 'smooth'});
            }
            return;
        }
        if (a.classList.contains('ugp-row__more') && a.tagName === 'BUTTON') {
            var row = a.closest('.ugp-row');
            var open = row.classList.toggle('is-open');
            a.setAttribute('aria-expanded', open ? 'true' : 'false');
            a.firstChild.nodeValue = open ? 'Свернуть' : 'Подробнее';
            a.querySelector('.ugp-arr').textContent = open ? '↑' : '↓';
            return;
        }
        if (a.getAttribute('href') === '#ugp-form') {
            // без поп-апа (нет формы) — обычный переход к форме внизу
            if (openCalc(a.getAttribute('data-option') || '', a.getAttribute('data-dir') || '')) {
                e.preventDefault();
            }
        }
    });

    window.addEventListener('hashchange', function () {
        var id = dirFromHash();
        if (id && id !== current) {
            resetSearch();
            showDir(id);
        }
    });

    showDir(dirFromHash() || current);
})();
