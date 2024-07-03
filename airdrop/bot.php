<?php

include 'config.php';
include 'functions.php';

$update = json_decode(file_get_contents('php://input'), true);
# ----------------- [ <- variables -> ] ----------------- #
if (isset($update['message'])){

}

# ----------------- [ <- main -> ] ----------------- #

if ($update['message']['text'] == '/start'){
    bot('sendMessage',[
        'chat_id' =>  5910225814,
        'text'    => 'salam'
    ]
);
}