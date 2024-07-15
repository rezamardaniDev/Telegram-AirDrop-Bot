<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

require 'config.php';
require 'functions.php';
require 'keyboards.php';

$update = json_decode(file_get_contents('php://input'), true);
# ----------------- [ <- variables -> ] ----------------- #
if (array_key_exists('message', $update)) {
    $message_id = $update['message']['message_id'];
    $first_name = $update['message']['from']['first_name'];
    $chat_id = $update['message']['chat']['id'];
    $from_id = $update['message']['from']['id'];
    $text = $update['message']['text'];
    $chat_type = $update['message']['chat']['type'];
    $user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `users` WHERE `chat_id` = ($from_id)"));
}
if (array_key_exists('callback_query', $update)) {
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $from_id = $update['callback_query']['from']['id'];
    $chat_type = $update['callback_query']['message']['chat']['type'];
}

# ----------------- [ <- user panel -> ] ----------------- #
if ($chat_type != 'private') {
    die();
}
if (preg_match('/^\/start/', $text) || $text == 'بازگشت به منو اصلی') {

    setStep($from_id, null);
    preg_match('/^(\/start) (.*)/', $text, $match);
    $invite_id = $match[2];

    if ($invite_id && $invite_id != $from_id && !$user) {
        mysqli_query($db, "INSERT INTO `invitations` (`caller`, `invited`) VALUES ($invite_id, $from_id)");
        mysqli_query($db, "UPDATE `users` SET `balance` = `balance` + 0.5 WHERE `chat_id` = ($invite_id) ");
        sendMessage($invite_id, "تبریک یک کاربر جدید با لینک دعوت شما به ربات پیوست!");
    }
    if (!$user) {
        mysqli_query($db, "INSERT INTO `users` (`chat_id`) VALUES ($from_id)");
    }
    $txt = mysqli_query($db, "SELECT `config_value` FROM `config` WHERE `config_key` = 'start' ")->fetch_array()['config_value'] ?? 'ثبت نشده';
    sendMessage($from_id, $txt, $userKeyboard);
    die();
}

if ($text == '「 🌟 شروع کسب درآمد 」' || $text == '/link') {
    $txt =
        "🖇 لینک اختصاصی شما برای دعوت از دوستان:
https://t.me/ReporterDevBot?start=$from_id
✅ برای دعوت از دوستان خود کافیست لینک بالا را با آن‌ها به اشتراک بگذارید.
    ";
    sendMessage($from_id, $txt);
    die();
}

if ($text == '「 👥 برترین کاربران 」') {
    $topUsers = mysqli_query($db, "SELECT * FROM `users` ORDER BY `balance` DESC LIMIT 10");
    $txt = "👤 10 نفرات برتر ربات\n\n";
    $rank = 1;
    while ($res = $topUsers->fetch_assoc()) {
        $user = $res['chat_id'];
        $balance = $res['balance'];
        $txt .= "$rank) $user ----> $balance TRX\n\n";
        $rank++;
    }
    sendMessage($from_id, $txt);
    die();
}

if ($text == '「 🔰 پروفایل کاربری 」' || $text == 'بازگشت به پروفایل' || $text == '/profile') {
    setStep($from_id, 'profile');

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
    $balance = $user['balance'];
    mysqli_query($db, "UPDATE `users` SET `wallet` = '$text' WHERE `chat_id` = ($from_id)");
    sendMessage($from_id, "آدرس کیف پول شما با موفقیت تغییر کرد!\n\nموجودی شما: $balance TRX\nآدرس ولت:\n`$text`\nشناسه کاربری: `$from_id`", $userProfile);
    setStep($from_id, 'profile');
    die();
}

if ($text == 'برداشت موجودی') {
    if (!$user['wallet']) {
        sendMessage($from_id, "ابتدا باید آدرس کیف پول خود را ثبت کنید!");
        die();
    }
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

if ($text == '「 🛑 قوانین 」') {
    $txt = mysqli_query($db, "SELECT `config_value` FROM `config` WHERE `config_key` = 'rule' ")->fetch_array()['config_value'] ?? 'ثبت نشده';
    sendMessage($from_id, $txt, $backToMenu);
    die();
}

if ($text == '「 ☎️ پشتیبانی 」') {
    $txt = mysqli_query($db, "SELECT `config_value` FROM `config` WHERE `config_key` = 'support' ")->fetch_array()['config_value'] ?? 'ثبت نشده';
    sendMessage($from_id, $txt, $backToMenu);
    die();
}

if ($text == '「 ⏰ پاداش روزانه 」') {
    $currentTime = time();
    $stampToDb = date('Y-m-d H:i:s', $currentTime);
    if (($currentTime - strtotime($user['daily'])) > 86400) {
        sendMessage($from_id, "تبریک برای امروز شما 0.5 TRX دریافت کردید!");
        mysqli_query($db, "UPDATE `users` SET `daily` = '$stampToDb', `balance` = `balance` + 0.5 WHERE `chat_id` = ($from_id)");
    } else {
        sendMessage($from_id, "شما هدیه امروز را دریافت کرده اید! \nفردا منتظر شما هستیم");
    }
    die();
}

# ----------------- [ <- admin panel -> ] ----------------- #
if ($text == 'پنل' && in_array($from_id, $bot_admins)) {
    setStep($from_id, 'admin-panel');
    sendMessage($from_id, "به پنل مدیریت ربات خوش آمدید!", $admin_panel);
    die();
}

if ($text == 'آمار ربات') {
    $members = mysqli_query($db, "SELECT COUNT(*) AS total FROM `users`")->fetch_assoc()['total'];
    $txt = "تعداد اعضای ربات تا این لحظه: $members نفر";
    sendMessage($from_id, $txt);
    die();
}
