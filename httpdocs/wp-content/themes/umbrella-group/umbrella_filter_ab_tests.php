<?php

function umbrella_filter_ab_tests($content)
{
    $ab_tags = umbrella_get_ab_test_tags();
    foreach ($ab_tags as $counter => $ab_tag) {
        $counter = $counter + 1;
        for ($i = 1; $i < 10; $i++) {
            $tag_to_remove = "umbrella_ab_test" . $counter . "_variant" . $i . "";
            if ($tag_to_remove != $ab_tag) {
                $content = umbrella_content_fix($content, $tag_to_remove);
//                if ($tag_to_remove=='umbrella_ab_test3_variant1'){print_r($content);}
                $pattern_to_remove = "/<" . $tag_to_remove . ">[\s\S]+?<\/" . $tag_to_remove . ">/";
                $content = preg_replace($pattern_to_remove, "", $content);
            } else {
                $content = umbrella_content_fix($content, $ab_tag);
//                if ($tag_to_remove=='umbrella_ab_test3_variant2'){print_r($content);}
                $content = umbrella_clear_ab_tags($content, $ab_tag);
            }
        }
    }
    return $content;
}

function umbrella_clear_ab_tags($content, $ab_tag): string
{
    $fix = [
        sprintf("<%s>", $ab_tag) => "",
        sprintf("</%s>", $ab_tag) => "",
    ];
    return strtr($content, $fix);
}

function umbrella_content_fix($content, $ab_tag): string
{
    $fix = [
        sprintf("<p></%s><br />", $ab_tag) => sprintf("</%s>", $ab_tag),
        sprintf("<p><%s><br />", $ab_tag) => sprintf("<%s>", $ab_tag),
        sprintf("<p></%s></p>", $ab_tag) => sprintf("</%s>", $ab_tag),
        sprintf("<%s></p>", $ab_tag) => sprintf("<%s>", $ab_tag),
        sprintf("</%s><br />", $ab_tag) => sprintf("</%s>", $ab_tag),
        sprintf("<%s><br />", $ab_tag) => sprintf("<%s>", $ab_tag),
    ];
    return strtr($content, $fix);
}

// umbrella_ab_test1_variant2 - ТЗ-2, 12 - новые экраны страниц услуг
// umbrella_ab_test1_variant1 - ТЗ-2, 12 - старые экраны страниц услуг
// umbrella_ab_test2_variant1 - ТЗ-2, 8.3, 8.4 - блоки акций
// umbrella_ab_test3_variant2 - ТЗ-2, 8.7, 10 - Изменен первый, добавлены 2ой и 3ий слайды на баннере на главной.
// umbrella_ab_test3_variant1 - ТЗ-2, 10 - старый вариант первого слайда
// umbrella_ab_test4_variant2 - ТЗ-2, 2.1, 2.2 - новый вариант меню
// umbrella_ab_test5_variant1 - ТЗ-2, 7 - Подписи гарантий на главной
//
// umbrella_ab_test6_variant1 - ТЗ-3, 3 - без доработки дизайна страницы цен
// umbrella_ab_test6_variant2 - ТЗ-3, 3 - с доработкой дизайна страницы цен
// umbrella_ab_test7_variant1 - ТЗ-3, 4 - без блока частые вопросы
// umbrella_ab_test8_variant2 - ТЗ-3, 5 - с предоплатой
// umbrella_ab_test8_variant1 - ТЗ-3, 5 - без предоплаты
// umbrella_ab_test9_variant2 - ТЗ-3, 8 - с блоком отзывов в футере на СУ
function umbrella_get_ab_test_tags(): array
{
    $ab_tests_array = [];

    // expVar - ТЗ-2
    // expVar1 - ТЗ-3

    // Принудительный вывод нового варианта ТЗ-2
    $expVar = "1";

    // Установка Cookie основываясь на параметр expvar в URL
    /*
    if (isset($_GET['expvar'])) {
        $expVar1 = $_GET['expvar'];
        setAbTestCookie("exp_test", $expVar1);
    } elseif (isset($_COOKIE['exp_test'])) {
        $expVar1 = $_COOKIE['exp_test'];
    } else {
        $expVar1 = "0";
        setAbTestCookie("exp_test",$expVar1);
    }
    */

    if (isset($_COOKIE['exp_mXyZSFCYQ-y-yfVchN_bSg'])) 
    {
        $expVar1 = $_COOKIE['exp_mXyZSFCYQ-y-yfVchN_bSg'];
    } 
    elseif (isset($GLOBALS['expVar1'])) 
    {
        $expVar1 = $GLOBALS['expVar1'];
        setAbTestCookie("exp_mXyZSFCYQ-y-yfVchN_bSg",$expVar1);
    }
    else 
    {
        $expVar1 = rand(0,1);
        $GLOBALS['expVar1'] = $expVar1;
        setAbTestCookie("exp_mXyZSFCYQ-y-yfVchN_bSg",$expVar1);
    }

    // ТЗ-2
    if ($expVar == '1') {
        array_push($ab_tests_array, 'umbrella_ab_test1_variant2', 'umbrella_ab_test2_variant1', 'umbrella_ab_test3_variant2', 'umbrella_ab_test4_variant2', 'umbrella_ab_test5_variant2');
    } else {
        array_push($ab_tests_array, 'umbrella_ab_test1_variant2', 'umbrella_ab_test2_variant1', 'umbrella_ab_test3_variant2', 'umbrella_ab_test4_variant2', 'umbrella_ab_test5_variant2');
    }

    // ТЗ-3
    if ($expVar1 == "1") {
        array_push($ab_tests_array, 'umbrella_ab_test6_variant2', 'umbrella_ab_test7_variant1', 'umbrella_ab_test8_variant2', 'umbrella_ab_test9_variant2');
    } else {
        array_push($ab_tests_array, 'umbrella_ab_test6_variant1', 'umbrella_ab_test7_nothing', 'umbrella_ab_test8_variant1', 'umbrella_ab_test9_nothing');
    }
    return $ab_tests_array;
}

function setAbTestCookie(string $cookieName, string $value): bool
{
    if (isset($_COOKIE[$cookieName])) {
        if (htmlspecialchars($_COOKIE[$cookieName])==$value){
            return true;
        }
    }
    $previously_cached =  wp_cache_get('umbrella_cookie_modified');
    if (isset($previously_cached) && $previously_cached==$cookieName) {
        return true;
    }
    wp_cache_set('umbrella_cookie_modified', $cookieName);
    return setcookie($cookieName, $value, time() + 3600 * 24 * 30, "/", ".taxlab.ru");
}