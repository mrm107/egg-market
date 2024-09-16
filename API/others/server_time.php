<?php
$data = [];

//@$token = apache_request_headers()['Authorization'];
//$access = check_token($token);
//if ($access !== false) {
//    $customer = $access;
//
$data['server_time'] = $controller->datetime;
$data['server_timestamp'] = $timestamp = strtotime($controller->datetime);
$status = 200;
//
//} else {
//    $status = 403;
//}

$export['status'] = $status;
$export['data'] = $data;

return $export;

