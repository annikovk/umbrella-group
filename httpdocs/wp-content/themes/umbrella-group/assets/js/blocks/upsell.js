// Допродажа после заявки: шаблон печатает includes/upsell.php.
document.addEventListener('wpcf7mailsent', function (event) {
    const template = document.getElementById('umbrella-upsell');
    // CF7 шлёт событие с <form>, а прячем её целиком — работаем с обёрткой .wpcf7
    const container = event.target.closest('.wpcf7') || event.target;
    if (!template) return;

    // Форма в попапе: подменяем её подборкой прямо в этом попапе.
    if (container.closest('.lightbox-by-id, .mfp-content')) {
        if (container.querySelector('.umbrella-upsell')) return;
        container.classList.add('umbrella-upsell-shown');
        container.appendChild(template.content.cloneNode(true));
        return;
    }

    // Форма в тексте страницы: прячем плашку CF7 и открываем подборку
    // таким же попапом, как у тематических лайтбоксов flatsome.
    container.classList.add('umbrella-upsell-sent');
    const box = document.createElement('div');
    box.className = 'lightbox-content lightbox-white';
    box.style.maxWidth = '400px';
    box.appendChild(template.content.cloneNode(true));

    if (!window.jQuery || !jQuery.magnificPopup) {
        container.appendChild(box);
        return;
    }
    jQuery.magnificPopup.open({
        removalDelay: 300,
        closeBtnInside: window.flatsomeVars ? flatsomeVars.lightbox.close_btn_inside : true,
        closeMarkup: window.flatsomeVars ? flatsomeVars.lightbox.close_markup : undefined,
        items: {src: box, type: 'inline'}
    });
});
