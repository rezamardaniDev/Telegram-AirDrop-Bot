<?php

// error_reporting(0);

require 'config.php';
require 'functions.php';
require 'keyboards.php';

$update = json_decode(file_get_contents('php://input'), true);
# ----------------- [ <- variables -> ] ----------------- #
if (isset($update['message'])) {
    $message_id = $update['message']['message_id'];
    $first_name = $update['message']['from']['first_name'];
    $chat_id = $update['message']['chat']['id'];
    $from_id = $update['message']['from']['id'];
    $text = $update['message']['text'];
}

# ----------------- [ <- main -> ] ----------------- #
if (preg_match('/^\/start/', $text)) {

    $invite_id = explode(' ', $text)[1];
    if ($invite_id) {
        if ($invite_id != $from_id) {
            $userBalance = mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($invite_id)")->fetch_assoc()['balance'];
            $newBalance = $userBalance + 0.5;
            mysqli_query($db, "UPDATE `users` SET `balance` = $newBalance WHERE `chat_id` = ($invite_id) ");
            sendMessage($invite_id, "تبریک یک کاربر جدید با لینک دعوت شما به ربات پیوست!\nموجودی جدید شما: $newBalance TRX");
        }
    }

    $checkUser = mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)");
    if ($checkUser->num_rows == 0) {
        mysqli_query($db, "INSERT INTO `users` (`chat_id`) VALUES ($from_id)");
    }
    sendMessage($from_id, 'سلام کاربر عزیز به ربات کسب درآمد خوش آمدید!', $userKeyboard);
    die();
}

if ($text == 'شروع کسب درآمد') {
    $msg =
        "🖇 لینک اختصاصی شما برای دعوت از دوستان:
https://t.me/ReporterDevBot?start=$from_id
✅ برای دعوت از دوستان خود کافیست لینک بالا را با آن‌ها به اشتراک بگذارید.
    ";
    sendMessage($from_id, $msg);
    die();
}

if ($text == 'برترین کاربران') {
    $topUsers = mysqli_query($db, "SELECT * FROM `users` ORDER BY `balance` DESC LIMIT 10");
    $msg = "👤 10 نفرات برتر ربات\n\n";
    $rank = 1;
    while ($res = $topUsers->fetch_assoc()) {
        $user = $res['chat_id'];
        $balance = $res['balance'];
        $msg .= "$rank) $user ----> $balance TRX\n\n";
        $rank++;
    }
    sendMessage($from_id, $msg);
    die();
}
