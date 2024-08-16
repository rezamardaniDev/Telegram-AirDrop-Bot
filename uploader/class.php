<?php

include 'config.php';

class Bot{

    protected $botApi;
    protected $webhook;

    public function __construct(){
        $this->botApi = TOKEN;
        $this->setWebhook("https://fara-it.ir/uploader/bot.php");
    }


    public function getBotAPI() {return $this->botApi;}
    public function getWebhook() {return $this->webhook;}


    public function setWebhook($link){
        $url = "https://api.telegram.org/bot".$this->getBotAPI()."/setWebhook?url=" . urlencode($link);
        $result = file_get_contents($url);
        $webhook = json_decode($result);
        if ($webhook['ok']){
            $this->webhook = true;
            return true;
        } else {
            $this->webhook = false;
            return false;
        }

    }

    public function TelegramAPI(string $method, array $params)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . TOKEN . '/' . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 'Content-Type: application/json',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function sendMessage($chat_id, $text, $reply_markup = null)
    {
        $this->TelegramAPI('sendMessage', [
            'chat_id'      => $chat_id,
            'text'         => $text,
            'parse_mode'   => 'Markdown',
            'reply_markup' => $reply_markup
        ]);
    }
}