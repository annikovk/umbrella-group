/**
 * Лид-магнит «Чек-листы для клиник» — поп-ап #checklists из [ug_checklists_form]
 * (inc/leadmagnet.php).
 *
 * Открывается сам по ?checklist=1 или #checklists (QR-код с конференции) и по
 * переходу на #checklists без перезагрузки. На сервере это не решить: страницу
 * кэширует WP Rocket. После отправки формы (wpcf7mailsent именно этой формы,
 * не invalid/spam) вместо формы показываем кнопки скачивания PDF.
 *
 * Отправку помним 30 дней (localStorage ug_checklists_sent = время, без
 * личных данных): вернулся со страницы PDF, перезагрузил, открыл QR ещё раз —
 * сразу кнопки, форму заново не заполнять.
 */
(function ($) {
    'use strict';

    var ID = 'checklists';
    var FLAG = 'ug_checklists_sent';
    var TTL = 30 * 24 * 3600 * 1000;
    var popup = document.getElementById(ID);
    if (!popup || !$) {
        return;
    }
    var box = popup.querySelector('.ug-lm');

    function remembered() {
        try {
            var t = Number(localStorage.getItem(FLAG));
            if (t && Date.now() - t < TTL) {
                return true;
            }
            if (t) {
                localStorage.removeItem(FLAG);
            }
        } catch (e) { /* localStorage недоступен — просто покажем форму */ }
        return false;
    }

    function remember() {
        try {
            localStorage.setItem(FLAG, String(Date.now()));
        } catch (e) { /* приватный режим и т.п. */ }
    }

    /** Состояние «Готово»: форму прячем, кнопки показываем. */
    function showDone() {
        if (!box) {
            return null;
        }
        var done = box.querySelector('.ug-lm__done');
        box.classList.add('is-sent');
        if (done) {
            done.hidden = false;
        }
        return done;
    }

    if (remembered()) {
        showDone();
    }
    // Возврат из bfcache (iPhone «Назад» со страницы PDF): состояние «Готово» не теряем.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted && remembered()) {
            showDone();
        }
    });

    function wanted() {
        if (location.hash === '#' + ID) {
            return true;
        }
        try {
            return new URLSearchParams(location.search).get('checklist') === '1';
        } catch (e) {
            return /[?&]checklist=1(?:&|$)/.test(location.search);
        }
    }

    // Убрать триггер из адреса, чтобы повторный #checklists снова открыл
    // поп-ап, а «Обновить» после закрытия его не открывал. UTM-метки остаются.
    function clearTrigger() {
        if (!window.history || !history.replaceState) {
            return;
        }
        var search = location.search;
        try {
            var q = new URLSearchParams(search);
            q.delete('checklist');
            search = q.toString() ? '?' + q.toString() : '';
        } catch (e) { /* старый браузер — оставляем как есть */ }
        var hash = location.hash === '#' + ID ? '' : location.hash;
        history.replaceState(history.state, '', location.pathname + search + hash);
    }

    function show() {
        var mp = $.magnificPopup;
        if (mp && mp.instance && mp.instance.isOpen && mp.instance.content && mp.instance.content[0] === popup) {
            return;
        }
        mp && mp.close && mp.instance && mp.instance.isOpen && mp.close();
        var vars = window.flatsomeVars && flatsomeVars.lightbox ? flatsomeVars.lightbox : {};
        $.magnificPopup.open({
            midClick: true,
            removalDelay: 300,
            closeBtnInside: vars.close_btn_inside !== undefined ? vars.close_btn_inside : true,
            closeMarkup: vars.close_markup,
            items: {src: '#' + ID, type: 'inline'},
            callbacks: {afterClose: clearTrigger}
        });
    }

    function open() {
        if (remembered()) {
            showDone();
        }
        if ($.magnificPopup) {
            show();
        } else if ($.loadMagnificPopup) {
            // Flatsome 3.20 грузит magnific лениво — до первого поп-апа его нет
            $.loadMagnificPopup().then(show);
        }
    }

    $(function () {
        if (wanted()) {
            open();
        }
    });
    window.addEventListener('hashchange', function () {
        if (location.hash === '#' + ID) {
            open();
        }
    });

    document.addEventListener('wpcf7mailsent', function (e) {
        if (!box || !e.target || !box.contains(e.target)) {
            return;
        }
        remember();
        var done = showDone();
        if (done) {
            var first = done.querySelector('a');
            first && first.focus({preventScroll: true});
        }
        popup.scrollTop = 0;
        if (window.dataLayer && dataLayer.push) {
            dataLayer.push({event: 'checklists_sent'});
        }
    });
})(window.jQuery);
