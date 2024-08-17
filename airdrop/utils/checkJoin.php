<?php

$stmt = $db->query("SELECT * FROM `channels`");
$channels = $stmt->fetchAll();

$user_join = true;
$channel_key = [
    'inline_keyboard' => []
];

foreach ($channels as $channel) {
    $checked = getChatMember($channel['channel_id'], $from_id);

    if ($checked == 'left') {
        $user_join = false;
        $link = preg_replace('/^(@|https:\/\/t\.me\/)/', '', $channel['channel_link']);
        $channel_key['inline_keyboard'][] = [['text' => $channel['channel_name'], 'url' => "https://t.me/$link"]];
    }
}
if (!$user_join) {
    sendMessage($from_id, "ابتدا در کانال های زیر عضو شو!", json_encode($channel_key));
    die;
}
