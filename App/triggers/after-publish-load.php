<?php

function after_publish_load($loadId)
{
    $url = 'http://195.248.241.237:8000/sendload';
//    $chatId = "-1001189628215"; // test
    $chatId = "-1001116693025"; // final
    $data = array("chat_id" => $chatId, "load_id" => $loadId);

    $options = array(
        'http' => array(
            'header' => "Authorization: f3z4whkBRMPgpKTE23YEuygCrkncHTLXnf8qtj2pQ4j8qcEF7w\r\n" . "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result_body = json_decode($result);
    return $result_body->message_id;
}