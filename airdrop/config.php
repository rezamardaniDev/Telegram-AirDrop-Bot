<?php

error_reporting(E_ALL);
date_default_timezone_set('Asia/Tehran');

const APIKEY = '6331777475:AAEcCY_1UCjvgcvUzDpCOYtL7HG9BdkZVFI';

$bot_admins = [5910225814];
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
$db_username = 'faraitir_reza';
$db_password = 'mardani80';
$db_database = 'faraitir_airdrop';

$db = new mysqli($db_host, $db_username, $db_password, $db_database);
$db->query("SET NAMES 'utf8'");
$db->set_charset('utf8mb4');

if  ($db->connect_error){
    print_r($db->connect_error);
}