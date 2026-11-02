// Страница «Команда»: карусель руководства, строка вкладок со стрелками и карусель
// специалистов под ней. Вкладка оставляет в карусели карточки, у которых в
// data-tabs есть её ключ, и ставит заголовок из data-heading.

// Стрелки гаснут, когда листать в эту сторону некуда; если всё влезло — прячутся.
function ugTeamScroller(track, prev, next, step) {
    function update() {
        var max = track.scrollWidth - track.clientWidth - 2;
        prev.disabled = track.scrollLeft <= 2;
        next.disabled = track.scrollLeft >= max;
        prev.hidden = next.hidden = max <= 0;
    }
    [prev, next].forEach(function (arrow) {
        arrow.addEventListener('click', function () {
            track.scrollBy({left: step() * arrow.dataset.dir, behavior: 'smooth'});
        });
    });
    track.addEventListener('scroll', update, {passive: true});
    window.addEventListener('resize', update);
    update();
    return update;
}

// Каждая карусель листается своими стрелками; update — пересчёт после смены вкладки.
document.querySelectorAll('.team-leads').forEach(function (block) {
    var track = block.querySelector('.team-leads__track');
    var arrows = block.querySelectorAll('.team-leads__arrow');
    block.ugUpdate = ugTeamScroller(track, arrows[0], arrows[1], function () {
        var card = track.querySelector('.team-leads__card:not([hidden])');
        return card.offsetWidth + parseFloat(getComputedStyle(track).columnGap || 0);
    });
});

document.querySelectorAll('.team-list').forEach(function (list) {
    var tabsTrack = list.querySelector('.team-tabs__track');
    var tabsArrows = list.querySelectorAll('.team-tabs__arrow');
    var buttons = list.querySelectorAll('.team-tabs__btn');
    var block = list.querySelector('.team-leads');
    var track = block.querySelector('.team-leads__track');
    var cards = block.querySelectorAll('.team-leads__card');
    var heading = block.querySelector('.team-leads__heading');

    ugTeamScroller(tabsTrack, tabsArrows[0], tabsArrows[1], function () {
        return tabsTrack.clientWidth * 0.6;
    });

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.dataset.tab;
            buttons.forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            cards.forEach(function (card) {
                card.hidden = tab !== 'all' && card.dataset.tabs.split(' ').indexOf(tab) === -1;
                card.style.order = tab !== 'all' && card.hasAttribute('data-lead') ? -1 : '';
                if (card.dataset.title) {
                    card.querySelector('.team-leads__title').textContent = card.getAttribute('data-title-' + tab) || card.dataset.title;
                }
            });
            heading.textContent = btn.dataset.heading;
            track.scrollLeft = 0;
            block.ugUpdate();
            btn.scrollIntoView({block: 'nearest', inline: 'nearest', behavior: 'smooth'});
        });
    });
});

// Окно «Подробнее»: фото (data-full — отдельное большое; пустой data-full — без
// фото; нет атрибута — из карточки), имя,
// должность и теги копируем из карточки, описание — из её <template>. Закрывается крестиком, кликом по подложке и Esc.
document.querySelectorAll('.team-modal').forEach(function (modal) {
    var slot = function (name) {
        return modal.querySelector('.team-modal__' + name);
    };
    var lastCard = null;

    function close(keepFocus) {
        modal.hidden = true;
        document.documentElement.classList.remove('team-modal-open');
        if (lastCard && !keepFocus) {
            lastCard.querySelector('.team-leads__more').focus({preventScroll: true});
        }
    }

    function open(card) {
        var img = card.querySelector('.team-leads__img');
        var photo = slot('photo');
        photo.innerHTML = '';
        if (card.dataset.full) {
            var big = new Image();
            big.className = 'team-leads__img';
            big.alt = img ? img.alt : '';
            big.src = card.dataset.full;
            photo.appendChild(big);
        } else if (img && !card.hasAttribute('data-full')) {
            photo.innerHTML = img.outerHTML.replace(' loading="lazy"', '');
        }
        slot('name').textContent = card.querySelector('.team-leads__name').textContent;
        slot('title').textContent = card.querySelector('.team-leads__title').textContent;
        slot('tags').innerHTML = card.querySelector('.team-leads__tags').innerHTML;
        var list = card.closest('.team-list');
        var active = list && list.querySelector('.team-tabs__btn.is-active');
        var tpl = active && card.querySelector('template[data-tab="' + active.dataset.tab + '"]');
        slot('content').innerHTML = (tpl || card.querySelector('template:not([data-tab])')).innerHTML;
        lastCard = card;
        modal.hidden = false;
        document.documentElement.classList.add('team-modal-open');
        slot('close').focus();
    }

    document.querySelectorAll('.team-leads__card.has-more').forEach(function (card) {
        card.addEventListener('click', function () {
            open(card);
        });
    });
    modal.addEventListener('click', function (e) {
        if (e.target === modal || e.target.closest('.team-modal__close')) {
            close(false);
        }
    });
    // Форма заявки — лайтбокс Flatsome. Ссылку из нашего окна он не открывает,
    // поэтому закрываем окно и жмём такую же ссылку вне его (баннер ниже).
    slot('btn').addEventListener('click', function (e) {
        e.preventDefault();
        close(true);
        var link = document.querySelector('.team-cta__btn');
        setTimeout(function () {
            link.click();
        }, 0);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) {
            close(false);
        }
    });

    // /about/team/#specialist-11811 — сразу окно этого специалиста (ссылка
    // «Посмотреть специалиста» из окна кейса на «Кейсах»)
    var m = location.hash.match(/^#specialist-(\d+)$/);
    var target = m && document.querySelector('.team-leads__card.has-more[data-specialist="' + m[1] + '"]');
    if (target) {
        target.scrollIntoView({block: 'center'});
        open(target);
    }
});
