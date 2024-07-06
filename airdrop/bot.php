<?php

// error_reporting(0);

require 'config.php';
require 'functions.php';
require 'keyboards.php';

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
    $userExists = mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)");
    if ($userExists->num_rows == 0){
        mysqli_query($db, "INSERT INTO `users` (`chat_id`) VALUES ($from_id)");
    }
    sendMessage($from_id, 'سلام کاربر عزیز به ربات کسب درآمد خوش آمدید!', $userKeyboard);
    die();
}
