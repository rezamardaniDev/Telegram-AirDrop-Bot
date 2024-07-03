<?php

include 'config.php';
include 'functions.php';

$update = json_decode(file_get_contents('php://input'), true);
# ----------------- [ <- variables -> ] ----------------- #
if (isset($update['message'])){
    $message_id = $update['message']['message_id'];
    $first_name = $update['message']['from']['first_name'];
    $chat_id = $update['message']['chat']['id'];
    $from_id = $update['message']['from']['id'];
    $text = $update['message']['text'];
}

# ----------------- [ <- main -> ] ----------------- #
if ($text == '/start'){
    sendMessage($from_id, $message_id);
    sleep(5);
    deleteMessages($chat_id, $message_id);
}