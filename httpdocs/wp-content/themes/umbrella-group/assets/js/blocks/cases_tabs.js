// Вкладки-«таблетки» и листалка ← 1 2 … 17 → — общие для /about/cases/
// (cases_page.js) и /about/clients/ (client_tiles.js); вёрстка — cases_tabs.css.
// Вынесено из cases_page.js без изменений поведения.
//
// ugTabsPager({tabs, cards, pager, list, perPage}) — карточкам нужен
// data-branch со списком вкладок через пробел. Возвращает {selectTab, render}:
// вызывающий сам решает, открыть ли вкладку из адреса или просто render().
window.ugTabsPager = function (o) {
    var tabs = o.tabs;
    var cards = Array.prototype.slice.call(o.cards);
    var pager = o.pager;
    var list = o.list;
    var PER_PAGE = o.perPage;
    var branch = 'all';
    var page = 1;

    function matching() {
        return cards.filter(function (card) {
            // data-branch — вкладки через пробел: карточка бывает в нескольких
            return branch === 'all' || card.dataset.branch.split(' ').indexOf(branch) !== -1;
        });
    }

    // Номера страниц: первая, последняя, текущая и соседние, между ними «…»
    function pageNumbers(total) {
        var out = [];
        for (var i = 1; i <= total; i++) {
            if (i === 1 || i === total || Math.abs(i - page) <= 1) {
                out.push(i);
            } else if (out[out.length - 1] !== '…') {
                out.push('…');
            }
        }
        return out;
    }

    function button(label, target, opts) {
        var b = document.createElement('button');
        b.type = 'button';
        b.innerHTML = label;
        b.dataset.page = target;
        if (opts.active) {
            b.className = 'is-active';
            b.setAttribute('aria-current', 'page');
        }
        if (opts.disabled) {
            b.disabled = true;
        }
        if (opts.title) {
            b.setAttribute('aria-label', opts.title);
        }
        return b;
    }

    function render() {
        var shown = matching();
        var total = Math.max(1, Math.ceil(shown.length / PER_PAGE));
        page = Math.min(page, total);
        cards.forEach(function (card) {
            card.hidden = true;
        });
        shown.slice((page - 1) * PER_PAGE, page * PER_PAGE).forEach(function (card) {
            card.hidden = false;
        });

        pager.innerHTML = '';
        if (total < 2) {
            return;
        }
        pager.appendChild(button('←', page - 1, {disabled: page === 1, title: 'Предыдущая страница'}));
        pageNumbers(total).forEach(function (n) {
            if (n === '…') {
                var gap = document.createElement('span');
                gap.textContent = '…';
                pager.appendChild(gap);
            } else {
                pager.appendChild(button(String(n), n, {active: n === page}));
            }
        });
        pager.appendChild(button('→', page + 1, {disabled: page === total, title: 'Следующая страница'}));
    }

    function selectTab(tab) {
        tabs.forEach(function (t) {
            t.classList.toggle('is-active', t === tab);
            t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
        });
        branch = tab.dataset.branch;
        page = 1;
        // На телефоне вкладки листаются лентой — выбранную подвинуть в видимую часть
        var strip = tab.parentNode;
        strip.scrollLeft = tab.offsetLeft - (strip.clientWidth - tab.offsetWidth) / 2;
        render();
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            selectTab(tab);
        });
    });

    pager.addEventListener('click', function (e) {
        var b = e.target.closest('button');
        if (!b || b.disabled) {
            return;
        }
        page = Number(b.dataset.page);
        render();
        // Начало списка ушло за верх экрана — вернуть к нему, иначе новая страница не видна
        var top = list.getBoundingClientRect().top;
        if (top < 0) {
            window.scrollTo({top: window.pageYOffset + top - 140, behavior: 'smooth'});
        }
    });

    return {selectTab: selectTab, render: render};
};
