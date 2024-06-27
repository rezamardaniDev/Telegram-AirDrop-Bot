<?php

error_reporting(0);

define('API_KEY', '6331777475:AAEcCY_1UCjvgcvUzDpCOYtL7HG9BdkZVFI');

function bot(string $method, array $params)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.telegram.org/bot' . API_KEY . '/' . $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function sendMessage($chat_id, $text, $keyboard = null, $mrk = 'html')
{
    $params = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $mrk,
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard
    ];
    return bot('sendMessage', $params);
}

# ----------------- [ <- variables -> ] ----------------- #

$update = json_decode(file_get_contents('php://input'));

if (isset($update->message)) {
    $message_id = $update->message->message_id;
    $first_name = $update->message->from->first_name;
    $username = $update->message->from->username;
    $from_id = $update->message->from->id;
    $chat_id = $update->message->chat->id;
    $reply_to_message_id = $update->message->reply_to_message->from->id;
    $text = $update->message->text;
    $type = $update->message->chat->type;
}
if (isset($update->callback_query)) {
    $from_id = $update->callback_query->from->id;
    $chat_id = $update->callback_query->message->chat->id;
    $data = $update->callback_query->data;
    $query_id = $update->callback_query->id;
    $type = $update->callback_query->message->chat->type;
    $message_id = $update->callback_query->message->message_id;
    $username = $update->callback_query->from->username;
}
# ----------------- [ <- main -> ] ----------------- #
if ($text == '/start'){
    sendMessage($from_id, 'Salam');
}