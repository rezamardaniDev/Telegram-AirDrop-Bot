<?php

$userKeyboard = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'شروع کسب درآمد'], ['text' => 'برترین کاربران']],
        [['text' => 'پروفایل کاربری']],
        [['text' => 'پشتیبانی'], ['text' => 'قوانین']]
    ]
]);

$userProfile = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'برداشت موجودی'], ['text' => 'تغییر کیف پول']],
        [['text' => 'بازگشت به منو اصلی']]
    ]
]);

$withdraw = json_encode([
    'inline_keyboard' => [
        [['text' => 'تایید برداشت', 'callback_data' => 'withdraw']]
    ]
]);

$backToProfile = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'بازگشت به پروفایل']]
    ]
]);
