<?php

include 'config.php';
include '../utils/functions.php';
include '../database/connection.php';


$stmt = $db->query("SELECT * FROM `messages` WHERE `status` = 'pending' LIMIT 1");
$message = $stmt->fetch();

$sender = $message['sender'];
$stmt = $db->query("SELECT * FROM `users`");
$users = $stmt->fetchAll();

if ($message) {
    foreach ($users as $user) {
        sendMessage($user['chat_id'], $message['text']);
    }

    $db->exec("UPDATE `messages` SET `status` = 'done' WHERE `id` = {$message['id']}");
    sendMessage($sender, "پیام شما به تمام اعضای ربات ارسال شد.");
}
