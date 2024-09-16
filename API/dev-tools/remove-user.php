<?php

ob_start();
session_start();

require('../../Inc/config.inc.php');
require('../../Controller/admin.controller.php');
require('../../Model/model.php');
require('../../View/view.php');
require('../../Plugin/jdatetime.inc.php');
require('../../Util/sms-manager.php');
require('../../Util/generate_string.php');
require('../../Util/persian-utils.php');
$controller = new controller;


$user_phone = $_REQUEST['number'];

if (in_array($user_phone, ['09307475837', '09191515145', '09353372018', '09217191069', '09353372018', '09358022517', '09107712937', '09198711159‬', '09356547668‬'])) {

    $getUser = $controller->model->db->prepare("SELECT * FROM customers WHERE mobile = :mobile limit 1");
    $getUser->execute(array(":mobile" => $user_phone));
    if ($getUser->rowCount() > 0) {
        $user = $getUser->fetch();

        $update = $controller->model->db->prepare("update loads set reg_customer = '768' where reg_customer = '{$user['id']}'");
        $update->execute();

        $remove = $controller->model->db->prepare("delete from customers_bookmarked_loads where customer = '{$user['id']}';delete from customers_seen_loads where customer = '{$user['id']}';delete from customers_tokens where customer = '{$user['id']}'; delete from customers where id = '{$user['id']}';");
        $remove->execute();

    }


}