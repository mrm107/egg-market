<?php
$data = [];

@$token = apache_request_headers()['Authorization'];

$access = check_token($token);
if ($access !== false) {
    $customer = $access;

    if (isset($_POST['name'])) {
        $name = utils__arabic_character_to_persian($controller->check_null_no($_POST['name']));
        if (isset($_POST['type'])) {
            $type = $controller->check_null_no($_POST['type']);

            $update = $controller->model->db->prepare("UPDATE customers SET name = :name, type = :type WHERE id = :id");
            $update->execute(array(":name" => $name, ":type" => $type, ":id" => $customer));

            $status = 200;
       } else {
           $status = 400;
       }
    } else {
        $status = 400;
    }
} else {
    $status = 403;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

