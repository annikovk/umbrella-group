// «Наша специализация» на странице «О компании»: вкладка слева показывает свою
// панель справа, остальные прячет. На телефоне панель под списком — доводим до неё экран.
document.querySelectorAll('.about-spec').forEach(function (block) {
    var tabs = block.querySelectorAll('.about-spec__tab');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) {
                var on = t === tab;
                t.classList.toggle('is-active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
            });
            if (window.innerWidth < 850) {
                block.querySelector('.about-spec__panels').scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });
    });
});

// «История компании»: год открывает окно с событиями по наведению (CSS), а по нажатию —
// закрепляет его, чтобы работало и на планшете. Второе нажатие, Esc или клик мимо — закрывают.
document.querySelectorAll('.about-history__tree').forEach(function (tree) {
    var nodes = tree.querySelectorAll('.about-history__node');
    function close(except) {
        nodes.forEach(function (n) {
            if (n !== except) {
                n.classList.remove('is-open');
                n.querySelector('.about-history__year').setAttribute('aria-expanded', 'false');
            }
        });
    }
    nodes.forEach(function (node) {
        var btn = node.querySelector('.about-history__year');
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            close(node);
            var open = node.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.about-history__card')) {
            close(null);
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            close(null);
        }
    });
});
