<?php
$data = [];

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

$count_limit = 12;

if (isset($_POST['origins'])) {

    @$token = apache_request_headers()['Authorization'];
    $access = check_token($token);

    $origins = array_filter($_POST['origins']);
    $types = array_filter($_POST['types']);
    $pack_types = array_filter($_POST['pack_types']);
    $yolk_types = array_filter($_POST['yolk_types']);
    $print_types = array_filter($_POST['print_types']);
    $qualities = array_filter($_POST['qualities']);

    if (isset($_POST['limit']) and $controller->check_null($_POST['limit']) != NULL and intval($controller->check_null($_POST['limit'])) <= 30) {
        $limit = intval($controller->check_null($_POST['limit']));
        $limit_query = " limit {$limit} ";
    } else {
        $limit_query = " limit {$count_limit} ";
    }

    if (isset($_POST['lastID']) and $controller->check_null($_POST['lastID']) != NULL) {
        $lastID = $controller->check_null($_POST['lastID']);
        $limit_condition = " and id < :lastID ";
        $limit_data = array(":lastID" => $lastID);
    } else {
        $limit_condition = '';
        $limit_data = [];
    }

    if (count($origins) > 0) {
        $keys = [];
        $values = [];
        for ($i = 0; $i < count($origins); $i++) {
            $key = ":origin" . $i;
            $keys[] = $key;
            $values[$key] = $origins[$i];
        }
        $origin_query = "and origin_field1 in (" . implode(',', $keys) . ")";
        $origin_data = $values;
    } else {
        $origin_query = '';
        $origin_data = [];
    }

    if (count($types) > 0) {
        // $keys = [];
        // $values = [];
        // for ($i = 0; $i < count($types); $i++) {
        //     $key = ":type" . $i;
        //     $keys[] = $key;
        //     $values[$key] = $types[$i];
        // }
        // $type_query = "and type in (" . implode(',', $keys) . ")";
        // $type_data = $values;

        $type_query = "and `type` = :type";
        $type_data = array(":type" => $types[0]);
    } else {
        $type_query = '';
        $type_data = [];
    }

    if (count($pack_types) > 0) {
        $keys = [];
        $values = [];
        for ($i = 0; $i < count($pack_types); $i++) {
            $key = ":pack_type" . $i;
            $keys[] = $key;
            $values[$key] = $pack_types[$i];
        }
        $pack_type_query = "and pack_type in (" . implode(',', $keys) . ")";
        $pack_type_data = $values;
    } else {
        $pack_type_query = '';
        $pack_type_data = [];
    }

    if (count($yolk_types) > 0) {
        $keys = [];
        $values = [];
        for ($i = 0; $i < count($yolk_types); $i++) {
            $key = ":yolk_type" . $i;
            $keys[] = $key;
            $values[$key] = $yolk_types[$i];
        }
        $yolk_type_query = "and yolk_type in (" . implode(',', $keys) . ")";
        $yolk_type_data = $values;
    } else {
        $yolk_type_query = '';
        $yolk_type_data = [];
    }

    if (count($print_types) > 0) {
        $keys = [];
        $values = [];
        for ($i = 0; $i < count($print_types); $i++) {
            $key = ":print_type" . $i;
            $keys[] = $key;
            $values[$key] = $print_types[$i];
        }
        $print_type_query = "and print_type in (" . implode(',', $keys) . ")";
        $print_type_data = $values;
    } else {
        $print_type_query = '';
        $print_type_data = [];
    }

    if (count($qualities) > 0) {
        $keys = [];
        $values = [];
        for ($i = 0; $i < count($qualities); $i++) {
            $key = ":quality" . $i;
            $keys[] = $key;
            $values[$key] = $qualities[$i];
        }
        $quality_query = "and quality in (" . implode(',', $keys) . ")";
        $quality_data = $values;
    } else {
        $quality_query = '';
        $quality_data = [];
    }


    $fetch_data = $controller->model->db->prepare("select id AS loadID,origin_field1,origin_field2,reg_date,UNIX_TIMESTAMP(reg_date) AS reg_date_timestamp, DATE(reg_date) as reg_just_date,description, weight, count, print_type, yolk_type, box_type, stage_type, type, pack_type, owner_name, status, price, quality from loads where 1 = 1 {$origin_query} {$type_query} {$pack_type_query} {$yolk_type_query} {$print_type_query} {$quality_query} {$limit_condition} and (status = 'accepted' || status = 'sold' || status = 'expired') order by `id` DESC {$limit_query}");
    $fetch_data->execute(array_merge($origin_data,$type_data, $pack_type_data, $yolk_type_data, $print_type_data, $quality_data , $limit_data));
    $fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);

    if ($fetch_data->rowCount() > 0) {
        $values_string = [];
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
            $controller->check_null_no($item['quality']) && $tmpDetail[] = array("title" => "کیفیت", "value" => $quality[$item['quality']]);
            $controller->check_null_no($item['price']) && $tmpDetail[] = array("title" => "قیمت", "value" => $item['price']);

            $fetched_data[$index]['details'] = $tmpDetail;
            unset(
                $fetched_data[$index]['weight'],
                $fetched_data[$index]['count'],
                $fetched_data[$index]['print_type'],
                $fetched_data[$index]['yolk_type'],
                $fetched_data[$index]['box_type'],
                $fetched_data[$index]['stage_type'],
                $fetched_data[$index]['pack_type'],
                $fetched_data[$index]['owner_name'],
                $fetched_data[$index]['quality'],
                $fetched_data[$index]['price']
            );

            if ($access !== false) {
                $values_string[] = "({$access}, {$item['loadID']})";
            }
        }
        if ($access !== false) {
            $insert = $controller->model->db->prepare("INSERT IGNORE INTO customers_seen_loads (customer, `load`) VALUES " . implode(',', $values_string));
            $insert->execute();
        }
    }

    $data['loads'] = $fetched_data;
    $status = 200;

} else {
    $status = 400;
}

$export['status'] = $status;
$export['data'] = $data;

return $export;

