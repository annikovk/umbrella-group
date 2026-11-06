// /about/clients/ ([ug_client_tiles]): вкладки отраслей и листалка по 6 карточек
// (3 × 2) — общие с /about/cases/, cases_tabs.js. #<slug отрасли> в адресе —
// сразу открыть вкладку.
document.addEventListener('DOMContentLoaded', function () {
    var root = document.querySelector('.client-tiles');
    if (!root) {
        return;
    }
    var tabsPager = window.ugTabsPager({
        tabs: root.querySelectorAll('.cases-page__tab'),
        cards: root.querySelectorAll('.client-tiles__card'),
        pager: root.querySelector('.cases-page__pager'),
        list: root.querySelector('.client-tiles__grid'),
        perPage: 6
    });

    var hash = location.hash.replace(/[^\w-]/g, '');
    var hashTab = hash && root.querySelector('.cases-page__tab[data-branch="' + hash + '"]');
    if (hashTab) {
        tabsPager.selectTab(hashTab);
    } else {
        tabsPager.render();
    }
});

// Окно отзыва клиента (<dialog class="client-review">, ug_client_review в
// 1031-client-tiles.php): открывается по клику на карточку и по #client-feedback-<ID>
// в адресе; закрывается крестиком, Esc и кликом по затемнению. Скан письма —
// второй <dialog> поверх (.client-review-zoom): «Увеличить документ» и клик
// по превью; закрыть его — вернуться к отзыву.
document.addEventListener('DOMContentLoaded', function () {
    var zoom = document.querySelector('.client-review-zoom');
    var reviews = document.querySelectorAll('.client-review');
    if (!reviews.length || typeof HTMLDialogElement === 'undefined') {
        return;
    }
    var html = document.documentElement;
    var zoomImg = zoom.querySelector('img');
    var lastCard = null;

    function openReview(dialog, card) {
        lastCard = card || null;
        html.classList.add('client-review-open');
        dialog.showModal();
        dialog.scrollTop = 0;
        // Фокус — на окно, а не на крестик: иначе вокруг него рамка
        dialog.querySelector('.client-review__box').focus({preventScroll: true});
    }

    document.querySelectorAll('a.client-tiles__card[aria-haspopup="dialog"]').forEach(function (card) {
        card.addEventListener('click', function (e) {
            var dialog = document.getElementById(card.getAttribute('href').slice(1));
            if (!dialog) {
                return;
            }
            e.preventDefault();
            openReview(dialog, card);
        });
    });

    reviews.forEach(function (dialog) {
        dialog.addEventListener('close', function () {
            html.classList.remove('client-review-open');
            if (lastCard) {
                lastCard.focus({preventScroll: true});
            }
        });
        dialog.addEventListener('click', function (e) {
            // Клик по ::backdrop приходит с target = сам <dialog>
            if (e.target === dialog || e.target.closest('[data-close]')) {
                dialog.close();
                return;
            }
            var z = e.target.closest('[data-zoom]');
            if (z) {
                e.preventDefault();
                zoomImg.src = z.getAttribute('href');
                zoomImg.alt = (z.querySelector('img') || dialog.querySelector('.client-review__scan img')).alt;
                zoom.showModal();
                zoom.scrollTop = 0;
                zoom.focus();
            }
        });
        // «Обсудить задачу» — форма из шапки, лайтбокс Flatsome. Поверх <dialog>
        // он не встанет: закрыть отзыв и нажать ссылку шапки (как в cases_page.js).
        // Capture и stopPropagation — чтобы Flatsome не открыл форму под окном
        dialog.addEventListener('click', function (e) {
            var link = e.target.closest('a[href="#header-contact-form-lightbox"]');
            if (!link) {
                return;
            }
            e.preventDefault();
            e.stopPropagation();
            lastCard = null;
            dialog.close();
            var header = document.querySelector('a[href="#header-contact-form-lightbox"]:not(.client-review a)');
            if (header) {
                setTimeout(function () {
                    header.click();
                }, 0);
            }
        }, true);
    });

    zoom.addEventListener('click', function (e) {
        // Клик по документу или мимо него — закрыть; документ крупнее экрана
        // прокручивается, поэтому по самой картинке не закрываем
        if (e.target !== zoomImg) {
            zoom.close();
        }
    });
    zoom.addEventListener('close', function () {
        zoomImg.removeAttribute('src');
    });

    var hash = location.hash.slice(1).replace(/[^\w-]/g, '');
    var fromHash = hash.indexOf('client-feedback-') === 0 && document.getElementById(hash);
    if (fromHash && fromHash.classList.contains('client-review')) {
        openReview(fromHash, document.querySelector('a.client-tiles__card[href="#' + hash + '"]'));
    }
});
