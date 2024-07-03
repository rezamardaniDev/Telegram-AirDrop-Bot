<?php

error_reporting(E_ALL);
date_default_timezone_set('Asia/Tehran');

const APIKEY = '6331777475:AAEcCY_1UCjvgcvUzDpCOYtL7HG9BdkZVFI';

$bot_admins = [''];
$bot_username = '';
$bot_channels = [
    'main' => '',
    'support' => ''
];
$bot_channels_id = [
    'main' => '',
    'support' => ''
];
$bot_name = '';
$support_bot = ['@'];

$db_host = 'localhost';
$db_username = '';
$db_passwrod = '';
$db_database = '';

$db = new mysqli($db_host, $db_username, $db_password, $db_database);
$db->query("SET NAMES 'utf8'");
$db->set_charset('utf8mb4');