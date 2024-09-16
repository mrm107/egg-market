<?php
$data = [];

//@$token = apache_request_headers()['Authorization'];
//$access = check_token($token);
//if ($access !== false) {
//    $customer = $access;
//
    $fetch_data = $controller->model->db->prepare("SELECT id, title FROM locations_provinces ORDER BY `title` ASC");
    $fetch_data->execute();
    $fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);

    $data['provinces'] = $fetched_data;
    $status = 200;
//
//} else {
//    $status = 403;
//}

$export['status'] = $status;
$export['data'] = $data;

return $export;

