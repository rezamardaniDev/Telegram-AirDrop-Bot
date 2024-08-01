<?php

$hostName = 'localhost';
$userName = 'faraitir_reza';
$password = 'mardani80';
$dbName = 'faraitir_airdrop';

try {
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    $db = new PDO("mysql:host=$hostName;dbname=$dbName;charset=utf8mb4", $userName, $password, $options);

    echo "connected successfully";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}