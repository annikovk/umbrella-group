<?php
/**
 * UTM-атрибуция заявок (код OMA Intelsib): источник/канал в cookie source_cookie,
 * строка $html для шорткодов [omatraffic]/[omacid] в формах CF7.
 *
 * Перенесено из functions.php:18-124 дословно (WS-0). Исполняется при загрузке
 * файла и пишет глобальные переменные: omatraffic_cf7_func() читает
 * $GLOBALS['html']. Поэтому файл подключается require'ом на верхнем уровне
 * (functions.php → inc/bootstrap.php → сюда), не из функции. Переписывает WS-1.
 */

//OMA INTELSIB CODE
$search_systems = array('google', 'yandex', 'mail.ru', 'rambler', 'bing');
$important_source = false;
$source_array = array(
    'medium' => null,
    'source' => null,
    'campaign' => null,
    'term' => null,
    'content' => null
);

if (isset($_GET['utm_medium'])) {
    $important_source = true;
    $source_array['medium'] = $_GET['utm_medium'];
    $source_array['source'] = isset($_GET['utm_source']) ? $_GET['utm_source'] : null;
    $source_array['campaign'] = isset($_GET['utm_campaign']) ? $_GET['utm_campaign'] : null;
    $source_array['term'] = isset($_GET['utm_term']) ? $_GET['utm_term'] : null;
    $source_array['content'] = isset($_GET['utm_content']) ? $_GET['utm_content'] : null;
} else if (isset($_GET['yclid'])) {
    $important_source = true;
    $source_array['medium'] = 'cpc';
    $source_array['source'] = 'yandex';
} else if (isset($_GET['gclid'])) {
    $important_source = true;
    $source_array['medium'] = 'cpc';
    $source_array['source'] = 'google';
} else if (isset($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], 'yabs.yandex')) {
    $important_source = true;
    $source_array['medium'] = 'cpc';
    $source_array['source'] = 'yandex';
} else if (!empty($_SERVER['HTTP_REFERER'])) {
    preg_match('/(?:https?:\/\/)?(.*?)[:\/$]/', $_SERVER['HTTP_REFERER'], $referrer);
    $referrer = preg_match('/\.(.*\.\w*)/', $referrer[1], $new_referrer) ? $new_referrer[1] : $referrer[1];
    preg_match('/(?:https?:\/\/)?(.*?)(:|\/|$)/', $_SERVER['HTTP_HOST'], $host);
    $host = preg_match('/\.(.*\.\w*)/', $host[1], $new_host) ? $new_host[1] : $host[1];
    if ($host != $referrer) {
        $important_source = true;
        $source_array['source'] = $referrer;
        //Если реферал содержит маркет, проставляем канал контекст
        if (strpos($referrer, 'market.yandex') > -1) {
            $source_array['medium'] = 'cpc';
        } else {
            //Если реферал - не поисковая система проставляем канал рефералы
            $source_array['medium'] = 'referral';
            foreach ($search_systems as $search_system) {
                if (strpos($referrer, $search_system) > -1) {
                    $organic = true;
                    $source_array['medium'] = 'organic';
                    $source_array['source'] = $search_system;
                    break;
                }
            }
        }
    } else {
        $source_array['source'] = 'none';
        $source_array['medium'] = 'direct';
    }
} else {
    $source_array['source'] = 'none';
    $source_array['medium'] = 'direct';
}

if (!$important_source && isset($_COOKIE['source_cookie'])) {
    $source_cookie = unserialize(stripslashes($_COOKIE['source_cookie']));
    if ($source_cookie && !empty($source_cookie['medium'])) {
        $source_array = $source_cookie;
    }
}

setcookie("source_cookie", serialize($source_array), time() + 3600 * 24 * 30, "/");
$html = "Источник - $source_array[source], Канал - $source_array[medium]";
$html .= $source_array['campaign'] ? ", Кампания - $source_array[campaign]" : null;
$html .= $source_array['term'] ? ", Объявление - $source_array[term]" : null;
$html .= $source_array['content'] ? ", Ключевое слово - $source_array[content]" : null;
$_SESSION['traffic_source'] = $html;

add_filter('wpcf7_form_elements', 'do_shortcode');

function omatraffic_cf7_func()
{

    return $GLOBALS['html'];
}

function omacid_cf7_func()
{
    $cid = "";
    if (isset($_COOKIE['_gaexp'])) {
        $cid = $_COOKIE['_gaexp'];
    }
    return $cid;
}

add_shortcode('omatraffic', 'omatraffic_cf7_func');
add_shortcode('omacid', 'omacid_cf7_func');

add_filter('wpcf7_mail_components', 'do_shortcode_mail', 10, 3);
function do_shortcode_mail($components, $contactForm, $mailComponent)
{
    if (isset($components['body'])) {
        $components['body'] = do_shortcode($components['body']);
    }

    return $components;
}

//END OMA INTELSIB CODE
