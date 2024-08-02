<?php

# this file for testing

require '../database/connection.php';

try {

    $db->beginTransaction();

    $querys = [
    "CREATE TABLE IF NOT EXISTS `users` (
    `id`      INT NOT NULL AUTO_INCREMENT,
    `chat_id` BIGINT NOT NULL,
    `balance` DECIMAL(10, 2) DEFAULT 0.5,
    `wallet`  TEXT NULL,
    `step`    TEXT NULL,
    `status`  BOOLEAN DEFAULT 1,
    `joined`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    `referal` INT DEFAULT 0,
    PRIMARY KEY (`id`)
)",
    "CREATE TABLE IF NOT EXISTS `config` (
    `id`           INT NOT NULL AUTO_INCREMENT,
    `config_key`   VARCHAR(255) NOT NULL,
    `config_value` TEXT NULL,
    PRIMARY KEY (`id`)
)",
    "CREATE TABLE IF NOT EXISTS `invitations` (
    `id`      INT NOT NULL AUTO_INCREMENT,
    `caller`  BIGINT NOT NULL,
    `invited` BIGINT NOT NULL,
    PRIMARY KEY (`id`)
)",
    "CREATE TABLE IF NOT EXISTS `messages` (
    `id`     INT NOT NULL AUTO_INCREMENT,
    `text`   TEXT NOT NULL,
    `sender` BIGINT NOT NULL,
    `status` ENUM ('done', 'pending') DEFAULT 'pending',
    PRIMARY KEY (`id`)
)",
    "CREATE TABLE IF NOT EXISTS `withdraw_request` (
    `id`         INT NOT NULL AUTO_INCREMENT,
    `chat_id`    BIGINT NOT NULL,
    `wallet`     TEXT NOT NULL,
    `amount`     DECIMAL(10, 2) NOT NULL,
    `status`     ENUM ('done', 'registered') DEFAULT 'registered',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
    )",
    "INSERT INTO `config` (`config_key`, `config_value`) VALUES ('rule', 'ثبت نشده')",
    "INSERT INTO `config` (`config_key`, `config_value`) VALUES ('support', 'ثبت نشده')",
    "INSERT INTO `config` (`config_key`, `config_value`) VALUES ('start', 'ثبت نشده')",
    "INSERT INTO `config` (`config_key`, `config_value`) VALUES ('gift', 0.5)"
];

    foreach ($querys as $query) {
        $db->exec($query);
    }
    $db->commit();

    echo "جداول دیتابیس ساخته شد!";
} catch (PDOException $e) {
    $db->rollBack();
    echo "Error!" . $e->getMessage();
}
