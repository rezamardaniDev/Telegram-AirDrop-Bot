<?php

if (array_key_exists('message', $update)) {
    $message_id = $update['message']['message_id'];
    $first_name = $update['message']['from']['first_name'];
    $chat_id = $update['message']['chat']['id'];
    $from_id = $update['message']['from']['id'];
    $text = $update['message']['text'];
    $chat_type = $update['message']['chat']['type'];
    $stmt = $db->query("SELECT * FROM `users` WHERE `chat_id` = ($from_id)");
    $user = $stmt->fetch();
}
if (array_key_exists('callback_query', $update)) {
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $from_id = $update['callback_query']['from']['id'];
    $chat_type = $update['callback_query']['message']['chat']['type'];
    $stmt = $db->query("SELECT * FROM `users` WHERE `chat_id` = ($from_id)");
    $user = $stmt->fetch();
}
