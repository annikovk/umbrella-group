<?php
/**
 * Счётчики темы — единственное место (спека 1, §3.1, WS-3 шаг 4).
 *
 *     get_template_part('template-parts/analytics', null, ['part' => 'head', 'variant' => …]);
 *     get_template_part('template-parts/analytics', null, ['part' => 'body', 'variant' => …]);
 *
 * Состав счётчиков не меняется (решение владельца, §10 п. 1), меняется только
 * форма: вместо 8 копий загрузчика marquiz по REQUEST_URI — один загрузчик и
 * таблица «адрес → квиз»; протокольно-относительные //script.marquiz.ru,
 * //quiz.marquiz.ru и //counter.yadro.ru → https://; jQuery-блок ROISTAT из
 * начала <body> — defer-файл assets/js/roistat-links.js (inc/layout.php).
 *
 * Не здесь (настройки Flatsome в theme_mods, печатает Flatsome): GTM-WW2PFC,
 * Pixel VK, Метрика, ROISTAT-счётчик, venyoo, qoopler.
 *
 * Вариант business (page-bussnise.php, страница 14148) — как было в
 * header-bussnise.php: gtag.js грузится по UA-168903290-1, без G-L1SYSHHPK5,
 * плюс событие «Over 15 seconds» через 15 с.
 */

$ug_part    = $args['part'] ?? 'head';
$ug_variant = $args['variant'] ?? 'default';

if ($ug_part === 'body') : ?>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TRFKKFL"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<!--LiveInternet counter-->
<script type="text/javascript">
    new Image().src = "https://counter.yadro.ru/hit?r" +
        escape(document.referrer) + ((typeof (screen) == "undefined") ? "" :
            ";s" + screen.width + "*" + screen.height + "*" + (screen.colorDepth ?
            screen.colorDepth : screen.pixelDepth)) + ";u" + escape(document.URL) +
        ";h" + escape(document.title.substring(0, 150)) +
        ";" + Math.random();</script>
<!--/LiveInternet-->
<?php
    return;
endif;

if ($ug_variant === 'business') : ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-168903290-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-168903290-1', {
            'custom_map': {
                'dimension1': 'clientId'
            }
        });
        setTimeout(function () {
            gtag('event', 'Over 15 seconds', {
                event_category: "NoBounce"
            });
        }, 15000);
    </script>
<?php else : ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L1SYSHHPK5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-L1SYSHHPK5');
        gtag('config', 'UA-168903290-1', {
            'custom_map': {
                'dimension1': 'clientId'
            }
        });
    </script>
<?php endif; ?>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TRFKKFL');
    </script>
    <!-- End Google Tag Manager -->
<?php
/*
 * Marquiz: какой квиз на какой странице — 1:1 с прежними восемью ветками
 * header.php. Сравнение адреса — как было: побайтовое совпадение REQUEST_URI
 * (с query string; кириллица — %-кодами в верхнем регистре, как у rawurlencode:
 * ссылки WordPress с %d0%… в нижнем регистре сюда не попадали и не попадают),
 * потом подстроки register-elimination и licensing.
 */
$ug_quiz_default = [
    'host'           => 'https://quiz.marquiz.ru',
    'region'         => 'eu',
    'id'             => '5f8423a5d8d13200441acb88',
    'autoOpen'       => false,
    'autoOpenFreq'   => 'once',
    'openOnExit'     => true,
    'disableOnMobile' => false,
];
$ug_quiz_602 = array_merge($ug_quiz_default, ['id' => '602f350e7914c10044581a97']);
$ug_quiz_pop = [
    'delay'    => 2,
    'type'     => 'side',
    'position' => 'position_top-left',
    'rounded'  => true,
    'blicked'  => true,
];
$ug_quizzes = [
    '/blog/' . rawurlencode('приглашаем-на-бесплатный-вебинар-за') . '/' => ['init' => $ug_quiz_602],
    '/blog/' . rawurlencode('банкротство-некоммерческих-организа') . '/' => ['init' => $ug_quiz_602],
    '/blog/' . rawurlencode('ликвидация-общественного-объединени') . '/' => ['init' => $ug_quiz_602],
    '/blog/' . rawurlencode('принудительная-ликвидация-индивидуа') . '/' => ['init' => $ug_quiz_602],
    '/services/services-le/' => [
        'init' => array_merge($ug_quiz_default, ['autoOpen' => 30, 'disableOnMobile' => true]),
        'pop'  => [
            'id'         => '5f8423a5d8d13200441acb88',
            'title'      => 'Пройдите опрос',
            'text'       => 'Получите бесплатную консультацию',
            'textColor'  => '#ffffff',
            'bgColor'    => '#ff0000',
            'svgColor'   => '#ffffff',
            'closeColor' => '#ffffff',
            'bonusCount' => 0,
            'bonusText'  => '',
            'shadow'     => 'rgba(255, 0, 0, 0)',
        ] + $ug_quiz_pop,
    ],
    '/services/bukhgalterskie-uslugi/obsluzhivanie/' => [
        'init' => array_merge($ug_quiz_default, ['id' => '6102660bfe7cb6003ecc09b9', 'autoOpen' => 30, 'disableOnMobile' => true]),
        'pop'  => [
            'id'         => '6102660bfe7cb6003ecc09b9',
            'title'      => 'Рассчитайте за 1 минуту',
            'text'       => 'Стоимость бухгалтерии на аутсорсинге',
            'textColor'  => '#fff',
            'bgColor'    => '#ec1c23',
            'svgColor'   => '#fff',
            'closeColor' => '#ffffff',
            'bonusCount' => 1,
            'bonusText'  => 'Вам доступен бонус и скидка',
            'shadow'     => 'rgba(236, 28, 35, 0)',
        ] + $ug_quiz_pop,
    ],
];

$ug_uri  = (string) ($_SERVER['REQUEST_URI'] ?? '');
$ug_quiz = $ug_quizzes[$ug_uri] ?? null;
if ($ug_quiz === null) {
    if (str_contains($ug_uri, 'register-elimination')) {
        // Как было: загрузчик стартует по DOMContentLoaded.
        $ug_quiz = ['init' => $ug_quiz_602, 'on_ready' => true];
    } elseif (!str_contains($ug_uri, 'licensing') && !preg_match('~/services/intellektualnoe-pravo/[^/?]~', $ug_uri)) {
        // страницы, перенесённые из «Лицензирования» в «Интеллектуальное право» (2026-10-23), — без квиза, как раньше
        $ug_quiz = ['init' => $ug_quiz_default];
    }
}

if ($ug_quiz !== null) :
    $ug_json = static fn(array $data): string => (string) wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    ?>
    <!-- Marquiz script start -->
    <script>
        (function (w, d, s, o, ready) {
            function load() {
                var j = d.createElement(s);
                j.async = true;
                j.src = 'https://script.marquiz.ru/v2.js';
                j.onload = function () {
                    if (d.readyState !== 'loading') Marquiz.init(o);
                    else d.addEventListener('DOMContentLoaded', function () {
                        Marquiz.init(o);
                    });
                };
                d.head.insertBefore(j, d.head.firstElementChild);
            }
            if (ready) d.addEventListener('DOMContentLoaded', load);
            else load();
        })(window, document, 'script', <?php echo $ug_json($ug_quiz['init']); ?>, <?php echo empty($ug_quiz['on_ready']) ? 'false' : 'true'; ?>);
<?php if (isset($ug_quiz['pop'])) : ?>
        (function (t, p) {
            window.Marquiz
                ? Marquiz.add([t, p])
                : document.addEventListener('marquizLoaded', function () {
                    Marquiz.add([t, p]);
                });
        })('Pop', <?php echo $ug_json($ug_quiz['pop']); ?>);
<?php endif; ?>
    </script>
    <!-- Marquiz script end -->
<?php endif;
