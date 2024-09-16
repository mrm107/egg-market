<?php
$data = [];

if (isset($_POST['loadID'])) {

    @$token = apache_request_headers()['Authorization'];
    $loadID = $controller->check_null($_POST['loadID']);

    $access = check_token($token);
    if ($access !== false) {
        $customer = $access;

        $fetch_data = $controller->model->db->prepare("SELECT loads_phones.phone, loads.owner_name, loads.person_owner_name FROM loads_phones INNER JOIN loads ON loads_phones.load = loads.id WHERE loads.status = 'accepted' AND loads_phones.load = :load");
        $fetch_data->execute(array(":load" => $loadID));
        if ($fetch_data->rowCount() > 0) {
            $fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);

            foreach ($fetched_data as $item) {
                $data['contact']['phones'][] = $item['phone'];
            }
            $data['contact']['name'] = $fetched_data[0]['owner_name'];
            $data['contact']['personName'] = $fetched_data[0]['person_owner_name'];

            $update = $controller->model->db->prepare("INSERT IGNORE INTO customers_seen_loads (customer, `load`) VALUES (:customer, :load); UPDATE customers_seen_loads SET seen = 1 WHERE customer = :customer AND `load` = :load");
            $update->execute(array(":customer" => $customer, ":load" => $loadID));

            $status = 200;
        } else {
            $data['contact']['phones'] = [];
            $data['contact']['name'] = "";
//            $status = 404;
            $status = 200;
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

