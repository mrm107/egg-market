<?php
$data = [];

if (isset($_POST['origin_field1']) and isset($_POST['weight']) and isset($_POST['count']) and isset($_POST['yolk_type']) and isset($_POST['client']) and isset($_POST['type']) and isset($_POST['phones']) and isset($_POST['pack_type']) and isset($_POST['print_type'])) {

    @$token = apache_request_headers()['Authorization'];
    $client = utils__arabic_character_to_persian($controller->check_null_no($_POST['client']));

    $origin_field1 = $controller->check_null($_POST['origin_field1']);
    @$origin_field2 = utils__arabic_character_to_persian($controller->check_null_no($_POST['origin_field2']));

    $weight = utils__arabic_character_to_persian($controller->check_null_no($_POST['weight']));
    $count = utils__arabic_character_to_persian($controller->check_null_no($_POST['count']));
    $print_type = utils__arabic_character_to_persian($controller->check_null_no($_POST['print_type']));
    $yolk_type = utils__arabic_character_to_persian($controller->check_null_no($_POST['yolk_type']));
    @$box_type = utils__arabic_character_to_persian($controller->check_null_no($_POST['box_type']));
    @$stage_type = utils__arabic_character_to_persian($controller->check_null_no($_POST['stage_type']));
    $type = utils__arabic_character_to_persian($controller->check_null_no($_POST['type']));
    $pack_type = utils__arabic_character_to_persian($controller->check_null_no($_POST['pack_type']));
    @$quality = utils__arabic_character_to_persian($controller->check_null_no($_POST['quality']));
    @$price = utils__arabic_character_to_persian($controller->check_null_no($_POST['price']));

    @$description = utils__arabic_character_to_persian($controller->check_null_no($_POST['description']));
    @$owner_name = utils__arabic_character_to_persian($controller->check_null_no($_POST['owner_name']));
    @$person_owner_name = utils__arabic_character_to_persian($controller->check_null_no($_POST['person_owner_name']));
    $phones = $_POST['phones'];

    if (!(is_array($phones) and sizeof($phones) > 0) || $origin_field1 == NULL || $client == NULL || $weight == NULL || $count == NULL || $yolk_type == NULL || $type == NULL || $pack_type == NULL) {
        $export['status'] = 400;
        return $export;
    }

    $access = check_token($token);

    if ($access !== false) {
        $customer = $access;
        try {

            $insert = $controller->model->db->prepare("INSERT INTO `loads` (`origin_field1`, `origin_field2`, `reg_date`, `reg_client`, `status`, `description`, `owner_name`, `weight`, `count`, `person_owner_name`, `print_type`, `yolk_type`, `box_type`, `stage_type`, `type`, `pack_type`, `reg_customer`, `price`, `quality`) 
                                                                              VALUES (:origin_field1, :origin_field2, :reg_date, :reg_client, :status, :description, :owner_name, :weight, :count, :person_owner_name, :print_type, :yolk_type, :box_type, :stage_type, :type, :pack_type, :reg_customer, :price, :quality)");
            $insert->execute(array(
                ":origin_field1" => $origin_field1,
                ":origin_field2" => $origin_field2,
                ":reg_date" => $controller->datetime,
                ":reg_client" => $client,
                ":status" => "accepted",
                ":description" => $description,
                ":owner_name" => $owner_name,
                ":person_owner_name" => $person_owner_name,
                ":weight" => $weight,
                ":count" => $count,
                ":print_type" => $print_type,
                ":yolk_type" => $yolk_type,
                ":box_type" => $box_type,
                ":stage_type" => $stage_type,
                ":type" => $type,
                ":pack_type" => $pack_type,
                ":reg_customer" => $customer,
                ":price" => $price,
                ":quality" => $quality,
            ));
            $loadID = $controller->model->db->lastInsertId();
            foreach ($phones as $phone) {
                if ($controller->check_null_no($phone) !== null) {
                    $insert = $controller->model->db->prepare("INSERT INTO `loads_phones`(`load`, `phone`) VALUES (:load, :phone)");
                    $insert->execute(array(
                        ":load" => $loadID,
                        ":phone" => utils__fatoen_numbers($controller->check_null_no($phone))
                    ));
                }
            }
            require_once('../App/triggers/after-publish-load.php');
//            @after_publish_load($loadID);
            $message_id = after_publish_load($loadID);

            $update = $controller->model->db->prepare("update loads set `telegram_message_id` = :message_id where id = :load_id");
            $update->execute(array(":message_id" => $message_id, ":load_id" => $loadID));

            $status = 200;
        } catch (Exception $e) {
//            echo 'Message: ' . $e->getMessage();
            $status = 500;
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

