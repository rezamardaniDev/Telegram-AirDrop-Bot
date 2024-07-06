<?php

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
    return $data;
}

function sendMessage($chat_id, $text, $reply_markup = null)
{
    TelegramAPI('sendMessage', [
        'chat_id'      => $chat_id,
        'text'         => $text,
        'parse_mode'   => 'Markdown',
        'reply_markup' => $reply_markup
    ]);
}

function deleteMessages($chat_id, $message_id)
{
    TelegramAPI('deleteMessage', [
        'chat_id'     => $chat_id,
        'message_id'  => $message_id
    ]);
}

function convertToEnglishNumbers(string $text)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    $PeToEn = str_replace($persian, $english, $text);
    $ArToEn = str_replace($arabic, $english, $PeToEn);

    return $ArToEn;
}

function debug($data)
{
    $result = print_r($data, true);
    sendMessage(5910225814, $result);
}
