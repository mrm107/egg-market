<?php
$data = [];

if (isset($_POST['loadID'])) {

    @$token = apache_request_headers()['Authorization'];
    $loadID = $controller->check_null($_POST['loadID']);

    $access = check_token($token);
    if ($access !== false) {
        $customer = $access;

        $fetch_data = $controller->model->db->prepare("SELECT id FROM loads WHERE status = 'accepted' AND id = :load limit 1");
        $fetch_data->execute(array(":load" => $loadID));
        if ($fetch_data->rowCount() > 0) {

            $update = $controller->model->db->prepare("DELETE FROM customers_bookmarked_loads WHERE `customer` = :customer AND `load` = :load");
            $update->execute(array(":customer" => $customer, ":load" => $loadID));

            $status = 200;
        } else {
            $status = 404;
        }
    } else {
        $status = 403;
    }
} else {
    $status = 400;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

