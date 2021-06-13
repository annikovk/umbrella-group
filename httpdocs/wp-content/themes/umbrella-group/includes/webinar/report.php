<?php
if (!wp_next_scheduled('webinar_task_hook')) {
    wp_schedule_event(strtotime('01:20:00'), 'daily', 'webinar_task_hook');
}
add_action('webinar_task_hook', 'webinar_task_function');
function webinar_task_function()
{
    $date = date('Y-m-d', strtotime("-1 days"));
    $xlsxFileName = 'Отчет о регистрациях.xlsx';
    $htmlreportlastday = html_report($date);
    if (!empty($htmlreportlastday) && (strpos( $htmlreportlastday, 'не было регистраций' ) == false)) {
        generate_xlsx_report($xlsxFileName);
        send_report_email('n.kilina@taxlab.ru', $htmlreportlastday, $date, $xlsxFileName);
        //send_report_email('jvv@taxlab.ru', $htmlreportlastday, $date, $xlsxFileName);
        send_report_email('annikovk@gmail.com', $htmlreportlastday, $date, $xlsxFileName);
        unlink($xlsxFileName);
    } else {
        return 0;
    }
}

register_deactivation_hook(__FILE__, 'webinar_schedule_deactivate');

function webinar_schedule_deactivate()
{
    // when the last event was scheduled
    $timestamp = wp_next_scheduled('webinar_daily_cron_job');
    // unschedule previous event if any
    wp_unschedule_event($timestamp, 'webinar_daily_cron_job');
}


function generate_xlsx_report($xlsxFileName)
{
    include_once(get_theme_file_path() . '/includes/webinar/SimpleXLSXGen.php');
    global $wpdb;
    $file = (new SimpleXLSXGen);
    $query = " SELECT webinar_date 
               FROM (
                SELECT webinar_date,date_submitted FROM r17Y1_webinar_register_log  
                ORDER BY date_submitted DESC      
               ) AS sub
    GROUP BY webinar_date 
    ";
    $webinar_dates=$wpdb->get_results($query);
    $participants_common = [['ФИО', 'Телефон', 'E-Mail', 'Компания', 'ИНН', 'Дата Регистрации', 'Название семинара', 'Дата семинара']];

    foreach ($webinar_dates as $webinar_date){
        $results = $wpdb->get_results("SELECT * FROM r17Y1_webinar_register_log WHERE webinar_date='".$webinar_date->webinar_date."' ORDER BY date_submitted DESC");
        $participants = [['ФИО', 'Телефон', 'E-Mail', 'Компания', 'ИНН', 'Дата Регистрации', 'Название семинара', 'Дата семинара']];
        foreach ($results as $row) {
            $string = [$row->surname . ' ' . $row->name . ' ' . $row->patronymic, $row->mobile_number, $row->email, $row->company_name, $row->inn, date('d.m.Y', strtotime($row->date_submitted)), $row->webinar_title , $row->webinar_date ];
            array_push($participants, $string);
            array_push($participants_common, $string);
        }
        $file->addSheet( $participants, 'Семинар '.$webinar_date->webinar_date);
    }
    $file->addSheet( $participants_common, 'Общий список');
    $file->saveAs($xlsxFileName);
//    SimpleXLSXGen::fromArray($books)->saveAs($xlsxFileName);
}

function html_report($date)
{
    $debug = $date;
    if ($date == 'all') {
        $datesql = '';

    } else {
        $datesql = ' WHERE date_submitted LIKE "' . $date . '%"';
    }
    global $wpdb;
    $results = $wpdb->get_results('SELECT * FROM r17Y1_webinar_register_log' . $datesql . ' ORDER BY date_submitted DESC');
    if (!empty($results)) {
        $count = sizeof($results);
        $content = date('d.m.Y', strtotime($date)) . " было зарегестрировано " . $count . " пользователей: ";
        $content .= "<table>";
        $content .= "
                </tr>
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>E-mail</th>
                    <th>Компания</th>
                    <th>ИНН</th>
                    <th>Дата регистрации</th>
                    <th>Вебинар</th>
                </tr>
                ";
        foreach ($results as $row) {
            $content .= '<tr>';
            $content .= '<td>' . $row->surname . ' ' . $row->name . ' ' . $row->patronymic . '</td>';
            $content .= '<td>' . $row->mobile_number . '</td>';
            $content .= '<td>' . $row->email . '</td>';
            $content .= '<td>' . $row->company_name . '</td>';
            $content .= '<td>' . $row->inn . '</td>';
            $content .= '<td>' . $row->date_submitted . '</td>';
            $content .= '<td>' . $row->webinar_title . ' (' . $row->webinar_date . ')</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';
        $content .= 'Список пользователей за весь период во вложении.';

    } else {
        $content = 'За ' . date('d.m.Y', strtotime($date)) . ' не было регистраций. Список пользователей за весь период во вложении.';
    }
    return $content;
}

function send_report_email($to, $body, $date, $fileToAttach)
{
    $subject = 'Отчет о регистрациях на семинары за ' . date('d.m.Y', strtotime($date)) . '';
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Umbrella group &lt;site@taxlab.ru');
    wp_mail($to, $subject, $body, $headers, $fileToAttach);
}