/*
 * Цели Roistat на ссылках «Скачать анкету» / «Скачать памятку» — бывший
 * инлайн-скрипт в начале <body> header.php, без изменений логики.
 * Подключается defer после jQuery (inc/layout.php).
 */
jQuery(function ($) {
    $('div.col.medium-4.small-12.large-4 a span').each(function (key, val) {
        if ($(val).text() == 'Скачать анкету') {
            $(val).closest('a').attr('onclick', 'roistat.event.send(\'anketa\');');
        }
    });
    $('.col-inner a span').each(function (key, val) {
        if ($(val).text() == 'Скачать памятку') {
            $(val).closest('a').attr('onclick', 'roistat.event.send(\'pam\');');
        }
    });
});
