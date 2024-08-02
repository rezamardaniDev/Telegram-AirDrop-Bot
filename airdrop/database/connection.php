<?php

$hostName = 'localhost'; # تغییر ندید
$userName = 'faraitir_reza'; # یوزرنیم دیتابیس
$password = 'mardani80'; # پسورد دیتابیس
$dbName = 'faraitir_airdrop'; # نام دیتابیس

try {

    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    $db = new PDO("mysql:host=$hostName;dbname=$dbName;charset=utf8mb4", $userName, $password, $options);
    # Connected!

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
