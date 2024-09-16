<?php
$data = [];

$count_limit = 12;

$print_types = [
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

if (1 == 1) {

    @$token = apache_request_headers()['Authorization'];

    $limit_query = " limit {$count_limit} ";
    if (isset($_POST['lastID']) and $controller->check_null($_POST['lastID']) != NULL) {
        $lastID = $controller->check_null($_POST['lastID']);
        $limit_condition = " and id < :lastID ";
        $limit_data = array(":lastID" => $lastID);
    } else {
        $limit_condition = '';
        $limit_data = [];
    }

    $access = check_token($token);

    if ($access !== false) {
        $customer = $access;

        $fetch_data = $controller->model->db->prepare("select id as loadID,origin_field1,origin_field2,reg_date,UNIX_TIMESTAMP(reg_date) AS reg_date_timestamp,description, weight, count, print_type, yolk_type, box_type, stage_type, type, pack_type, owner_name, person_owner_name, status, price, quality, 
          (
            SELECT GROUP_CONCAT(phone) AS phones
            FROM loads_phones
            where `load` = loads.id 
            GROUP BY `load`
          ) AS phones from loads where 1 = 1 {$limit_condition} and reg_customer = :reg_customer order by `id` DESC {$limit_query}");
        $fetch_data->execute(array_merge($limit_data, [":reg_customer" => $customer]));
        $fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fetched_data as $index => $item) {

            $tmpDetail = [];
            $controller->check_null_no($item['pack_type']) && $tmpDetail[] = array("title" => "نوع بسته بندی", "value" => $pack_type[$item['pack_type']]);
            if($item['pack_type'] == 'bulk') {
                $controller->check_null_no($item['weight']) && $tmpDetail[] = array("title" => "وزن کارتن", "value" => $item['weight']);
            } else {
                $controller->check_null_no($item['weight']) && $tmpDetail[] = array("title" => "تعداد در کارتن", "value" => $item['weight']);
            }
            $controller->check_null_no($item['count']) && $tmpDetail[] = array("title" => "تعداد کارتن", "value" => $item['count']);
            $controller->check_null_no($item['yolk_type']) && $tmpDetail[] = array("title" => "نوع زرده", "value" => $yolk_type[$item['yolk_type']]);
            $controller->check_null_no($item['print_type']) && $tmpDetail[] = array("title" => "نوع پرینت", "value" => $print_types[$item['print_type']]);
            $controller->check_null_no($item['box_type']) && $tmpDetail[] = array("title" => "نوع کارتن", "value" => $item['box_type']);
            $controller->check_null_no($item['stage_type']) && $tmpDetail[] = array("title" => "نوع شانه", "value" => $item['stage_type']);
            $controller->check_null_no($item['quality']) && $tmpDetail[] = array("title" => "کیفیت", "value" => $quality[$item['quality']]);
            $controller->check_null_no($item['price']) && $tmpDetail[] = array("title" => "قیمت", "value" => $item['price']);

            $fetched_data[$index]['details'] = $tmpDetail;

            $fetched_data[$index]['phones'] = explode(',', $fetched_data[$index]['phones']);
        }

        $data['my_loads'] = $fetched_data;
        $status = 200;
    } else {
        $status = 403;
    }

} else {
    $status = 400;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

