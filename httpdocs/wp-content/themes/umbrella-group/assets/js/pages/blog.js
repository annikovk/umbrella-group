/**
 * Живой поиск по статьям блога (home.php, inc/blog.php).
 *
 * С 2 символов, через 300 мс после ввода, тянет HTML той же страницы поиска
 * (/?s=…&post_type=post[&category_name=…]) и подменяет крошки, h1 с
 * подзаголовком и колонку со списком и пагинацией; адрес — replaceState.
 * Пустое поле (стёрли, крестик, Enter) — сразу раздел без поиска: /blog/
 * или текущее направление. Ответ на устаревший запрос отбрасывается.
 * Без JS форма уходит по Enter, пустой запрос сервер редиректит на раздел.
 */
(function () {
    'use strict';

    var form = document.querySelector('.blog-search');
    var input = form && form.querySelector('.blog-search__input');
    if (!input || !window.fetch || !window.DOMParser || !window.URLSearchParams) {
        return;
    }
    var clear = form.querySelector('.blog-search__clear');
    var baseUrl = form.getAttribute('data-base-url');
    var MIN = 2, DELAY = 300;
    var timer = 0, seq = 0, ctrl = null, last = null;

    function fieldValue(name) {
        var el = form.querySelector('input[name="' + name + '"]');
        return el ? el.value : '';
    }

    function urlFor(q) {
        var order = fieldValue('order');
        if (q === '') {
            var u = new URL(baseUrl, location.href);
            if (order) { u.searchParams.set('order', order); }
            return u.toString();
        }
        var p = new URLSearchParams();
        p.set('s', q);
        p.set('post_type', 'post');
        var cat = fieldValue('category_name');
        if (cat) { p.set('category_name', cat); }
        if (order) { p.set('order', order); }
        return new URL(form.getAttribute('action'), location.href).toString().split('?')[0] + '?' + p.toString();
    }

    // Flatsome lazy-load: в подгруженной разметке адрес картинки может быть в data-src.
    function showImages(root) {
        root.querySelectorAll('img[data-src]').forEach(function (img) {
            img.src = img.getAttribute('data-src');
            if (img.getAttribute('data-srcset')) { img.srcset = img.getAttribute('data-srcset'); }
            img.removeAttribute('data-src');
            img.removeAttribute('data-srcset');
            img.classList.remove('lazy-load');
        });
    }

    function swap(doc) {
        ['.blog-crumbs', '.blog-head__text', '.blog-main'].forEach(function (sel) {
            var from = doc.querySelector(sel), to = document.querySelector(sel);
            if (from && to) { to.innerHTML = from.innerHTML; }
        });
        if (doc.title) { document.title = doc.title; }
        showImages(document.querySelector('.blog-main'));
    }

    function run(q) {
        clearTimeout(timer);
        var url = urlFor(q);
        if (url === last) { return; }
        last = url;
        var my = ++seq;
        if (ctrl) { ctrl.abort(); }
        ctrl = window.AbortController ? new AbortController() : null;
        var main = document.querySelector('.blog-main');
        if (main) { main.setAttribute('aria-busy', 'true'); }
        fetch(url, { credentials: 'same-origin', signal: ctrl ? ctrl.signal : undefined })
            .then(function (r) { return r.ok || r.status === 404 ? r.text() : Promise.reject(r.status); })
            .then(function (html) {
                if (my !== seq) { return; }
                swap(new DOMParser().parseFromString(html, 'text/html'));
                history.replaceState(null, '', url);
            })
            .catch(function () {
                if (my === seq) { last = null; }
            })
            .then(function () {
                var m = document.querySelector('.blog-main');
                if (my === seq && m) { m.removeAttribute('aria-busy'); }
            });
    }

    function sync() {
        if (clear) { clear.hidden = input.value === ''; }
    }

    input.addEventListener('input', function () {
        sync();
        var q = input.value.trim();
        clearTimeout(timer);
        if (q === '') { run(''); return; }
        if (q.length < MIN) { return; }
        timer = setTimeout(function () { run(q); }, DELAY);
    });

    // Нативный крестик type=search (Safari/Chrome) шлёт search без input в старых браузерах.
    input.addEventListener('search', function () {
        sync();
        if (input.value.trim() === '') { run(''); }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        sync();
        run(input.value.trim());
    });

    if (clear) {
        clear.addEventListener('click', function () {
            input.value = '';
            sync();
            run('');
            input.focus();
        });
    }

    last = location.href;
})();
