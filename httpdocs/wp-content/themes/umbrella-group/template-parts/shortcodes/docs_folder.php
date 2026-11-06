<?php
/**
 * [docs_folder] — страница «Документы компании» (/about/dokumenty-kompanii/, page 9322)
 * как интерактивная папка-регистратор.
 *
 * Слева хлебные крошки, h1, подзаголовок, подсказка и кнопка «Открыть папку»;
 * справа закрытая папка — картинка из макета (UG_DOCS_PHOTO) с кнопками-ярлыками
 * поверх. По клику обложка картинки приподнимается вокруг корешка (rotate3d,
 * стрелка-подсказка), и папка сменяется открытой, нарисованной CSS: справа
 * кольца и листы в прозрачных файлах, ярлыки; перевёрнутые листы ложатся
 * оборотом на изнанку обложки слева и копятся там стопкой. В углу изнанки —
 * «Закрыть папку». Листы перелистываются
 * стрелками, клавишами и перетаскиванием (assets/js/blocks/docs_folder.js).
 * Шапку page.php с крошками и h1 страница не выводит — см. has_shortcode() там.
 *
 * Без JS папка не открывается, но под кнопкой виден обычный список документов
 * со ссылками на сканы и PDF (класс docs-folder--nojs снимает инлайн-скрипт
 * сразу за открывающим тегом секции, поэтому с JS список не мигает).
 */

defined('ABSPATH') || exit;

/**
 * Документы — листы папки, по порядку. tab — ярлык, к которому относится лист:
 * несколько листов с одним ярлыком идут подряд, ярлык ведёт на первый из них.
 * scan — полный скан (лайтбокс и «Скачать», если нет pdf), sheet — копия
 * поменьше для листа, w × h — её размер. Файлы — в wp-content/uploads.
 * pdf — файл в uploads или путь от корня сайта: у свидетельства на знак и
 * свидетельства МоАП своего PDF не было, их PDF собраны из тех же сканов
 * (A4) и лежат в теме, assets/docs/.
 */
const UG_DOCS_FOLDER = [
    [
        'tab'   => 'Товарный знак',
        'card'  => ['Товарный знак', 'Свидетельство о&nbsp;регистрации товарного знака Umbrella Group', 'cert'], // «Что хранится в папке»: заголовок, текст, иконка
        'title' => 'Свидетельство на&nbsp;товарный знак UMBRELLA GROUP',
        'date'  => '20 сентября 2021',
        'sheet' => 'doc_tovarniy-znak-768x1086.jpg',
        'w'     => 768,
        'h'     => 1086,
        'scan'  => 'doc_tovarniy-znak.jpg',
        'pdf'   => '/wp-content/themes/umbrella-group/assets/docs/svidetelstvo-tovarnyj-znak.pdf',
    ],
    [
        'tab'   => 'СРО',
        'card'  => ['Членство в&nbsp;СРО', 'Документ о&nbsp;членстве в&nbsp;Ассоциации «Содружество»', 'people'], // «Что хранится в папке»: заголовок, текст, иконка
        'title' => 'Член СРО аудиторов Ассоциации «Содружество»',
        'date'  => '30 мая 2023',
        'sheet' => 'cert-2-min.jpg',
        'w'     => 635,
        'h'     => 899,
        'scan'  => 'cert-2-min.jpg',
        'pdf'   => 'Выписка-95222-ЮC_23.pdf', // «Ю» кириллицей, «C» латиницей — как в uploads
    ],
    [
        'tab'   => 'Страхование',
        'card'  => ['Страхование', 'Полис страхования аудиторской деятельности', 'shield'], // «Что хранится в папке»: заголовок, текст, иконка
        'title' => 'Страхование аудиторской деятельности',
        'date'  => 'до&nbsp;31 октября 2027',
        'sheet' => '/wp-content/themes/umbrella-group/assets/docs/polis-vsk-2026-2027-768.jpg',
        'w'     => 768,
        'h'     => 1086,
        'scan'  => '/wp-content/themes/umbrella-group/assets/docs/polis-vsk-2026-2027.jpg',
        'pdf'   => '/wp-content/themes/umbrella-group/assets/docs/polis-vsk-2026-2027.pdf', // САО «ВСК», № 2621037000007, 01.11.2026–31.10.2027
    ],
    [
        'tab'   => 'Сертификаты',
        'card'  => ['Контроль качества', 'Свидетельство о&nbsp;прохождении внешнего контроля качества', 'check'], // «Что хранится в папке»: заголовок, текст, иконка
        'title' => 'Контроль качества СРО НП «Московская аудиторская палата»',
        'date'  => '26 декабря 2014',
        'sheet' => 'cert-4-min-566x800.jpg',
        'w'     => 566,
        'h'     => 800,
        'scan'  => 'cert-4-min.jpg',
        'pdf'   => '/wp-content/themes/umbrella-group/assets/docs/kontrol-kachestva-moap.pdf',
    ],
];

/**
 * Закрытая папка — картинка Наташи (.tg-inbox/3524.png, прозрачный фон,
 * обрезана до 1374 × 884; тень — CSS). Координаты ниже — в пикселях этого
 * кадра: обложка (подъём и клик), лист под обложкой, ярлыки — каждый вырезан
 * отдельным слоем (-tabN.webp) и лежит поверх целой картинки ровно по месту.
 * Меняется картинка — пересчитать (скрипт разрезки: /tmp/docs-folder/v3/work/cut3.py).
 */
const UG_DOCS_PHOTO = 'assets/img/docs-folder';
const UG_DOCS_PHOTO_FRAME = [1374, 884];
const UG_DOCS_PHOTO_COVER = [[310, 6], [1162, 40], [1178, 58], [1182, 80], [1012, 772], [996, 786], [982, 788], [62, 706], [16, 700], [12, 685], [292, 14]];
const UG_DOCS_PHOTO_PAGES = '316,20 1162,54 1007,768 27,690';
const UG_DOCS_PHOTO_TABS = [[1138, 150, 236, 110], [1114, 260, 237, 100], [1090, 360, 243, 100], [1063, 460, 258, 112]];

const UG_DOCS_ICONS = [
    'doc'   => '<path d="M14 3.75H7c-.7 0-1.25.55-1.25 1.25v14c0 .7.55 1.25 1.25 1.25h10c.7 0 1.25-.55 1.25-1.25V8L14 3.75Z" stroke-linejoin="round"/><path d="M13.75 4v4.25H18M9 12.5h6M9 16h6" stroke-linecap="round"/>',
    'prev'  => '<path d="M19 12H5M11 6l-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/>',
    'next'  => '<path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>',
    'zoom'  => '<circle cx="10.75" cy="10.75" r="6"/><path d="m15.25 15.25 4.5 4.5" stroke-linecap="round"/>',
    'down'  => '<path d="M12 4v11M7 10.5l5 5 5-5M5 19.5h14" stroke-linecap="round" stroke-linejoin="round"/>',
    'close' => '<path d="m6 6 12 12M18 6 6 18" stroke-linecap="round"/>',
    'arrow' => '<path d="M4 12h15M14 7l5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/>',
    // иконки карточек «Что хранится в папке» — линейные, 24 × 24
    'cert'   => '<path d="M16 3.75H5.75v16.5h7.5M16 3.75l3.25 3.25v5.25M16 3.75V7h3.25" stroke-linejoin="round"/><path d="M8.5 8.5h5M8.5 11.5h7M8.5 14.5h3.5" stroke-linecap="round"/><circle cx="17.25" cy="16.25" r="2.75"/><path d="m15.6 18.5-.85 3.25 2.5-1.25 2.5 1.25-.85-3.25" stroke-linejoin="round"/>',
    'people' => '<circle cx="12" cy="7.75" r="2.75"/><path d="M7 19.25c0-3 2.25-5.25 5-5.25s5 2.25 5 5.25" stroke-linecap="round"/><circle cx="5.5" cy="9.75" r="2.1"/><path d="M2.25 18.75c0-2.35 1.5-4 3.65-4 .8 0 1.5.2 2.1.6" stroke-linecap="round"/><circle cx="18.5" cy="9.75" r="2.1"/><path d="M21.75 18.75c0-2.35-1.5-4-3.65-4-.8 0-1.5.2-2.1.6" stroke-linecap="round"/>',
    'shield' => '<path d="M12 3.25 4.75 6v5.75c0 4.25 2.9 7.6 7.25 9 4.35-1.4 7.25-4.75 7.25-9V6L12 3.25Z" stroke-linejoin="round"/><path d="m8.75 12.25 2.25 2.25 4.25-4.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'check'  => '<rect x="4.75" y="4.75" width="14.5" height="16.5" rx="1.5"/><rect x="9" y="3" width="6" height="3.25" rx=".75"/><path d="m8 10.25 1.25 1.25 2.25-2.25M13.75 10.5h2.75M8 15.75 9.25 17l2.25-2.25M13.75 16h2.75" stroke-linecap="round" stroke-linejoin="round"/>',
];

function ug_docs_icon(string $name, int $size = 18): string
{
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">' . UG_DOCS_ICONS[$name] . '</svg>';
}

/** Файл в uploads по имени или путь от корня сайта (файлы в теме, assets/docs/). */
function ug_docs_upload_url(string $file): string
{
    return str_starts_with($file, '/') ? home_url($file) : content_url('uploads/' . rawurlencode($file));
}

/** [адрес, подпись] кнопки скачивания: PDF, а если его нет — исходный файл скана. */
function ug_docs_download(array $doc): array
{
    $file = $doc['pdf'] !== '' ? $doc['pdf'] : $doc['scan'];
    $ext  = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
    return [esc_url(ug_docs_upload_url($file)), 'Скачать ' . ($ext === 'JPEG' ? 'JPG' : $ext)];
}

function ug_docs_folder_shortcode(): string
{
    $docs  = UG_DOCS_FOLDER;
    $total = count($docs);

    $sheets = $tabs = $fallback = $cards = '';
    $seen   = [];
    foreach ($docs as $i => $doc) {
        $n        = $i + 1;
        $title    = $doc['title'];
        $alt      = esc_attr(str_replace('&nbsp;', ' ', $title));
        $date     = $doc['date'];
        $scan     = esc_url(ug_docs_upload_url($doc['scan']));
        $src      = esc_url(ug_docs_upload_url($doc['sheet']));
        [$dl, $dlLabel] = ug_docs_download($doc);
        $active   = $i === 0 ? ' is-current' : '';

        $sheets .= '<figure class="docs-folder__sheet' . $active . '" id="docs-sheet-' . $n . '" data-docs-sheet="' . $i . '"'
            . ' data-title="' . esc_attr($title) . '" data-date="' . esc_attr($date) . '"'
            . ' data-scan="' . $scan . '" data-download="' . $dl . '" data-download-label="' . esc_attr($dlLabel) . '">'
            . '<div class="docs-folder__face docs-folder__face--front">'
            . '<a class="docs-folder__paper" href="' . $scan . '" target="_blank" rel="noopener" data-docs-zoom draggable="false" aria-label="Увеличить: ' . $alt . '">'
            . '<img src="' . $src . '" alt="' . $alt . '" width="' . $doc['w'] . '" height="' . $doc['h'] . '" loading="lazy" decoding="async" draggable="false">'
            . '</a>'
            . '</div>'
            // оборот листа: бумага в кармане, скан едва просвечивает зеркально
            . '<div class="docs-folder__face docs-folder__face--back" aria-hidden="true"><span class="docs-folder__paper docs-folder__paper--back">'
            . '<img src="' . $src . '" alt="" width="' . $doc['w'] . '" height="' . $doc['h'] . '" loading="lazy" decoding="async" draggable="false">'
            . '</span></div>'
            . '</figure>';

        if (!isset($seen[$doc['tab']])) {
            $seen[$doc['tab']] = true;
            $tabs .= '<button type="button" class="docs-folder__tab' . $active . '" data-docs-go="' . $i . '" data-docs-tab="' . esc_attr($doc['tab']) . '">'
                . '<span>' . esc_html($doc['tab']) . '</span></button>';
        }

        // Карточка «Что хранится в папке»: без JS — ссылка на скан, с JS — к листу в папке
        [$cTitle, $cText, $cIcon] = $doc['card'];
        $cards .= '<a class="docs-folder__card" href="' . $scan . '" target="_blank" rel="noopener" data-docs-card="' . $i . '">'
            . '<span class="docs-folder__card-icon">' . ug_docs_icon($cIcon, 46) . '</span>'
            . '<span class="docs-folder__card-body"><b class="docs-folder__card-title">' . $cTitle . '</b>'
            . '<span class="docs-folder__card-text">' . $cText . '</span>'
            . '<span class="docs-folder__card-link">Посмотреть документ' . ug_docs_icon('arrow', 18) . '</span></span>'
            . '</a>';

        $fallback .= '<li><a href="' . $scan . '" target="_blank" rel="noopener">' . $title . '</a>, ' . $date
            . ($doc['pdf'] !== '' ? ' (<a href="' . $dl . '" target="_blank" rel="noopener">PDF</a>)' : '')
            . '</li>';
    }

    // Ярлыки на картинке закрытой папки: по одному на группу, в порядке групп
    $hits  = '';
    [$fw, $fh] = UG_DOCS_PHOTO_FRAME;
    $px    = fn($v) => round($v * 100 / $fw, 3);
    $py    = fn($v) => round($v * 100 / $fh, 3);
    $photo = esc_url(get_stylesheet_directory_uri() . '/' . UG_DOCS_PHOTO);
    $groups = array_values(array_unique(array_column($docs, 'tab')));
    foreach ($groups as $g => $name) {
        if (!isset(UG_DOCS_PHOTO_TABS[$g])) {
            break;
        }
        [$x, $y, $w, $h] = UG_DOCS_PHOTO_TABS[$g];
        $go    = array_search($name, array_column($docs, 'tab'), true);
        $hits .= '<button type="button" class="docs-folder__hit" data-docs-go="' . $go . '"'
            . ' style="left:' . $px($x) . '%;top:' . $py($y) . '%;width:' . $px($w) . '%;height:' . $py($h) . '%"'
            . ' aria-label="Открыть папку: ' . esc_attr($name) . '">'
            . '<img src="' . $photo . '-tab' . ($g + 1) . '.webp" alt="" width="' . $w . '" height="' . $h . '" fetchpriority="high" decoding="async" draggable="false"></button>';
    }
    $lidClip = implode(', ', array_map(fn($p) => $px($p[0]) . '% ' . $py($p[1]) . '%', UG_DOCS_PHOTO_COVER));
    $lidPoly = implode(' ', array_map(fn($p) => $p[0] . ',' . $p[1], UG_DOCS_PHOTO_COVER));
    $pages   = UG_DOCS_PHOTO_PAGES;
    $view    = '0 0 ' . $fw . ' ' . $fh;

    $first = $docs[0];
    [$dl0, $dl0Label] = ug_docs_download($first);
    $scan0       = esc_url(ug_docs_upload_url($first['scan']));
    $breadcrumbs = get_breadcrumbs();
    $iPrev       = ug_docs_icon('prev');
    $iNext       = ug_docs_icon('next');
    $iZoom       = ug_docs_icon('zoom', 17);
    $iDown       = ug_docs_icon('down', 17);
    $iClose      = ug_docs_icon('close', 24);

    ug_enqueue_style('assets/css/blocks/docs_folder.css');
    ug_enqueue_script('assets/js/blocks/docs_folder.js', [], ['in_footer' => true, 'strategy' => 'defer']);

    return <<<EOHTML
    <section class="docs-folder docs-folder--nojs" data-docs-folder>
        <script>document.currentScript.parentNode.classList.remove('docs-folder--nojs');</script>
        <div class="docs-folder__inner">
            <div class="breadcrumbs">$breadcrumbs</div>
            <div class="docs-folder__body">
                <div class="docs-folder__text">
                    <h1 class="docs-folder__title">Документы компании</h1>
                    <p class="docs-folder__lead">Свидетельства, сертификаты и&nbsp;документы&nbsp;— в&nbsp;одной папке</p>
                    <p class="docs-folder__hint">Откройте папку, чтобы посмотреть документы</p>
                    <a href="#docs-folder-list" class="docs-folder__btn" data-docs-toggle aria-controls="docs-folder-binder" aria-expanded="false"><span data-docs-toggle-label>Открыть папку</span></a>
                    <ol class="docs-folder__fallback" id="docs-folder-list">$fallback</ol>
                </div>
                <div class="docs-folder__stage">
                    <div class="docs-folder__photo" data-docs-photo>
                        <picture>
                            <source type="image/webp" srcset="$photo.webp 1x, $photo-2x.webp 2x">
                            <img class="docs-folder__photo-img" src="$photo.png" width="$fw" height="$fh" alt="Красная папка-регистратор Umbrella Group «Документы компании» с ярлыками разделов" fetchpriority="high" decoding="async" draggable="false">
                        </picture>
                        <svg class="docs-folder__photo-pages" viewBox="$view" aria-hidden="true"><defs><linearGradient id="docs-folder-pages" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fbfbfc"/><stop offset="1" stop-color="#dfe2e6"/></linearGradient></defs><polygon points="$pages" fill="url(#docs-folder-pages)" stroke="#c9cdd2" stroke-width="2"/></svg>
                        <div class="docs-folder__lid-shadow" aria-hidden="true"><div class="docs-folder__lid" style="clip-path:polygon($lidClip)"></div></div>
                        <svg class="docs-folder__photo-top" viewBox="$view" aria-hidden="true">
                            <polygon class="docs-folder__photo-hit" data-docs-photo-hit points="$lidPoly"/>
                            <g class="docs-folder__hint-arrow"><path d="M1206 146C1228 86 1196 38 1128 26"/><path d="M1152 6 1126 26 1152 46"/></g>
                        </svg>
                        $hits
                    </div>
                    <div class="docs-folder__binder" id="docs-folder-binder">
                        <div class="docs-folder__tabs" role="group" aria-label="Разделы папки">$tabs</div>
                        <button type="button" class="docs-folder__close" data-docs-close>Закрыть папку</button>
                        <div class="docs-folder__back">
                            <div class="docs-folder__pocket" data-docs-pocket>
                                <div class="docs-folder__sheets" data-docs-sheets>$sheets</div>
                                <div class="docs-folder__rings" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
                            </div>
                        </div>
                        <div class="docs-folder__spine">
                            <div class="docs-folder__cover">
                                <div class="docs-folder__cover-in"></div>
                            </div>
                        </div>
                    </div>
                    <div class="docs-folder__panel" data-docs-panel>
                        <p class="docs-folder__caption" aria-live="polite"><b data-docs-title>{$first['title']}</b> <span data-docs-date>{$first['date']}</span></p>
                        <div class="docs-folder__controls">
                            <div class="docs-folder__pager">
                                <button type="button" class="docs-folder__arrow" data-docs-prev aria-label="Предыдущий документ" disabled>$iPrev</button>
                                <span class="docs-folder__count"><span data-docs-index>1</span>&nbsp;/&nbsp;$total</span>
                                <button type="button" class="docs-folder__arrow" data-docs-next aria-label="Следующий документ">$iNext</button>
                            </div>
                            <a class="docs-folder__action" href="$scan0" target="_blank" rel="noopener" data-docs-zoom data-docs-zoom-link>{$iZoom}<span>Увеличить</span></a>
                            <a class="docs-folder__action" href="$dl0" target="_blank" rel="noopener" download data-docs-download>{$iDown}<span data-docs-download-label>$dl0Label</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="docs-folder__contents">
                <h2 class="docs-folder__contents-title">Что хранится в&nbsp;папке</h2>
                <p class="docs-folder__contents-lead">Выберите раздел, чтобы перейти к&nbsp;нужному документу</p>
                <div class="docs-folder__cards">$cards</div>
            </div>
        </div>
        <dialog class="docs-folder__lightbox" data-docs-lightbox aria-label="Скан документа">
            <button type="button" class="docs-folder__lightbox-close" data-docs-lightbox-close aria-label="Закрыть">$iClose</button>
            <figure class="docs-folder__lightbox-body">
                <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="" data-docs-lightbox-img>
                <figcaption data-docs-lightbox-caption></figcaption>
            </figure>
        </dialog>
    </section>
    EOHTML;
}

add_shortcode('docs_folder', 'ug_docs_folder_shortcode');

// CSS папки — копией в <head>: это первый экран, без неё до подвала
// виден голый список (см. ug_paint_copy() в inc/assets.php). Картинку
// закрытой папки — в preload: это LCP страницы.
add_action('wp', function (): void {
    $post = get_queried_object();
    if (is_page() && $post instanceof WP_Post && has_shortcode($post->post_content, 'docs_folder')) {
        ug_paint_copy('assets/css/blocks/docs_folder.css');
        add_action('wp_head', function (): void {
            $base = get_stylesheet_directory_uri() . '/' . UG_DOCS_PHOTO;
            echo '<link rel="preload" as="image" type="image/webp" imagesrcset="' . esc_url($base . '.webp') . ' 1x, ' . esc_url($base . '-2x.webp') . ' 2x" fetchpriority="high">' . "\n";
        }, 2);
    }
});
