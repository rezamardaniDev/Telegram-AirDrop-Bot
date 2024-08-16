<?php

function sendMessage($chat_id, $text, $reply_markup = null)
{
    TelegramAPI('sendMessage', [
        'chat_id'      => $chat_id,
        'text'         => $text,
        'parse_mode'   => 'Markdown',
        'reply_markup' => $reply_markup
    ]);
}

function deleteMessages($chat_id, $message_id)
{
    TelegramAPI('deleteMessage', [
        'chat_id'     => $chat_id,
        'message_id'  => $message_id
    ]);
}

function editMessage($chat_id, $text, $message_id, $reply_markup = null)
{
    TelegramAPI('editMessageText', [
        'chat_id'      => $chat_id,
        'text'         => $text,
        'message_id'   => $message_id,
        'parse_mode'   => 'Markdown',
        'reply_markup' => $reply_markup
    ]);
}

function debug($data)
{
    $result = print_r($data, true);
    sendMessage(5910225814, $result);
}
