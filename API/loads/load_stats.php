<?php
$data = [];

if (isset($_POST['loadID'])) {

    @$token = apache_request_headers()['Authorization'];
    $loadID = $controller->check_null($_POST['loadID']);

    $access = check_token($token);
    if ($access !== false) {
        $customer = $access;

        $check = $controller->model->db->prepare("SELECT id FROM loads WHERE id = :id AND reg_customer = :reg_customer LIMIT 1");
        $check->execute(array(":id" => $loadID, ":reg_customer" => $customer));
        if ($check->rowCount() > 0) {
            $check = $check->fetch();

            $seen_load = $controller->model->db->prepare("SELECT count(customer) FROM customers_seen_loads WHERE `load` = :load");
            $seen_load->execute(array(
                ":load" => $loadID
            ));
            $seen_load = $seen_load->fetch();
            $data['seen_load'] = $seen_load[0];

            $seen_contact = $controller->model->db->prepare("SELECT count(customer) FROM customers_seen_loads WHERE `load` = :load and seen = 1");
            $seen_contact->execute(array(
                ":load" => $loadID
            ));
            $seen_contact = $seen_contact->fetch();
            $data['seen_contact'] = $seen_contact[0];

            $status = 200;
        } else {
            $status = 403;
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

