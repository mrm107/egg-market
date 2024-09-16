<?php
$data = [];

$count_limit = 5;

if (isset($_POST['loadID']) and $controller->check_null($_POST['loadID']) != NULL) {

    @$token = apache_request_headers()['Authorization'];
    $access = check_token($token);

    $loadID = $controller->check_null($_POST['loadID']);

    $fetch_data = $controller->model->db->prepare("SELECT id AS loadID,origin_field1,origin_field2,reg_date,UNIX_TIMESTAMP(reg_date) AS reg_date_timestamp,description, weight, count, print_type, yolk_type, box_type, stage_type, type, pack_type, owner_name, status, price, quality FROM loads WHERE id = :id AND (status = 'accepted' || status = 'expired') LIMIT 1");
    $fetch_data->execute(array(":id" => $loadID));
    $fetched_data = $fetch_data->fetch(PDO::FETCH_ASSOC);

    $print_type = [
        "with" => "با پرینت",
        "without" => "بدون پرینت",
        "ability" => "با قابلیت پرینت",
        NULL => "نامشخص",
    ];

    $yolk_type = [
        "golden" => "طلایی",
        "simple" => "ساده",
        "corn" => "ذرتی",
        NULL => "نامشخص",
    ];

    $pack_type = [
        "bulk" => "فله (شانه ای)",
        "box" => "بسته بندی",
        NULL => "نامشخص",
    ];

    $type = [
        "announcement" => "اعلام بار",
        "request" => "درخواست بار",
    ];

    $quality = [
        "lux" => "لوکس",
        "grade-1" => "درجه ۱",
        "grade-2" => "درجه ۲",
        "for-factories" => "درجه ۳ (کارخانه‌ای)",
        NULL => "نامشخص",
    ];

    $tmpDetail = [];
    $controller->check_null_no($fetched_data['pack_type']) && $tmpDetail[] = array("title" => "نوع بسته بندی", "value" => $pack_type[$fetched_data['pack_type']]);
    if($fetched_data['pack_type'] == 'bulk') {
        $controller->check_null_no($fetched_data['weight']) && $tmpDetail[] = array("title" => "وزن کارتن", "value" => $fetched_data['weight']);
    } else {
        $controller->check_null_no($fetched_data['weight']) && $tmpDetail[] = array("title" => "تعداد در کارتن", "value" => $fetched_data['weight']);
    }
    $controller->check_null_no($fetched_data['count']) && $tmpDetail[] = array("title" => "تعداد کارتن", "value" => $fetched_data['count']);
    $controller->check_null_no($fetched_data['yolk_type']) && $tmpDetail[] = array("title" => "نوع زرده", "value" => $yolk_type[$fetched_data['yolk_type']]);
    $controller->check_null_no($fetched_data['print_type']) && $tmpDetail[] = array("title" => "نوع پرینت", "value" => $print_type[$fetched_data['print_type']]);
    $controller->check_null_no($fetched_data['box_type']) && $tmpDetail[] = array("title" => "نوع کارتن", "value" => $fetched_data['box_type']);
    $controller->check_null_no($fetched_data['stage_type']) && $tmpDetail[] = array("title" => "نوع شانه", "value" => $fetched_data['stage_type']);
    $controller->check_null_no($fetched_data['owner_name']) && $tmpDetail[] = array("title" => "نام مجموعه", "value" => $fetched_data['owner_name']);
    $controller->check_null_no($fetched_data['quality']) && $tmpDetail[] = array("title" => "کیفیت", "value" => $quality[$fetched_data['quality']]);
    $controller->check_null_no($fetched_data['price']) && $tmpDetail[] = array("title" => "قیمت", "value" => $fetched_data['price']);

    $fetched_data['details'] = $tmpDetail;
    unset(
        $fetched_data['weight'],
        $fetched_data['count'],
        $fetched_data['print_type'],
        $fetched_data['yolk_type'],
        $fetched_data['box_type'],
        $fetched_data['stage_type'],
        $fetched_data['pack_type'],
        $fetched_data['owner_name'],
        $fetched_data['quality'],
        $fetched_data['price']
    );


    if ($fetch_data->rowCount() > 0 and $access !== false) {
        $customer = $access;
        $values_string[] = "({$customer}, {$fetched_data['loadID']})";
        $insert = $controller->model->db->prepare("INSERT IGNORE INTO customers_seen_loads (customer, `load`) VALUES " . implode(',', $values_string));
        $insert->execute();
    }

    $data['load'] = $fetched_data;
    $status = 200;

} else {
    $status = 400;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

