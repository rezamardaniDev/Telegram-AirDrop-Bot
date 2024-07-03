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

function sendMessage($chat_id, $text, $parse_mode = null, $reply_markup = null){
    TelegramAPI('sendMessage', [
        'chat_id'      => $chat_id,
        'text'         => $text,
        'parse_mode'   => $parse_mode,
        'reply_markup' => $reply_markup
    ]);
}

function deleteMessages($chat_id, $message_id){
    TelegramAPI('deleteMessages', [
        'chat_id'     => $chat_id,
        'message_ids' => $message_id
    ]);
}