<?php
/**
 * Сниппет insert-php #9123 «Левый заголовок в Footer» → [ug_footer_form_title].
 * Заголовок формы в подвале темы (template-parts/shortcodes/umbrella_footer.php)
 * по разделу из URL.
 */

declare(strict_types=1);

function ug_snippet_footer_form_title(array $a): void
{
    $uri = $_SERVER['REQUEST_URI'];
    $subheader = '<p class="footer-h2">';
    $p_end = "</p>";
    if (strpos($uri, "/services/audit/konsultatsiya") !== false) {
        echo $subheader . "Задайте вопрос аудитору и получите консультацию!" . $p_end;
    } else if (strpos($uri, "/services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2/") !== false) {
        echo $subheader . "Задайте вопрос ведущему бухгалтеру!" . $p_end;
    } else if (strpos($uri, "/services/services-le/%d1%8e%d1%80%d0%b8%d0%b4%d0%b8%d1%87%d0%b5%d1%81%d0%ba%d0%b0%d1%8f-%d0%ba%d0%be%d0%bd%d1%81%d1%83%d0%bb%d1%8c%d1%82%d0%b0%d1%86%d0%b8%d1%8f") !== false) {
        echo $subheader . "Получите оценку стоимости и сроков решения по Вашей ситуации!" . $p_end;
    } else if (strpos($uri, "services/audit") !== false) {
        echo $subheader . "Получите оценку стоимости <br/> и сроков аудита" . $p_end;
    } else if (strpos($uri, "services/bukhgalterskie-uslugi/") !== false) {
        echo $subheader . "Бесплатная консультация ведущего бухгалтера" . $p_end;
    } else if (strpos($uri, "services/licensing/") !== false) {
        echo $subheader . "Бесплатная консультация специалиста по лицензированию" . $p_end;
    } else if (strpos($uri, "services/services-le") !== false) {
        echo $subheader . "Консультация юриста" . $p_end;
    } else if (strpos($uri, "services/register-elimination/uvedomlenie-rkn/") !== false) {
        echo "Бесплатная консультация по подаче уведомления в Роскомнадзор";
    } else {
        echo $subheader . "Бесплатная консультация специалиста" . $p_end;
    }
}

ug_snippet_shortcode('ug_footer_form_title', 'ug_snippet_footer_form_title');
