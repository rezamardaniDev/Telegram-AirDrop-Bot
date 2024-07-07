<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

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
if (isset($update['callback_query'])) {
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $from_id = $update['callback_query']['from']['id'];
}

# ----------------- [ <- main -> ] ----------------- #
if (preg_match('/^\/start/', $text) || $text == 'بازگشت به منو اصلی') {

    setStep($from_id, null);
    $invite_id = explode(' ', $text)[1];

    if ($invite_id && $invite_id != $from_id) {
        $checkInvite = mysqli_query($db, "SELECT * FROM `invitations` WHERE `invited` = ($from_id)");
        if ($checkInvite->num_rows == 0) {
            mysqli_query($db, "INSERT INTO `invitations` (`caller`, `invited`) VALUES ($invite_id, $from_id)");
            $userBalance = mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($invite_id)")->fetch_assoc()['balance'];
            $newBalance = $userBalance + 0.5;
            mysqli_query($db, "UPDATE `users` SET `balance` = $newBalance WHERE `chat_id` = ($invite_id) ");
            sendMessage($invite_id, "تبریک یک کاربر جدید با لینک دعوت شما به ربات پیوست!\nموجودی جدید شما: $newBalance TRX");
        }
    } else {
        mysqli_query($db, "INSERT INTO `invitations` (`caller`, `invited`) VALUES (0, $from_id)");
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

if ($text == 'پروفایل کاربری' || $text == 'بازگشت به پروفایل' || $text == '/profile') {
    setStep($from_id, 'profile');
    $user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)"));
    $balance = $user['balance'];
    $wallet = $user['wallet'] ?? 'ثبت نشده';

    sendMessage($from_id, "به بخش پروفایل خوش آمدید \n\nموجودی شما: $balance TRX\nآدرس ولت:\n`$wallet`\nشناسه کاربری: `$from_id`", $userProfile);
    die();
}

if ($text == 'تغییر کیف پول') {
    setStep($from_id, 'set-wallet-address');
    sendMessage($from_id, "آدرس کیف پول خود را وارد کنید: ", $backToProfile);
    die();
}

if ($text && getStep($from_id) == 'set-wallet-address') {
    $user = mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)");
    $balance = $user->fetch_assoc()['balance'];
    mysqli_query($db, "UPDATE `users` SET `wallet` = '$text' WHERE `chat_id` = ($from_id)");
    sendMessage($from_id, "آدرس کیف پول شما با موفقیت تغییر کرد!\n\nموجودی شما: $balance TRX\nآدرس ولت:\n`$text`\nشناسه کاربری: `$from_id`", $userProfile);
    setStep($from_id, null);
    die();
}

if ($text == 'برداشت موجودی') {
    $userBalance = mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)")->fetch_assoc()['balance'];
    if ($userBalance >= 5) {
        setStep($from_id, 'withdraw');
        $user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)"));
        $balance = $user['balance'];
        $wallet = $user['wallet'];
        sendMessage($from_id, "تایید تراکنش!\n\nبرداشت: $balance TRX\nبه آدرس:\n$wallet\n\nدر صورتی که اطلاعات بالا مورد تایید است لطفا روی دکمه زیر کلیک کنید", $withdraw);
    } else {
        sendMessage($from_id, "موجودی شما برای برداشت کافی نیست! حداقل مقدار قابل برداشت 5 ترون میباشد.", $backToProfile);
    }
    die();
}

if ($data == 'withdraw') {
    mysqli_query($db, "UPDATE `users` SET `balance` = 0 WHERE `chat_id` = ($from_id) ");
    editMessage($chat_id, "درخواست برداشت شما با موفقیت ثبت شد!", $message_id);
    setStep($from_id, null);
    die();
}
