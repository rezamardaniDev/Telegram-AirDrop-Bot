<?php

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

mysqli_query($db, "INSERT INTO `users` (`chat_id`) VALUES (451258)");
