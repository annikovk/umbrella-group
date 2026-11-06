// /about/cases/: вкладки направлений, по 3 кейса на страницу (cases_tabs.js) и окно «Подробнее о кейсе».
// #Yuridicheskie_uslugi в адресе — сразу открыть вкладку (ссылки «Посмотрите
// примеры» в [trouble_2022] на главной).
document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('.cases-page');
    if (!root) {
        return;
    }
    // Вкладки и листалка по 3 кейса — cases_tabs.js (общие с /about/clients/)
    var tabsPager = window.ugTabsPager({
        tabs: root.querySelectorAll('.cases-page__tab'),
        cards: root.querySelectorAll('.case-card'),
        pager: root.querySelector('.cases-page__pager'),
        list: root.querySelector('.cases-page__list'),
        perPage: 3
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
        tabsPager.selectTab(hashTab);
        window.scrollTo(0, root.getBoundingClientRect().top + window.pageYOffset - 120);
    } else {
        tabsPager.render();
    }
});
