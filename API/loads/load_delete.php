<?php
$data = [];

if (isset($_POST['loadID'])) {

    @$token = apache_request_headers()['Authorization'];
    $loadID = $controller->check_null($_POST['loadID']);

    if ($loadID == NULL) {
        $export['status'] = 400;
        return $export;
    }

    $access = check_token($token);

    if ($access !== false) {
        $customer = $access;

        $check = $controller->model->db->prepare("SELECT id, telegram_message_id FROM loads WHERE id = :id AND reg_customer = :reg_customer LIMIT 1");
        $check->execute(array(":id" => $loadID, ":reg_customer" => $customer));
        if ($check->rowCount() > 0) {
            $check = $check->fetch();

            $loadID = $check['id'];

            try {
                $delete = $controller->model->db->prepare("UPDATE loads SET status = 'expired' WHERE `id` = :loadID AND (`status` = 'accepted' or `status` = 'pending') ");
                $delete->execute(array(
                    ":loadID" => $loadID
                ));


                $message_id = $check['telegram_message_id'];

                if ($controller->check_null($message_id) != NULL) {
                    require_once('../App/triggers/remove-load.php');
                    remove_load($message_id);
                }

                $status = 200;
            } catch (Exception $e) {
//                echo 'Message: ' . $e->getMessage();
                $status = 500;
            }
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

