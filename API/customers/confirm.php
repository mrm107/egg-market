<?php
$data = [];

if (isset($address[2]) and isset($address[3])) {
    $phone_number = $controller->check_null($address[2]);
    $phone_number = str_replace("-", "", $phone_number);
    $confirm_code = $controller->check_null_no($address[3]);
    if (preg_match("/^(?:09)(?:\d(?:-)?){9}$/m", $phone_number)) {
        $check = $controller->model->db->prepare("SELECT id, `name` FROM customers WHERE mobile = :mobile AND confirm_code = :confirm_code AND (status = 'pending' or status = 'accepted') LIMIT 1");
        $check->execute(array(":mobile" => $phone_number, ":confirm_code" => $confirm_code));
        if ($check->rowCount() > 0) {
            $customer = $check->fetch();
            $token = utils__generate_token();

            $update = $controller->model->db->prepare("UPDATE customers SET status = 'accepted', confirm_code = NULL WHERE id = :id");
            $update->execute(array(":id" => $customer['id']));

            do {
                $insert = $controller->model->db->prepare("INSERT INTO customers_tokens (`customer`, `token`, `active`, `creation_date`) VALUES (:customer, :token, 1, :date)");
                $request_status = $insert->execute(array(":customer" => $customer['id'], ":token" => $token, ":date" => $controller->datetime));
            } while(!$request_status);

            if ($controller->check_null_no($customer['name']) == NULL) {
                $status = 203;
            } else {
                $status = 200;
            }
            $data['token'] = $token;
        } else {
            $status = 401;
        }
    } else {
        $status = 400;
    }
} else {
    $status = 400;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

