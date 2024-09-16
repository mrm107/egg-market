<?php
$data = [];

if (isset($address[2]) and isset($address[3])) {
    $phone_number = $controller->check_null($address[2]);
    $phone_number = str_replace("-", "", $phone_number);
    $client = $controller->check_null_no($address[3]);
    if (in_array($client, ['core', 'web', 'ios', 'android', 'telegram_bot'])) {
        if (preg_match("/^(?:09)(?:\d(?:-)?){9}$/m", $phone_number)) {
            $confirm_code = utils__generate_confirm_code();

            $check = $controller->model->db->prepare("SELECT id FROM customers WHERE mobile = :mobile LIMIT 1");
            $check->execute(array(":mobile" => $phone_number));
            if ($check->rowCount() > 0) {
                $customer = $check->fetch();
                $update = $controller->model->db->prepare("UPDATE customers SET confirm_code = :confirm_code WHERE id = :id");
                $update->execute(array(":confirm_code" => $confirm_code, ":id" => $customer['id']));
                $status = 200;
            } else {
                $insert = $controller->model->db->prepare("INSERT INTO customers (`mobile`, `reg_date`, `reg_client`, `confirm_code`, `status`) VALUES (:mobile, :reg_date, :reg_client, :confirm_code, :status)");
                $insert->execute(array(
                    ":mobile" => $phone_number,
                    ":reg_date" => $controller->datetime,
                    ":reg_client" => $client,
                    ":confirm_code" => $confirm_code,
                    ":status" => 'pending'
                ));
                $status = 203;
            }
            utils__sms_send_token($phone_number, $confirm_code, 'sms');
        } else {
            $status = 400;
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

