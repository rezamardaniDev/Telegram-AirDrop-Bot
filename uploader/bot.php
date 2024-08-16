<?php

include 'class.php';
$update = json_decode(file_get_contents('php://input'));


$bot = new Bot();

if ($update->message == '/start')
$bot->sendMessage($update->message->from_id, "سلام این یک ربات با شی گرایی هست!");

