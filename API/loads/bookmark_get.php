<?php
$data = [];

@$token = apache_request_headers()['Authorization'];

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

$access = check_token($token);
if ($access !== false) {
    $customer = $access;

    if(isset($address[2]) and $address[2] == 'short') {
        $bookmarks = $controller->model->db->prepare("SELECT loads.id AS loadID FROM customers_bookmarked_loads INNER JOIN loads ON customers_bookmarked_loads.load = loads.id WHERE loads.status = 'accepted' AND customers_bookmarked_loads.customer = :customer ORDER BY customers_bookmarked_loads.id DESC");
        $bookmarks->execute(array_merge([":customer" => $customer]));
        $fetched_data = $bookmarks->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $bookmarks = $controller->model->db->prepare("SELECT customers_bookmarked_loads.id AS bookmarkID,loads.id AS loadID,origin_field1,origin_field2,reg_date,UNIX_TIMESTAMP(reg_date) AS reg_date_timestamp,description, weight, count, print_type, yolk_type, box_type, stage_type, type, pack_type, owner_name, status FROM customers_bookmarked_loads INNER JOIN loads ON customers_bookmarked_loads.load = loads.id WHERE loads.status = 'accepted' AND customers_bookmarked_loads.customer = :customer ORDER BY customers_bookmarked_loads.id DESC");
        $bookmarks->execute(array_merge([":customer" => $customer]));
        $fetched_data = $bookmarks->fetchAll(PDO::FETCH_ASSOC);

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
            $controller->check_null_no($item['print_type']) && $tmpDetail[] = array("title" => "نوع پرینت", "value" => $print_type[$item['print_type']]);
            $controller->check_null_no($item['box_type']) && $tmpDetail[] = array("title" => "نوع کارتن", "value" => $item['box_type']);
            $controller->check_null_no($item['stage_type']) && $tmpDetail[] = array("title" => "نوع شانه", "value" => $item['stage_type']);
            $controller->check_null_no($item['owner_name']) && $tmpDetail[] = array("title" => "نام مجموعه", "value" => $item['owner_name']);

            $fetched_data[$index]['details'] = $tmpDetail;
            unset(
                $fetched_data[$index]['weight'],
                $fetched_data[$index]['count'],
                $fetched_data[$index]['print_type'],
                $fetched_data[$index]['yolk_type'],
                $fetched_data[$index]['box_type'],
                $fetched_data[$index]['stage_type'],
                $fetched_data[$index]['pack_type'],
                $fetched_data[$index]['owner_name']
            );
        }
    }

    $data['bookmarks'] = $fetched_data;
    $status = 200;

} else {
    $status = 403;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

