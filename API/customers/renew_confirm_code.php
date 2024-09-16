<?php
$data = [];

if (isset($address[2]) and isset($address[3])) {
    $phone_number = $controller->check_null($address[2]);
    $phone_number = str_replace("-", "", $phone_number);
    $type = $controller->check_null_no($address[3]);
    if (preg_match("/^(?:09)(?:\d(?:-)?){9}$/m", $phone_number) and in_array($type, ['sms', 'call'])) {
        $check = $controller->model->db->prepare("SELECT id, `name`, `confirm_code` FROM customers WHERE mobile = :mobile AND (status = 'pending' or status = 'accepted') LIMIT 1");
        $check->execute(array(":mobile" => $phone_number));
        if ($check->rowCount() > 0) {
            $customer = $check->fetch();

//            $confirm_code = utils__generate_confirm_code();
//            $update = $controller->model->db->prepare("UPDATE customers SET confirm_code = :confirm_code WHERE id = :id");
//            $update->execute(array(":confirm_code" => $confirm_code, ":id" => $customer['id']));

            utils__sms_send_token($phone_number, $customer['confirm_code'], $type);
            $status = 200;
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

