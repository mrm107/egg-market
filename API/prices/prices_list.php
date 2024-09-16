<?php
// require('../Plugin/jdatetime.inc.php');

$data = [];

//@$token = apache_request_headers()['Authorization'];
//$access = check_token($token);
//if ($access !== false) {
//    $customer = $access;
//

$date = $controller->check_null($address[2]);

if(!isset($date) || $date > 0) {
    $date = 0;
} else {
    $date = $date * -1;
}

$fetch_data = $controller->model->db->prepare("SELECT id, title, description FROM prices_resources ORDER BY `sort` ASC, `title` ASC");
$fetch_data->execute();
$fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);
$data['resources'] = $fetched_data;

$fetch_data = $controller->model->db->prepare("SELECT id, title FROM prices_cities ORDER BY `sort` ASC, `title` ASC");
$fetch_data->execute();
$fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);
$data['cities'] = $fetched_data;

$fetch_data = $controller->model->db->prepare("SELECT id, title FROM prices_weights ORDER BY `sort` ASC, `title` ASC");
$fetch_data->execute();
$fetched_data = $fetch_data->fetchAll(PDO::FETCH_ASSOC);
$data['weights'] = $fetched_data;

$prices_list = $controller->model->db->prepare("select date, type, type_id, FORMAT(price_1, 0) as price_1, FORMAT(price_2, 0) as price_2, FORMAT(price_final, 0) as price_final, resource from prices_list where date=CURRENT_DATE - INTERVAL :date DAY");
$prices_list->execute(array(":date" => $date));
$prices_list = $prices_list->fetchAll(PDO::FETCH_ASSOC);
$data['price_list'] = $prices_list;

$list_title_date = date('Y-m-d', strtotime('-'.$date.' days'));
$list_title_date = $controller->sdate->date("l، j F Y", strtotime($list_title_date));
$data['list_title'] = $list_title_date;

$status = 200;
//
//} else {
//    $status = 403;
//}

$export['status'] = $status;
$export['data'] = $data;

return $export;

