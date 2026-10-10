<?php
/**
 * Сниппет insert-php #9110 «Заголовок в форме в Footer» → [ug_footer_form_subtitle].
 * Подзаголовок формы подвала (CF7 639) и <style>, который прячет пункты
 * выпадающего списка «вопрос аудитору / бухгалтеру / юристу» не своего раздела.
 */

declare(strict_types=1);

function ug_snippet_footer_form_subtitle(array $a): void
{
    $uri = $_SERVER['REQUEST_URI'];
    $subheader = '<div class="footer-form-subheader">';
    $div_end = "</div>";
    $text = "Мы позвоним Вам в ближайшее время и проконсультируем! Режим работы: с 09:00 до 18:00 по Новосибирску.";
    $style = "<style>.audit-consult-question, .buhuchet-question, .le-question {display: none;} </style>";
    if (strpos($uri, "/services/audit/konsultatsiya") !== false) {
        $style = "<style>.buhuchet-question, .le-question{display: none;} </style>";
    } else if (strpos($uri, "/services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2/") !== false) {
        $style = "<style>.audit-consult-question, .le-question{display: none;} </style>";
    } else if (strpos($uri, "/services/services-le/%d1%8e%d1%80%d0%b8%d0%b4%d0%b8%d1%87%d0%b5%d1%81%d0%ba%d0%b0%d1%8f-%d0%ba%d0%be%d0%bd%d1%81%d1%83%d0%bb%d1%8c%d1%82%d0%b0%d1%86%d0%b8%d1%8f") !== false) {
        $style = "<style>.audit-consult-question, .buhuchet-question {display: none;} </style>";
    }
    echo $subheader . $text . $div_end;
    echo $style;
}

ug_snippet_shortcode('ug_footer_form_subtitle', 'ug_snippet_footer_form_subtitle');
