<?php

include 'config.php';
include 'functions.php';


$message = $db->query("SELECT * FROM `messages` WHERE `status` = 'pending' LIMIT 1")->fetch_assoc();
$sender = $message['sender'];
$users = $db->query("SELECT * FROM `users`");
if ($message) {
    while ($res = $users->fetch_assoc()['chat_id']) {
        sendMessage($res, $message['text']);
    }

    $db->query("UPDATE `messages` SET `status` = 'done' WHERE `id` = {$message['id']}");
    sendMessage($sender, "همگانی ارسال شد!");
}
