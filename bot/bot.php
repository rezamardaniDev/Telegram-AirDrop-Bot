<?php

include 'functions.php';
include 'keyborads.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

const APIKEY = '7430401771:AAEYcZy9aUpdsSM91SEJ9RBXpYNdR7bLXvw';

$update = json_decode(file_get_contents('php://input'), true);
# ----------------- [ <- variables -> ] ----------------- #
if (array_key_exists('message', $update)) {
    $message_id = $update['message']['message_id'];
    $first_name = $update['message']['from']['first_name'];
    $chat_id = $update['message']['chat']['id'];
    $from_id = $update['message']['from']['id'];
    $text = $update['message']['text'];
    $chat_type = $update['message']['chat']['type'];
}
if (array_key_exists('callback_query', $update)) {
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $from_id = $update['callback_query']['from']['id'];
    $chat_type = $update['callback_query']['message']['chat']['type'];
}

function TelegramAPI(string $method, array $params)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.telegram.org/bot' . APIKEY . '/' . $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 'Content-Type: application/json',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data);
}

if ($text == '/start') {
    sendMessage($from_id, "به ربات تست سرعت هاست آلمان آران سرور خوش آمدید", $userKeyboard1);
    die();
}

if ($text == "ارسال پیام") {
    $start_time = microtime(true);
    for ($i = 1; $i <= 10; $i++) {
        sendMessage($from_id, "🔰 - پیام شماره $i");
    }
    $end_time = microtime(true);
    $execution_time =  intval($end_time - $start_time);


    $text = "
⭕️ تست ارسال پیام ها با موفقیت به پایان رسید.
-
⏳ مدت زمان طی شده برای ارسال 10 پیام : $execution_time ثانیه
-
🛍 تهیه‌ی هاست پرسرعت آلمان
🌐 AranServer.ir
    ";
    sendMessage($from_id, $text);
    die();
}

if ($text == "ویرایش پیام") {
    $start_time = microtime(true);
    sendMessage($chat_id, '1');
    $rand = 2;
    while ($rand <= 10) {
        editMessage($chat_id, "▫️ - $rand", $message_id + 1);
        $rand++;
    }
    $end_time = microtime(true);
    $execution_time =  intval($end_time - $start_time);

    $text = "
⭕️ تست ارسال پیام ها با موفقیت به پایان رسید.
-
⏳ مدت زمان طی شده برای ویرایش 10 پیام : $execution_time ثانیه
-
🛍 تهیه‌ی هاست پرسرعت آلمان
🌐 AranServer.ir
    ";
    editMessage($chat_id, $text, $message_id + 1);
    die();
}

if ($text == "خرید هاست پرسرعت") {
    $text = "
برای خرید هاست پرسرعت آلمان میتوانید به سایت آران سرور مراجعه کنید
🌐 AranServer.ir
    ";
}
