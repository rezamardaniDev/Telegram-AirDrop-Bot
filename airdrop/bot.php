<?php

require 'config.php';
require 'functions.php';


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
if ($update){
    sendMessage($from_id, convertToEnglishNumbers($text));
}