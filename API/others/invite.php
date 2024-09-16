<?php
$data = [];
if (isset($_POST['phone_number'])) {

    @$token = apache_request_headers()['Authorization'];

    $phone_number = $controller->check_null(utils__fatoen_numbers($_POST['phone_number']));
    $phone_number = str_replace("-", "", $phone_number);

    if (preg_match("/^(?:09|\+?989|9|00989)(?:\d(?:-)?){9}$/m", $phone_number)) {

        $access = check_token($token);
        if ($access !== false) {
            $customer = $access;
            $customer_data = $controller->model->fetch_one("customers", "where id = {$customer}");
            $site_address = $controller->conf_database->conf_web_client_address;

            $message = "سلام، {$customer_data['name']} شما را به سامانه مرغداران ایران دعوت کرده است.\nدانلود اپلیکیشن از طریق لینک زیر:\n{$site_address}";
            utils__sms_send($phone_number, $message);

            $status = 200;

        } else {
            $status = 403;
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

