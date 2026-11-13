/**
 * Реквизиты /rekvizity/ — вкладки организаций (якорь в адресе: #akkura),
 * копирование в буфер (все реквизиты и отдельные значения). «Скачать
 * карточку» — обычная ссылка на PDF (tools/rekvizity-cards.mjs). Разметка —
 * inc/rekvizity.php, стили — assets/css/pages/rekvizity.css.
 */
(function () {
    'use strict';

    function init() {
        var root = document.getElementById('ugr');
        if (!root) {
            return;
        }
        var tabs = Array.prototype.slice.call(root.querySelectorAll('.ugr-tab'));
        var panels = Array.prototype.slice.call(root.querySelectorAll('.ugr-org'));
        var live = root.querySelector('.ugr-live');

        function say(text) {
            if (!live) {
                return;
            }
            live.textContent = '';
            setTimeout(function () { live.textContent = text; }, 50);
        }

        // ── Вкладки ──────────────────────────────────────────────────────────

        function select(id, focus) {
            var found = false;
            tabs.forEach(function (tab) {
                var on = tab.getAttribute('href') === '#' + id;
                found = found || on;
                tab.classList.toggle('is-active', on);
                tab.setAttribute('aria-selected', on ? 'true' : 'false');
                tab.tabIndex = on ? 0 : -1;
                if (on && focus) {
                    tab.focus();
                }
                var bar = tab.parentNode;
                if (on && bar.scrollWidth > bar.clientWidth) {
                    bar.scrollLeft += tab.getBoundingClientRect().left - bar.getBoundingClientRect().left - 15;
                }
            });
            if (!found) {
                return false;
            }
            panels.forEach(function (p) {
                if (p.getAttribute('data-org') === id) {
                    p.removeAttribute('data-off');
                } else {
                    p.setAttribute('data-off', '');
                }
            });
            return true;
        }

        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                var id = tab.getAttribute('href').slice(1);
                select(id, false);
                if (history.replaceState) {
                    history.replaceState(null, '', i === 0 ? location.pathname + location.search : '#' + id);
                }
            });
            tab.addEventListener('keydown', function (e) {
                var to = null;
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    to = tabs[(i + 1) % tabs.length];
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    to = tabs[(i - 1 + tabs.length) % tabs.length];
                } else if (e.key === 'Home') {
                    to = tabs[0];
                } else if (e.key === 'End') {
                    to = tabs[tabs.length - 1];
                } else if (e.key === ' ' || e.key === 'Spacebar') {
                    e.preventDefault();
                    tab.click();
                    return;
                }
                if (to) {
                    e.preventDefault();
                    to.click();
                    to.focus();
                }
            });
        });

        function fromHash() {
            var id = decodeURIComponent(location.hash.slice(1));
            // Элемента с id = якорю нет (панель — #ugr-<id>), так что браузер
            // не прокручивает страницу: вкладки и так в первом экране.
            if (id) {
                select(id, false);
            }
        }
        fromHash();
        window.addEventListener('hashchange', fromHash);

        // ── Копирование ──────────────────────────────────────────────────────

        function copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text).catch(function () { return legacyCopy(text); });
            }
            return legacyCopy(text);
        }

        function legacyCopy(text) {
            return new Promise(function (resolve, reject) {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.cssText = 'position:fixed;top:0;left:0;width:1px;height:1px;opacity:0';
                document.body.appendChild(ta);
                ta.select();
                var ok = false;
                try {
                    ok = document.execCommand('copy');
                } catch (err) {
                    ok = false;
                }
                document.body.removeChild(ta);
                if (ok) {
                    resolve();
                } else {
                    reject(new Error('copy'));
                }
            });
        }

        function flash(btn) {
            btn.classList.add('is-done');
            clearTimeout(btn._ugrT);
            btn._ugrT = setTimeout(function () { btn.classList.remove('is-done'); }, 2000);
        }

        /** [[подпись, значение], …] панели — в порядке на странице. */
        function rows(panel) {
            return Array.prototype.map.call(panel.querySelectorAll('.ugr-f'), function (f) {
                return [f.querySelector('.ugr-f__k').textContent.trim(), f.querySelector('.ugr-f__t').textContent.trim()];
            });
        }

        function head(panel) {
            var full = panel.querySelector('.ugr-org__full');
            return {
                name: panel.querySelector('.ugr-org__name').textContent.trim(),
                full: full ? full.textContent.trim() : ''
            };
        }

        function plain(panel) {
            var h = head(panel);
            var out = [h.full || h.name];
            if (h.full && h.full !== h.name) {
                out.push('(' + h.name + ')');
            }
            rows(panel).forEach(function (r) { out.push(r[0] + ': ' + r[1]); });
            return out.join('\n');
        }

        root.addEventListener('click', function (e) {
            var one = e.target.closest('.ugr-f__copy');
            var all = e.target.closest('[data-copy-all]');
            if (one) {
                copyText(one.getAttribute('data-copy')).then(function () {
                    flash(one);
                    say('Скопировано');
                }, function () { say('Не удалось скопировать'); });
            } else if (all) {
                var t = all.querySelector('.ugr-btn__t');
                copyText(plain(all.closest('.ugr-org'))).then(function () {
                    flash(all);
                    t.textContent = t.getAttribute('data-done');
                    setTimeout(function () { t.textContent = t.getAttribute('data-t'); }, 2000);
                    say('Реквизиты скопированы');
                }, function () { say('Не удалось скопировать'); });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
