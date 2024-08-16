<?php

$userKeyboard1 = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => '「 🌟 شروع کسب درآمد 」'], ['text' => '「 👥 برترین کاربران 」']],
        [['text' => '「 🔰 پروفایل کاربری 」']],
        [['text' => '「 ☎️ پشتیبانی 」'], ['text' => '「 🛑 قوانین 」']]
    ]
]);

$userKeyboard2 = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => '「 🌟 شروع کسب درآمد 」'], ['text' => '「 👥 برترین کاربران 」']],
        [['text' => '「 🔰 پروفایل کاربری 」']],
        [['text' => '「 ☎️ پشتیبانی 」'], ['text' => '「 🛑 قوانین 」']],
        [['text' => 'پنل مدیریت']],
    ]
]);

$userProfile1 = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'برداشت موجودی'], ['text' => 'ثبت کیف پول']],
        [['text' => 'بازگشت به منو اصلی']]
    ]
]);

$userProfile2 = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'برداشت موجودی'], ['text' => 'تغییر کیف پول']],
        [['text' => 'بازگشت به منو اصلی']]
    ]
]);

$withdraw = json_encode([
    'inline_keyboard' => [
        [['text' => '🟢 تایید برداشت 🟢', 'callback_data' => 'withdraw']]
    ]
]);

$backToProfile = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'بازگشت به پروفایل']]
    ]
]);

$backToMenu = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'بازگشت به منو اصلی']]
    ]
]);

$admin_panel = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'آمار ربات'], ['text' => 'پیام همگانی']],
        [['text' => 'تنظیمات'], ['text' => 'مدیریت کاربران']],
        [['text' => 'بازگشت به منو اصلی']]
    ]
]);

$settings_keyboard = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'تنظیم هدیه زیرمجموعه گیری']],
        [['text' => 'تنظیم متن استارت'], ['text' => 'تنظیم متن قوانین']],
        [['text' => 'تنظیم متن پشتیبانی']],
        [['text' => 'تنظیم کانال جوین اجباری'], ['text' => 'حذف کانال جوین اجباری']],
        [['text' => 'لیست کانال های جوین اجباری']],
        [['text' => 'بازگشت به مدیریت']]
    ]
]);

$manage_user_keyboard = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'جستجوی کاربر']],
        [['text' => 'مسدود کردن'], ['text' => 'آزاد کردن']],
        [['text' => 'بازگشت به مدیریت']]
    ]
]);

$back_To_Admin = json_encode([
    'resize_keyboard' => true,
    'keyboard' => [
        [['text' => 'بازگشت به مدیریت ']]
    ]
]);
