<?php
$data = [];

@$token = apache_request_headers()['Authorization'];
$access = check_token($token);
if ($access !== false) {
    $customer = $access;

    $fetch = $controller->model->db->prepare("select id, mobile, name, owner_name, person_owner_name, phone1, phone2, phone3, phone4, type from customers where id = :id and status = 'accepted' limit 1");
    $fetch->execute(array(":id" => $customer));
    $data['profile'] = $fetch->fetch(PDO::FETCH_ASSOC);

    $status = 200;

} else {
    $status = 403;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

