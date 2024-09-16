<?php
$data = [];

if (isset($_POST['loadID']) and isset($_POST['origin_field1']) and isset($_POST['weight']) and isset($_POST['count']) and isset($_POST['yolk_type']) and isset($_POST['client']) and isset($_POST['type']) and isset($_POST['phones']) and isset($_POST['pack_type']) and isset($_POST['print_type'])) {


    @$token = apache_request_headers()['Authorization'];
    $client = utils__arabic_character_to_persian($controller->check_null_no($_POST['client']));
    $loadID = $controller->check_null($_POST['loadID']);

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

    if ($loadID == NULL || !(is_array($phones) and sizeof($phones) > 0) || $origin_field1 == NULL || $client == NULL || $weight == NULL || $count == NULL || $yolk_type == NULL || $type == NULL || $pack_type == NULL) {
        $export['status'] = 400;
        return $export;
    }

    $access = check_token($token);

    if ($access !== false) {
        $customer = $access;

        $check = $controller->model->db->prepare("SELECT id FROM loads WHERE id = :id AND reg_customer = :reg_customer AND (`status` = 'accepted' OR `status` = 'pending') LIMIT 1");
        $check->execute(array(":id" => $loadID, ":reg_customer" => $customer));
        if ($check->rowCount() > 0) {
            $check = $check->fetch();

            $loadID = $check['id'];

            try {
                $update = $controller->model->db->prepare("UPDATE `loads` SET `origin_field1` = :origin_field1, `origin_field2` = :origin_field2, `description` = :description, `owner_name` = :owner_name, `weight` = :weight, `count` = :count, `print_type` = :print_type, `yolk_type` = :yolk_type, `box_type` = :box_type, `stage_type` = :stage_type, `person_owner_name` = :person_owner_name, `type` = :type, `pack_type` = :pack_type, `price` = :price, `quality` = :quality WHERE `id` = :id AND `reg_customer` = :reg_customer");
                $update->execute(array(
                    ":origin_field1" => $origin_field1,
                    ":origin_field2" => $origin_field2,
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
                    ":id" => $loadID
                ));

                $delete = $controller->model->db->prepare("DELETE FROM loads_phones WHERE `load` = :loadID ");
                $delete->execute(array(
                    ":loadID" => $loadID
                ));
                foreach ($phones as $phone) {
                    if ($controller->check_null_no($phone) !== null) {
                        $insert = $controller->model->db->prepare("INSERT INTO `loads_phones` (`load`, `phone`) VALUES (:load, :phone)");
                        $insert->execute(array(
                            ":load" => $loadID,
                            ":phone" => utils__fatoen_numbers($controller->check_null_no($phone))
                        ));
                    }
                }
                $status = 200;
            } catch (Exception $e) {
                echo 'Message: ' . $e->getMessage();
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

