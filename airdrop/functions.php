<?php

function bot(string $method, array $params)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.telegram.org/bot' . APIKEY . '/' . $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => 'Content-Type: application/json',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}