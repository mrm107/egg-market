<?php
$data = [];

if (isset($_POST['area'])) {

    @$token = apache_request_headers()['Authorization'];
    $area = utils__arabic_character_to_persian($controller->check_null_no($_POST['area']));

//    $access = check_token($token);
//
//    if ($access !== false) {
//        $customer = $access;

        if (isset($_POST['province']) and $controller->check_null($_POST['province']) != NULL) {
            $province = $controller->check_null($_POST['province']);
            $fetch_data = $controller->model->db->prepare("SELECT id, title, province FROM locations_field2 WHERE province = :province AND title LIKE concat_ws('%',:area,'%') ORDER BY `title` ASC");
            $fetch_data->execute(array(":province" => $province, ":area" => $area));
        } else {
            $fetch_data = $controller->model->db->prepare("SELECT id, title, province FROM locations_field2 WHERE title LIKE concat_ws('%',:area,'%') ORDER BY `title` ASC");
            $fetch_data->execute(array(":area" => $area));
        }
        $fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);

        $data['suggestions'] = $fetched_data;
        $status = 200;

//    } else {
//        $status = 403;
//    }
} else {
    $status = 400;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

