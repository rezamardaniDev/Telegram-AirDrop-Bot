<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

require 'config/config.php';
require 'utils/functions.php';
require 'utils/keyboards.php';
require 'database/connection.php';

$update = json_decode(file_get_contents('php://input'), true);
# ----------------- [ <- variables -> ] ----------------- #
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
# ----------------- [ <- user panel -> ] ----------------- #
if (checkJoin($from_id) == 'left') {
    $txt = "
کاربر گرامی جهت استفاده از ربات ابتدا در کانال اطلاع رسانی عضو شوید!

$lock_channel
    ";
    sendMessage($from_id, $txt);
    die();
};

if ($user && $user['status'] == 0) {
    sendMessage($from_id, "کاربر گرامی متاسفانه شما از ربات بلاک شده اید!");
    die();
}

if (preg_match('/^\/start/', $text) || $text == 'بازگشت به منو اصلی') {
    $referal_Id = explode(" ", $text)[1];

    $stmt = $db->prepare("SELECT * FROM `users` WHERE `chat_id` = ?");
    $stmt->execute([$referal_Id]);
    $validate_Referal = $stmt->fetch();

    if ($validate_Referal && $from_id != $referal_Id && !$user) {
        sendMessage($referal_Id, "یک نفر با لینک شما به ربات پیوست!");
        $db->exec("INSERT INTO `invitations` (`caller`, `invited`) VALUES ($referal_Id, $from_id)");
        $stmt = $db->query("SELECT `config_value` FROM `config` WHERE `config_key` = 'gift' ");
        $gift = $stmt->fetch()['config_value'];
        $db->exec("UPDATE `users` SET `balance` = `balance` + $gift WHERE `chat_id` = ($referal_Id)");
    }

    if (!$user) {
        $db->exec("INSERT INTO `users` (`chat_id`) VALUES ($from_id)");
    }

    $stmt = $db->query("SELECT `config_value` FROM `config` WHERE `config_key` = 'start' ");
    $start = $stmt->fetch()['config_value'];

    if (in_array($from_id, $bot_admins)) {
        sendMessage($from_id, $start, $userKeyboard2);
    } else {
        sendMessage($from_id, $start, $userKeyboard1);
    }
    setStep($from_id, 'Home');
    die();
}


if ($text == '「 🌟 شروع کسب درآمد 」' || $text == '/link') {
    $invite_Banner_Text = "🖇 لینک اختصاصی شما برای دعوت از دوستان:\nhttps://t.me/ReporterDevBot?start=$from_id\n\n✅ برای دعوت از دوستان خود کافیست لینک بالا را با آن‌ها به اشتراک بگذارید.";
    sendPhoto($from_id, "https://fara-it.ir/airdrop/banner.jpg", $invite_Banner_Text);
    die();
}

if ($text == '「 👥 برترین کاربران 」') {
    $top_Users = $db->query("SELECT * FROM `users` ORDER BY `referal` DESC LIMIT 10");
    $top_Users_Text = "*💯 10 کاربر برتر ربات به ترتیب بیشترین زیرمجموعه*\n\n";
    $rank = 1;
    foreach ($top_Users as $tpUser) {
        $top_Users_Text .= "🔰 $rank | `{$tpUser['chat_id']}`\n💰 *{$tpUser['balance']} TRX* | 👤 {$tpUser['referal']} referal\n\n";
        $rank++;
    }
    sendMessage($from_id, $top_Users_Text);
    die();
}

if ($text == '「 🔰 پروفایل کاربری 」' || $text == 'بازگشت به پروفایل' || $text == '/profile') {
    setStep($from_id, 'profile');
    $user_Balance = $user['balance'];
    $user_Wallet = $user['wallet'] ?? 'ثبت نشده';
    $user_Referal = $user['referal'];
    $user_Info_Text = "🔺 پروفایل شما\n\n💳 آدرس کیف پول:\n`$user_Wallet`\n\n💰موجودی: $user_Balance TRX\n👀 شناسه کاربری: `$from_id`\n📊 تعداد زیرمجموعه ها: $user_Referal";

    if ($user_Wallet == "ثبت نشده") {
        sendMessage($from_id, $user_Info_Text, $userProfile1);
        die();
    } else {
        sendMessage($from_id, $user_Info_Text, $userProfile2);
        die();
    }
}

if (($text == 'تغییر کیف پول' || $text == 'ثبت کیف پول') && $user['step'] == 'profile') {
    setStep($from_id, 'set-wallet-address');
    sendMessage($from_id, "آدرس کیف پول خود را وارد کنید: ", $backToProfile);
    die();
}

if ($text && $user['step'] == 'set-wallet-address') {

    setStep($from_id, 'profile');

    $stmt = $db->prepare("UPDATE `users` SET `wallet` = (?) WHERE `chat_id` = ($from_id)");
    $stmt->execute([$text]);

    $user_Balance = $user['balance'];
    $user_Wallet = $text;
    $user_Referal = $user['referal'];

    $user_Info_Text = "آدرس کیف پول شما با موفقیت تغییر کرد!\n\n💳 آدرس کیف پول:\n`$user_Wallet`\n\n💰موجودی: $user_Balance TRX\n👀 شناسه کاربری: `$from_id`\n📊 تعداد زیرمجموعه ها: $user_Referal";
    sendMessage($from_id, $user_Info_Text, $userProfile2);
    die();
}

if ($text == 'برداشت موجودی') {

    if (!$user['wallet']) {
        sendMessage($from_id, "ابتدا باید آدرس کیف پول خود را ثبت کنید!");
        die();
    }

    if ($user['balance'] >= 5) {
        setStep($from_id, 'withdraw');
        $user_Balance = $user['balance'];
        $user_Wallet = $user['wallet'];
        $withdraw_Text = "♻️ اطلاعات تراکنش!\n\n💎 برداشت : $user_Balance TRX\n💳 به آدرس :\n`$user_Wallet`\n\n❗️در صورتی که اطلاعات بالا مورد تایید است لطفا روی دکمه زیر کلیک کنید";
        sendMessage($from_id, $withdraw_Text, $withdraw);
    } else {
        sendMessage($from_id, "موجودی شما برای برداشت کافی نیست! حداقل مقدار قابل برداشت 5 ترون میباشد.", $backToProfile);
    }
    die();
}

if ($data == 'withdraw' && $user['step'] == 'withdraw') {

    $check_Withdraw_Request = $db->query("SELECT * FROM `withdraw_request` WHERE `chat_id` = ($from_id) AND `status` = 'registered' ");
    if ($check_Withdraw_Request->rowCount() == 0) {

        $user_Balance = $user['balance'];
        $user_Wallet = $user['wallet'];
        $withdraw_Time = date("Y/m/d H:i:s");

        $db->exec("INSERT INTO `withdraw_request` (`chat_id`, `wallet`, `amount`) VALUES ($from_id, '$user_Wallet', $user_Balance)");
        $db->exec("UPDATE `users` SET `balance` = 0 WHERE `chat_id` = ($from_id) ");
        $stmt = $db->query("SELECT * FROM `withdraw_request` WHERE `chat_id` = ($from_id) AND `status` = 'registered' ");
        $seccess_Receipt = $stmt->fetch();
        $admin_Receipt_Text = "🟡 درخواست برداشت جدید\n\n▫️ شناسه کاربر : $from_id\n▫️ مقدار برداشت : {$seccess_Receipt['amount']} TRX\n▫️ آدرس کیف پول :\n\n`{$seccess_Receipt['wallet']}`\n\nتاریخ درخواست :\n$withdraw_Time";
        sendMessage($channel, $admin_Receipt_Text, json_encode([
            'inline_keyboard' => [
                [['text' => 'تایید واریز', 'callback_data' => $from_id]]
            ]
        ]));

        $Receipt_Text = "✅ درخواست برداشت شما در صف انتظار قرار گرفت!\n\n🔰 مقدار برداشت : {$seccess_Receipt['amount']} TRX\n💳 آدرس کیف پول :\n`{$seccess_Receipt['wallet']}`\n\n⏰ زمان ثبت درخواست :\n$withdraw_Time";
        editMessage($chat_id, $Receipt_Text, $message_id);
        setStep($from_id, 'profile');
    } else {
        editMessage($chat_id, "شما از قبل یک درخواست پردازش نشده دارید!\nبرای درخواست جدید باید تا تایید درخواست قبلی خود صبر کنید.", $message_id);
    }
    die();
}

if ($data) {

    $stmt = $db->query("SELECT * FROM `withdraw_request` WHERE `chat_id` = $data AND `status` = 'registered' ");
    $receipt = $stmt->fetch();

    $withdraw_Time = date("Y/m/d H:i:s");
    $Receipt_Text = "🟢 واریز انجام شد\n\n▫️ شناسه کاربر : {$receipt['chat_id']}\n▫️ مقدار برداشت : {$receipt['amount']} TRX\n▫️ آدرس کیف پول :\n\n`{$receipt['wallet']}`\n\nتاریخ درخواست :\n{$receipt['created_at']}\nتاریخ واریز :\n$withdraw_Time";
    editMessage($channel, $Receipt_Text, $message_id, json_encode([
        'inline_keyboard' => [
            [['text' => 'واریز شد', 'callback_data' => 'done']]
        ]
    ]));
    $db->exec("UPDATE `withdraw_request` SET `status` = 'done' WHERE `chat_id` = $data ");
    sendMessage($data, "کاربر گرامی!\n\nدرخواست برداشت شما به مقدار {$receipt['amount']} TRX توسط ادمین تایید و به حساب شما واریز گردید.");
    die();
}

if ($text == '「 🛑 قوانین 」') {
    $stmt = $db->query("SELECT `config_value` FROM `config` WHERE `config_key` = 'rule' ");
    $rule_Text = $stmt->fetch()['config_value'];
    sendMessage($from_id, $rule_Text, $backToMenu);
    die();
}

if ($text == '「 ☎️ پشتیبانی 」') {
    $stmt = $db->query("SELECT `config_value` FROM `config` WHERE `config_key` = 'support' ");
    $support_Text = $stmt->fetch()['config_value'];
    sendMessage($from_id, $support_Text, $backToMenu);
    die();
}

// # ----------------- [ <- admin panel -> ] ----------------- #
if (($text == 'پنل مدیریت' || $text == 'بازگشت به مدیریت') && in_array($from_id, $bot_admins)) {
    setStep($from_id, 'admin-panel');
    sendMessage($from_id, "به پنل مدیریت ربات خوش آمدید!", $admin_panel);
    die();
}

if ($text == 'آمار ربات' && in_array($from_id, $bot_admins)) {
    $stmt = $db->query("SELECT COUNT(*) AS total FROM `users`");
    $members = $stmt->fetch()['total'];
    $txt = "تعداد اعضای ربات تا این لحظه: $members نفر";
    sendMessage($from_id, $txt);
    die();
}

if ($text == 'پیام همگانی' && in_array($from_id, $bot_admins)) {
    setStep($from_id, "broadcast");
    sendMessage($from_id, "لطفا متن پیام را وارد کنید:", $back_To_Admin);
    die();
}

if ($user['step'] == 'broadcast' && in_array($from_id, $bot_admins)) {
    $db->exec("INSERT INTO `messages` (`text`, `sender`) VALUES ('$text', $from_id) ");
    sendMessage($from_id, "پیام شما در صف ارسال قرار گرفت!", $admin_panel);
    setStep($from_id, 'admin-panel');
    die();
}


if ($text == "تنظیمات" && in_array($from_id, $bot_admins)) {
    setStep($from_id, "settings");
    sendMessage($from_id, "لطفا یکی از گزینه های زیر را انتخاب کنید: ", $settings_keyboard);
    die();
}

if ($user['step'] == "settings" && in_array($from_id, $bot_admins)) {
    switch ($text) {
        case "تنظیم هدیه زیرمجموعه گیری":
            setStep($from_id, "set-gift");
            sendMessage($from_id, "مقدار مورد نظر خود برای هدیه زیرمجموعه گیری را وارد کنید:", $back_To_Admin);
            break;

        case "تنظیم متن قوانین":
            setStep($from_id, "set-rule");
            sendMessage($from_id, "متن قوانین جدید را ارسال کنید:", $back_To_Admin);
            break;

        case "تنظیم متن استارت":
            setStep($from_id, "set-start");
            sendMessage($from_id, "متن استارت جدید را ارسال کنید:", $back_To_Admin);
            break;

        case "تنظیم متن پشتیبانی":
            setStep($from_id, "set-support");
            sendMessage($from_id, "متن پشتیبانی جدید را ارسال کنید:", $back_To_Admin);
            break;
    }
    die();
}

if ($user['step'] == "set-gift") {
    if (preg_match("/\d+(\.\d+)?$/", $text)) {
        $db->exec("UPDATE `config` SET `config_value` = '$text' WHERE `config_key` = 'gift' ");
        sendMessage($from_id, "مقدار هدیه زیرمجموعه گیری با موفقیت تغییر کرد!", $settings_keyboard);
        setStep($from_id, "settings");
    } else {
        sendMessage($from_id, "مقدار ورودی معتبر نمیباشد! لطفا دقت کنید.");
    }
    die();
}

if ($user['step'] == "set-rule") {
    $db->exec("UPDATE `config` SET `config_value` = '$text' WHERE `config_key` = 'rule' ");
    sendMessage($from_id, "متن قوانین ربات تغییر کرد!", $settings_keyboard);
    setStep($from_id, "settings");
    die();
}

if ($user['step'] == "set-start") {
    $db->exec("UPDATE `config` SET `config_value` = '$text' WHERE `config_key` = 'start' ");
    sendMessage($from_id, "متن استارت ربات تغییر کرد!", $settings_keyboard);
    setStep($from_id, "settings");
    die();
}

if ($user['step'] == "set-support") {
    $db->exec("UPDATE `config` SET `config_value` = '$text' WHERE `config_key` = 'support' ");
    sendMessage($from_id, "متن پشتیبانی ربات تغییر کرد!", $settings_keyboard);
    setStep($from_id, "settings");
    die();
}

if ($text == 'مدیریت کاربران' && in_array($from_id, $bot_admins)) {
    setStep($from_id, "manage-users");
    sendMessage($from_id, "لطفا یکی از گزینه های زیر را انتخاب کنید: ", $manage_user_keyboard);
    die();
}

if ($user['step'] == "manage-users" && in_array($from_id, $bot_admins)) {
    switch ($text) {
        case "جستجوی کاربر":
            setStep($from_id, "search-user");
            sendMessage($from_id, "شناسه کاربری که میخواهید جستجو کنید را بفرستید: ", $back_To_Admin);
            break;

        case "آزاد کردن":
            setStep($from_id, "unblock-user");
            sendMessage($from_id, "شناسه کاربری که میخواهید آزاد کنید را بفرستید: ", $back_To_Admin);
            break;

        case "مسدود کردن":
            setStep($from_id, "block-user");
            sendMessage($from_id, "شناسه کاربری که میخواهید مسدود کنید را بفرستید: ", $back_To_Admin);
            break;
    }
    die();
}

if ($user['step'] == "search-user") {
    if (!preg_match("/\d+(\.\d+)?$/", $text)) {
        sendMessage($from_id, "فرمت ارسالی صحیح نیست مجددا تلاش کنید!");
        die();
    }

    $stmt = $db->query("SELECT * FROM `users` WHERE `chat_id` = {$text} ");
    $result = $stmt->fetch();

    $status = $result['status'] == 1 ? 'آزاد' : 'بلاک';
    if ($result) {
        sendMessage($from_id, "🔰 اطلاعات کاربر جستجو شده!\n\n▫️ شناسه کاربری : {$result['chat_id']}\n▫️ موجودی : {$result['balance']} TRX\n▫️ تعداد زیرمجموعه ها : {$result['referal']}\n▫️ وضعیت حساب: $status", $manage_user_keyboard);
    } else {
        sendMessage($from_id, "کاربری با این شناسه یافت نشد!", $manage_user_keyboard);
    }
    setStep($from_id, "manage-users");
    die();
}

if ($user['step'] == "unblock-user") {
    if (!preg_match("/\d+(\.\d+)?$/", $text)) {
        sendMessage($from_id, "فرمت ارسالی صحیح نیست مجددا تلاش کنید!");
        die();
    }
    $db->exec("UPDATE `users` SET `status` = 1 WHERE `chat_id` = {$text} ");
    sendMessage($from_id, "کاربر با شناسه $text آزاد شد.", $manage_user_keyboard);
    setStep($from_id, "manage-users");
    die();
}

if ($user['step'] == "block-user") {
    if (!preg_match("/\d+(\.\d+)?$/", $text)) {
        sendMessage($from_id, "فرمت ارسالی صحیح نیست مجددا تلاش کنید!");
        die();
    }
    $db->exec("UPDATE `users` SET `status` = 0 WHERE `chat_id` = {$text} ");
    sendMessage($from_id, "کاربر با شناسه $text مسدود شد.", $manage_user_keyboard);
    setStep($from_id, "manage-users");
    die();
}
