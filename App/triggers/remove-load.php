<?php

function remove_load($messageId)
{
    $url = 'http://195.248.241.237:8000/removemessage';
//    $chatId = "-1001189628215"; // test
    $chatId = "-1001116693025"; // final
    $data = array("chat_id" => $chatId, "message_id" => $messageId);

    $options = array(
        'http' => array(
            'header' => "Authorization: f3z4whkBRMPgpKTE23YEuygCrkncHTLXnf8qtj2pQ4j8qcEF7w\r\n" . "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
}