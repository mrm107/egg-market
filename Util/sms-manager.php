<?php
require('call-api.php');

$api_url = "https://api.kavenegar.com/v1/";
$api_key = "45554A562B743434614D544B52364249435A3168665573666C66767962336646";
$api = $api_url . $api_key;

function utils__sms_send($receptors, $message)
{
    global $api;
    utils__call_api(
        'POST',
        "{$api}/sms/send.json",
        array("receptor" => $receptors, "message" => $message
        ));
}

function utils__sms_send_token($receptor, $token, $type)
{
    global $api;
    utils__call_api(
        'POST',
        "{$api}/verify/lookup.json",
        array("receptor" => $receptor, "token" => $token, "template" => "EggMarketLogin", "type" => $type
        ));
}