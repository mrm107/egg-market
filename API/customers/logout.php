<?php
$data = [];

@$token = apache_request_headers()['Authorization'];
$access = check_token($token);
if ($access !== false) {
    $customer = $access;

    $update = $controller->model->db->prepare("UPDATE customers_tokens SET active = 0 WHERE customer = :customer AND token = :token");
    $update->execute(array(":customer" => $customer, ":token" => $token));

    $status = 200;

} else {
    $status = 403;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

