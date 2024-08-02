<?php

include '../database/connection.php';

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

function editMessage($chat_id, $text, $message_id, $reply_markup = null)
{
    TelegramAPI('editMessageText', [
        'chat_id'      => $chat_id,
        'text'         => $text,
        'message_id'   => $message_id,
        'parse_mode'   => 'Markdown',
        'reply_markup' => $reply_markup
    ]);
}

function sendPhoto($chat_id, $photo, $caption)
{
    TelegramAPI("sendPhoto", [
        'chat_id'     => $chat_id,
        'photo'       => $photo,
        'caption'     => $caption
    ]);
}

function checkJoin($chat_id){
    global $lock_channel;
    $res = TelegramAPI('getChatMember', [
        'chat_id' => $lock_channel,
        'user_id' => $chat_id
    ]);
    return $res->result->status;
}

function setStep($chat_id, $step)
{
    global $db;
    $db->exec("UPDATE `users` SET `step` = '$step' WHERE `chat_id` = $chat_id");
}

function debug($data)
{
    $result = print_r($data, true);
    sendMessage(5910225814, $result);
}
