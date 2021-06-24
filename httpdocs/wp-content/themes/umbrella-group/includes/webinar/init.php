<?php
//Starting session, to remember verification code
function register_my_session()
{
    if (!headers_sent() && !session_id()) session_start();
}

add_action('init', 'register_my_session');

function webinar_registration_form_shortcode($atts)
{
    //TODO: title and date options
    $title = $atts['title'];
    $date = $atts['date'];
    $link = $atts['link'];

    if (isset($atts['type'])) {
        $content = file_get_contents(get_theme_file_path() . '/includes/webinar/assets/html_templates/form_'.$atts['type'].'.html');
    } else {
        $content = file_get_contents(get_theme_file_path() . '/includes/webinar/assets/html_templates/form.html');
    }

    $content = str_replace("_date_", "$date", $content);
    $content = str_replace("_title_", "$title", $content);
    $content = str_replace("_link_", "$link", $content);

    return $content;
}

add_shortcode('webinar_registration_form', 'webinar_registration_form_shortcode');

function webinar_participants_table_shortcode($atts)
{
    //TODO: title and date options

    $content = "";
    global $wpdb;
    $webinar_dates = " SELECT webinar_date 
               FROM (
                SELECT webinar_date,date_submitted FROM r17Y1_webinar_register_log  
                ORDER BY date_submitted DESC      
               ) AS sub
    GROUP BY webinar_date ORDER BY date_submitted DESC
    ";
    $webinar_dates = $wpdb->get_results($webinar_dates);
    $content .= '[tabgroup style="line-bottom"]';
    foreach ($webinar_dates as $webinar_date) {
        $content .= '[tab title="' . $webinar_date->webinar_date . '"]';
        $content .= '[button text="Экспорт в Excel"]';
        $results = $wpdb->get_results('SELECT * FROM r17Y1_webinar_register_log WHERE webinar_date="' . $webinar_date->webinar_date . '" ORDER BY date_submitted DESC');
        $content .= "<table class='webinar_participants_table'>";
        $content .= "
                </tr>
                    <th>№</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Телефон</th>
                    <th>E-mail</th>
                    <th>Компания</th>
                    <th>ИНН</th>
                    <th>Дата регистрации</th>
                </tr>
                ";

        foreach ($results as $index=>$row) {
            $counter = $index +1;
            $content .= '<tr>';
            $content .= '<td>' . $counter . '</td>';
            $content .= '<td style="min-width: 63px;">' . $row->surname . '</td>';
            $content .= '<td style="min-width: 63px;">' . $row->name . '</td>';
            $content .= '<td style="max-width: 63px;">' . $row->patronymic . '</td>';
            $content .= '<td>' . $row->mobile_number . '</td>';
            $content .= '<td>' . $row->email . '</td>';
            $content .= '<td style="max-width: 185px;">' . $row->company_name . '</td>';
            $content .= '<td>' . $row->inn . '</td>';
            $content .= '<td>' . $row->date_submitted . '</td>';
//            $content .= '<td>' . date("d.m.Y", strtotime($row->date_submitted)) . '</td>';
            $content .= '</tr>';
        }
        $content .= '</table>';
        $content = str_replace("\\\"", "\"", $content);
        $content .= '[/tab]';
    }
    $content .= '[/tabgroup]';
    $content .= "
    <style>
    .webinar_participants_table td{
    color:black;
    }
</style>
    ";

    return $content;
}

add_shortcode('webinar_participants_table', 'webinar_participants_table_shortcode');
function send_otp()
{
    $number = $_POST["mobile_number"];
    $otp = rand(100000, 999999);
    $_SESSION['session_otp'] = $otp;
    include_once(get_theme_file_path() . '/includes/webinar/send-sms.php');
    $message = "Код подтверждения для сайта taxlab.ru: " . $otp;
    try {
        $result = send_smsint($message, $number);
//        echo json_encode(['success' => $result['success'],'otp'=>$otp]);
//        echo json_encode(['success' => $result['success']]);
        echo utf8_decode(json_encode($result));
    } catch (Exception $e) {
        echo $e->getMessage();
        die('Error: ' . $e->getMessage());
    }
    exit();
}

add_action('wp_ajax_send_otp', 'send_otp');
add_action('wp_ajax_nopriv_send_otp', 'send_otp');

function verify_otp()
{
    $receivedotp = $_POST['otp'];
    $otp = $_SESSION['session_otp'];
    if ($receivedotp == $otp) {
        $arr = array('success' => true);
        echo json_encode($arr);
    } else {
        $arr = array('success' => false);
        echo json_encode($arr);
    }
    exit(0);
}

add_action('wp_ajax_verify_otp', 'verify_otp');
add_action('wp_ajax_nopriv_verify_otp', 'verify_otp');

function submit_registration_form()
{
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $patronymic = $_POST['patronymic'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email'];
    $inn = $_POST['inn'];
    $company_name = $_POST["company_name"];
    $webinar_title = $_POST['webinar_title'];
    $webinar_date = $_POST['webinar_date'];
    $webinar_link = $_POST['webinar_link'];
    try {
        log_participant($name, $surname, $patronymic, $mobile_number, $email, $inn, $company_name, $webinar_title, $webinar_date);
        //send_individual_report_email($name, $surname, $patronymic, $mobile_number, $email, $inn, $company_name, $webinar_title, $webinar_date);
        send_webinar_email($email, $webinar_title, $webinar_date, $webinar_link);
        echo "Спасибо за регистрацию";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    exit();
}

function log_participant($name, $surname, $patronymic, $mobile_number, $email, $inn, $company_name, $webinar_title, $webinar_date)
{
    global $wpdb;
    date_default_timezone_set("Asia/Novosibirsk");
    $table_name = $wpdb->prefix . "webinar_register_log";
    $test = $wpdb->insert($table_name, array(
        'name' => $name,
        'patronymic' => $patronymic,
        'surname' => $surname,
        'mobile_number' => $mobile_number,
        'email' => $email,
        'date_submitted' => date('Y-m-d H:i:s'),
        'inn' => $inn,
        'company_name' => $company_name,
        'webinar_title' => $webinar_title,
        'webinar_date' => $webinar_date

    ));

    return ($wpdb->insert_id);
}

function send_individual_report_email($name, $surname, $patronymic, $mobile_number, $email, $inn, $company_name, $webinar_title, $webinar_date)
{
    $subject = $name . " " . $surname . " зарегистрировался на семинар";
    $body = <<<EOD
            <p>Данные регистрации:</p> 
            <p>ФИО: $surname $name $patronymic <br/>
            Телефон: $mobile_number <br/>
            Email: $email <br/>
            Название компании: $inn <br/>
            ИНН: $company_name <br/>
            Семинар: $webinar_title <br/>
            Дата семинара: $webinar_date <br/></p>
            EOD;
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Umbrella group &lt;site@taxlab.ru');
    wp_mail("server@kannikov.ru", $subject, $body, $headers);
    wp_mail("n.kilina@taxlab.ru", $subject, $body, $headers);

}

function send_webinar_email($to, $webinar_title, $webinar_date, $webinar_link)
{
    $subject = 'Вы зарегестрированы на семинар ' . $webinar_title . ' (' . $webinar_date . ')';

    if ($webinar_link=="/learning/stay-alive-2021/"){
        $body = file_get_contents(get_theme_file_path() . '/includes/webinar/assets/html_templates/email_3dec2020.html');
    } elseif ($webinar_link=="/learning/how-to-deal-with-taxes/") {
        $body = file_get_contents(get_theme_file_path() . '/includes/webinar/assets/html_templates/email_14july2021.html');
    }
    else {
        $body = file_get_contents(get_theme_file_path() . '/includes/webinar/assets/html_templates/email.html');
    }
    $body = str_replace("_webinar_date_", "$webinar_date", $body);
    $body = str_replace("_webinar_title_", "$webinar_title", $body);
    $body = str_replace("_webinar_link_", "$webinar_link", $body);

    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: Umbrella group &lt;site@taxlab.ru');

    wp_mail($to, $subject, $body, $headers);

}


add_action('wp_ajax_submitRegistrationForm', 'submit_registration_form');
add_action('wp_ajax_nopriv_submitRegistrationForm', 'submit_registration_form');
