<?php
function send_smsint($message, $number)
{
    $array_message = array(array("recipient" => $number,"text" => $message));
    $sendoptions = json_encode(["messages" => $array_message, "channel" => 1]);
    //return $sendoptions;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://lcab.smsint.ru/json/v1.0/sms/send/text",
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "X-Token: 5c5wt2fp9ybbrsxlrdv5g8eeukwwy7ob2fwniaqa589v5olfwc2vlqkqxkzylxw9",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS =>$sendoptions,
            CURLOPT_RETURNTRANSFER => true
    ]);

    $result = curl_exec($ch);
    return json_decode($result, true);
}

?>



