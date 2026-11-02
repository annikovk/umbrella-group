// /about/cases/: вкладки направлений, по 3 кейса на страницу и окно «Подробнее о кейсе».
// #Yuridicheskie_uslugi в адресе — сразу открыть вкладку (ссылки «Посмотрите
// примеры» в [trouble_2022] на главной).
document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('.cases-page');
    if (!root) {
        return;
    }
    var PER_PAGE = 3;
    var tabs = root.querySelectorAll('.cases-page__tab');
    var cards = Array.prototype.slice.call(root.querySelectorAll('.case-card'));
    var pager = root.querySelector('.cases-page__pager');
    var list = root.querySelector('.cases-page__list');
    var branch = 'all';
    var page = 1;

    function matching() {
        return cards.filter(function (card) {
            // data-branch — вкладки через пробел: кейс бывает в двух
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

    // Окно «Подробнее»: содержимое — из <template> своей карточки
    var modal = root.querySelector('.case-modal');
    var body = modal.querySelector('.case-modal__body');
    var caseView = null;

    function fill(tpl) {
        body.innerHTML = '';
        body.appendChild(tpl.content.cloneNode(true));
        // Lazy-load Flatsome прячет адрес в data-src, а до <template> его скрипт не доходит
        body.querySelectorAll('img[data-src]').forEach(function (img) {
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }
            img.src = img.dataset.src;
        });
    }

    // «Посмотреть специалиста» — карточка из «Команды» в этом же окне,
    // «← К кейсу» возвращает кейс на то же место прокрутки
    modal.addEventListener('click', function (e) {
        var btn = e.target.closest('.case-full__person-btn[data-specialist]');
        if (btn) {
            var spec = root.querySelector('.case-spec-tpl[data-specialist="' + btn.dataset.specialist + '"]');
            if (!spec) {
                return;
            }
            caseView = {nodes: Array.prototype.slice.call(body.childNodes), scroll: modal.scrollTop};
            fill(spec);
            modal.classList.add('case-modal--spec');
            modal.scrollTop = 0;
            body.focus();
            return;
        }
        if (e.target.closest('.case-spec__back') && caseView) {
            body.innerHTML = '';
            caseView.nodes.forEach(function (node) {
                body.appendChild(node);
            });
            modal.classList.remove('case-modal--spec');
            modal.scrollTop = caseView.scroll;
            caseView = null;
        }
    });
    root.addEventListener('click', function (e) {
        var more = e.target.closest('.case-card__more');
        if (!more) {
            return;
        }
        var tpl = more.closest('.case-card').querySelector('.case-card__details');
        fill(tpl);
        // Расширенное окно (заполнено «Что мы сделали») — шире обычного
        modal.classList.toggle('case-modal--wide', tpl.hasAttribute('data-wide'));
        modal.classList.remove('case-modal--spec');
        modal.showModal();
        // Иначе фокус встаёт на крестик и рисует вокруг него рамку
        body.focus();
    });
    modal.querySelector('.case-modal__close').addEventListener('click', function () {
        modal.close();
    });
    // «Обсудить вашу задачу» — форма из шапки. Flatsome вешает lightbox только
    // на ссылки, что были в DOM при загрузке, а эта пришла из <template>: закрыть
    // кейс (иначе форма встанет под <dialog>) и нажать ссылку шапки
    modal.addEventListener('click', function (e) {
        var link = e.target.closest('a[href="#header-contact-form-lightbox"]');
        if (!link) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        modal.close();
        var header = document.querySelector('a[href="#header-contact-form-lightbox"]:not(.case-modal a)');
        if (header) {
            header.click();
        }
    }, true);
    // Клик по затемнению вокруг окна закрывает его
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.close();
        }
    });

    var hash = location.hash.replace(/[^\w-]/g, '');
    var hashTab = hash && root.querySelector('.cases-page__tab[data-branch="' + hash + '"]');
    if (hashTab) {
        selectTab(hashTab);
        window.scrollTo(0, root.getBoundingClientRect().top + window.pageYOffset - 120);
    } else {
        render();
    }
});
