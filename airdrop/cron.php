<?php

include 'config.php';
include 'functions.php';


$message = $db->query("SELECT * FROM `messages` WHERE `status` = 'pending' LIMIT 1")->fetch_assoc();
$users = $db->query("SELECT * FROM `users`");
if ($message) {
    while ($res = $users->fetch_assoc()['chat_id']) {
        sendMessage($res, $message['text']);
    }

    $db->query("UPDATE `messages` SET `status` = 'done' WHERE `id` = {$message['id']}");
    sendMessage($bot_admins[0], "همگانی ارسال شد!");
}
