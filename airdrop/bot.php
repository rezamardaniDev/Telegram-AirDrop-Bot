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
    $user = $db->query("SELECT * FROM `users` WHERE `chat_id` = ($from_id)")->fetch_assoc();
}
if (array_key_exists('callback_query', $update)) {
    $data = $update['callback_query']['data'];
    $message_id = $update['callback_query']['message']['message_id'];
    $chat_id = $update['callback_query']['message']['chat']['id'];
    $from_id = $update['callback_query']['from']['id'];
    $chat_type = $update['callback_query']['message']['chat']['type'];
    $user = $db->query("SELECT * FROM `users` WHERE `chat_id` = ($from_id)")->fetch_assoc();
}
# ----------------- [ <- user panel -> ] ----------------- #
if ($chat_type != 'private') {
    die();
}

if (preg_match('/^\/start/', $text) || $text == 'بازگشت به منو اصلی') {

    setStep($from_id, 'home');
    $user_Invite_Id = explode(" ", $text)[1];

    if ($user_Invite_Id && $user_Invite_Id != $from_id && !$user) {
        $validate_Referal_Id = $db->query("SELECT * FROM `users` WHERE `chat_id` = ($user_Invite_Id)"); // validate invite id
        if ($validate_Referal_Id) {
            $new_Invitation_Text = "
            🎁 تبریک!
            یک کاربر جدید با لینک شما وارد ربات شد
            
            👤 نام شخص : $first_name
            👀 شناسه عددی : `$from_id`
                ";

            $db->query("INSERT INTO `invitations` (`caller`, `invited`) VALUES ($user_Invite_Id, $from_id)");
            $db->query("UPDATE `users` SET `balance` = `balance` + 0.5, `referal` = `referal` + 1 WHERE `chat_id` = ($user_Invite_Id) ");
            sendMessage($user_Invite_Id, $new_Invitation_Text);
        }
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
    sendPhoto($from_id, "https://fara-it.ir/airdrop/banner.jpg", $txt);
    die();
}

if ($text == '「 👥 برترین کاربران 」') {
    $topUsers = mysqli_query($db, "SELECT * FROM `users` ORDER BY `referal` DESC LIMIT 10");
    $txt = "*💯 10 کاربر برتر ربات به ترتیب بیشترین زیرمجموعه*\n\n";
    $rank = 1;
    while ($res = $topUsers->fetch_assoc()) {
        $txt .= "🔰 $rank | `{$res['chat_id']}`\n💰 *{$res['balance']} TRX* | 👤 {$res['referal']} referal\n\n";
        $rank++;
    }
    sendMessage($from_id, $txt);
    die();
}

if ($text == '「 🔰 پروفایل کاربری 」' || $text == 'بازگشت به پروفایل' || $text == '/profile') {
    setStep($from_id, 'profile');

    $balance = $user['balance'];
    $wallet = $user['wallet'] ?? 'ثبت نشده';
    $referal = $user['referal'];
    $txt = "
🔺 پروفایل شما

💳 آدرس کیف پول:
`$wallet`

💰موجودی: $balance TRX
👀 شناسه کاربری: `$from_id`
📊 تعداد زیرمجموعه ها: $referal
    ";
    sendMessage($from_id, $txt, $userProfile);
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
        $txt = "
♻️ اطلاعات تراکنش!

💎 برداشت : $balance TRX
💳 به آدرس : 
`$wallet`

❗️در صورتی که اطلاعات بالا مورد تایید است لطفا روی دکمه زیر کلیک کنید
        ";
        sendMessage($from_id, $txt, $withdraw);
    } else {
        sendMessage($from_id, "موجودی شما برای برداشت کافی نیست! حداقل مقدار قابل برداشت 5 ترون میباشد.", $backToProfile);
    }
    die();
}

if ($data == 'withdraw' && getStep($from_id) == 'withdraw') {
    $check_withdraw_request = mysqli_query($db, "SELECT * FROM `withdraw_request` WHERE `chat_id` = ($from_id) AND `status` = 'registered' ");
    if ($check_withdraw_request->num_rows == 0) {

        $user_wallet = $user['wallet'];
        $user_balance = $user['balance'];
        $withdraw_time = date("Y/m/d H:i:s");

        mysqli_query($db, "INSERT INTO `withdraw_request` (`chat_id`, `wallet`, `amount`) VALUES ($from_id, '$user_wallet', $user_balance)");
        mysqli_query($db, "UPDATE `users` SET `balance` = 0 WHERE `chat_id` = ($from_id) ");
        $recept = mysqli_query($db, "SELECT * FROM `withdraw_request` WHERE `chat_id` = ($from_id) AND `status` = 'registered' ")->fetch_array();
        $recept_txt = "
🤖 درخواست برداشت جدید

👤 شناسه کاربر : $from_id

🔰 مقدار برداشت : {$recept['amount']} TRX
💳 آدرس کیف پول : 
`{$recept['wallet']}`

تاریخ درخواست :
$withdraw_time
        ";
        sendMessage(-1002180465057, $recept_txt, json_encode([
            'inline_keyboard' => [
                [['text' => 'تایید واریز', 'callback_data' => $from_id]]
            ]
        ]));

        $txt = "
✅ درخواست برداشت شما در صف انتظار قرار گرفت!

🔰 مقدار برداشت : {$recept['amount']} TRX
💳 آدرس کیف پول : 
`{$recept['wallet']}`

⏰ زمان ثبت درخواست :
$withdraw_time
        ";

        editMessage($chat_id, $txt, $message_id);
        setStep($from_id, 'profile');
    } else {
        editMessage($chat_id, "شما یک درخواست تایید نشده دارید! لطفا تا بررسی آن صبر کنید", $message_id);
    }
    die();
}

if ($data and $data != 'withdraw') {

    sendMessage($data, "کاربر گرامی واریز برای شما انجام شد!");
    $recept = mysqli_query($db, "SELECT * FROM `withdraw_request` WHERE `chat_id` = ($data) AND `status` = 'registered' ")->fetch_array();
    $withdraw_time = date("Y/m/d H:i:s");
    $recept_txt = "
🤖 واریز انجام شد

👤 شناسه کاربر : $data

🔰 مقدار برداشت : {$recept['amount']} TRX
💳 آدرس کیف پول : 
`{$recept['wallet']}`

تاریخ واریز :
$withdraw_time
    ";
    editMessage(-1002180465057, $recept_txt, $message_id, json_encode([
        'inline_keyboard' => [
            [['text' => 'واریز شد', 'callback_data' => 'done']]
        ]
    ]));
    mysqli_query($db, "UPDATE `withdraw_request` SET `status` = 'done' WHERE `chat_id` = ($data) ");
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
