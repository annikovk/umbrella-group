<?php

header('Content-Type: text/html; charset=utf-8');

$arrTo = ['n.demidenko@taxlab.ru', 'server@kannikov.ru', 'contact@taxlab.ru', 'market@taxlab.ru', 'event_crm@taxlab.ru', 'a.klimov@taxlab.ru', 'fsl.taxlab@yandex.ru', 'mmd@taxlab.ru', 'k.vigant@taxlab.ru', 'kov@taxlab.ru', 's.dudik@taxlab.ru', 'o.baturina@taxlab.ru', 'n.hohlova@taxlab.ru'];

// Получение данных о заказчике
$user_name = htmlspecialchars(trim($_POST['user-name']));
$user_phone = htmlspecialchars(trim($_POST['phone']));

// Получение данных о количестве отходов
$amount = htmlspecialchars(trim($_POST['amount']));

// Получение данных о видах деятельности
$collection = htmlspecialchars(trim($_POST['collection']));
$transportation = htmlspecialchars(trim($_POST['transportation']));
$utilization = htmlspecialchars(trim($_POST['utilization']));
$processing = htmlspecialchars(trim($_POST['processing']));
$neutralization = htmlspecialchars(trim($_POST['neutralization']));
$distribution = htmlspecialchars(trim($_POST['distribution']));

// Получение данных о перечне услуг
$conclusion = htmlspecialchars(trim($_POST['conclusion']));
$project = htmlspecialchars(trim($_POST['project']));
$sec = htmlspecialchars(trim($_POST['sec']));
$formation = htmlspecialchars(trim($_POST['formation']));
$maintenance = htmlspecialchars(trim($_POST['maintenance']));
$development = htmlspecialchars(trim($_POST['development']));

$to = 'kirill.rusakov@intelsib-team.com';

function validation($checkbox, $value) 
{
    if($checkbox == "on")
    {
        $message .= $value . ", \n";
    }
}


if(!empty($user_name) AND !empty($user_phone))
{
    //Валидация 

    // Формирование письма
    $subject = 'Сообщение с сайта';

    $message = "Вам пришло сообщение от клиента - $user_name. \n Контактный номер - $user_phone. \n Колическтво отходов - $amount. \n  Виды деятельности и перечень услуг: \n";

    validation($collection, "Сбор");
    validation($transportation, "Транспортирование");
    validation($utilization, "Утилизация");
    validation($processing, "Обработка");
    validation($neutralization, "Обезвреживание");
    validation($distribution, "Размещение");

    validation($conclusion, "Экспертное заключение");
    validation($project, "Проект санитарно-защитной зоны");
    validation($sec, "Получение СЭЗ (Роспотребнадзор)");
    validation($formation, "Формирование пакета документов");
    validation($maintenance, "Сопровождение проверки Росприроднадзора");
    validation($development, "Повышение квалификации для работников");

    $headers = 'From: webmaster@example.com' . "\r\n" .
        'Reply-To: webmaster@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();


    // Отправление
    mail($to, $subject, $message, $headers);
    // foreach($arrTo as $to)
    // {
    //     mail($to, $subject, $message, $headers);
    // }


} 
else 
{
    die("Не все обязательные поля заполнены!");
}

