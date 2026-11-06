/* [contacts_page] — «Показать на карте»: виджет 2ГИС тот же (iframe
 * widgets.2gis.com, type=firmsonmap, обе карточки офисов), меняется только
 * центр — pos в options. Карта прокручивается в кадр, если её не видно.
 * Без JS ссылка открывает карточку офиса в 2ГИС. */
(function () {
    var frame = document.querySelector('.contacts-map__frame');
    if (!frame) return;
    var base = JSON.parse(frame.getAttribute('data-dg-options') || '{}');
    var widget = function (pos) {
        base.pos = pos;
        frame.src = 'https://widgets.2gis.com/widget?type=firmsonmap&options=' + encodeURIComponent(JSON.stringify(base));
    };

    // На телефоне при zoom 13 метки у самых краёв — стартуем на масштаб мельче.
    // iframe lazy и до прокрутки не грузится, так что подмена src бесплатна.
    if (window.matchMedia('(max-width: 849px)').matches && base.pos) {
        widget({lat: base.pos.lat, lon: base.pos.lon, zoom: 12});
    }

    document.addEventListener('click', function (e) {
        var link = e.target.closest('.contacts-office__map[data-lat]');
        if (!link) return;
        e.preventDefault();

        widget({lat: +link.dataset.lat, lon: +link.dataset.lon, zoom: 16});

        document.querySelectorAll('.contacts-office.is-active').forEach(function (el) {
            el.classList.remove('is-active');
        });
        link.closest('.contacts-office').classList.add('is-active');

        var r = frame.getBoundingClientRect();
        if (r.top < 0 || r.bottom > window.innerHeight) {
            frame.parentNode.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    });
})();
