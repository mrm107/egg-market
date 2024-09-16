<?php
$data = [];

if (isset($_POST['offerForLoad']) and isset($_POST['offerPrice']) and isset($_POST['offerDescription'])) {

    @$token = apache_request_headers()['Authorization'];
    $offerForLoad = $controller->check_null_no($_POST['offerForLoad']);
    $offerPrice = $controller->check_null_no($_POST['offerPrice']);
    $offerDescription = $controller->check_null_no($_POST['offerDescription']);

    $access = check_token($token);
    if ($access !== false) {
        $customer = $access;

        $fetch_data = $controller->model->db->prepare("SELECT id FROM loads WHERE status = 'accepted' AND id = :load limit 1");
        $fetch_data->execute(array(":load" => $offerForLoad));
        if ($fetch_data->rowCount() > 0) {


            $text = "
                <br/>
                بار: <a href='http://eggmarket.ir/l/{$offerForLoad}' target='_blank'>بار کد {$offerForLoad}</a> <br/>
                مشتری: <a href='http://core.eggmarket.ir/App/administrator/customers.edit.php?edit={$customer}' target='_blank'>مشتری کد {$customer}</a> <br/>
                قیمت پیشنهادی: {$offerPrice} <br/>
                توضیحات: {$offerDescription} <br/>
            ";

            $statement = $controller->model->db->prepare("INSERT INTO messages (`type`,`title`,`text`,`date`,`receiver`,`sender`,`status`,`mother_message`) VALUES ('inner',:title,:text,'" . $controller->datetime . "',:code,'1','unread','no')");
            $statement->execute(array(
                ':title' => 'پیشنهاد قیمت',
                ':text' => $text,
                ':code' => 32
            ));

            $status = 200;
        } else {
            $status = 404;
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

