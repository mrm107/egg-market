<?php

ob_start();
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
//        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
} else {
    $rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);
}

require('../Inc/config.inc.php');
require('../Controller/admin.controller.php');
require('../Model/model.php');
require('../View/view.php');
require('../Plugin/jdatetime.inc.php');
require('../Util/sms-manager.php');
require('../Util/generate_string.php');
require('../Util/persian-utils.php');
require('check_token.php');

$controller = new controller;

//header('Content-Type: application/json');
$export['status'] = 404;
$export['data'] = [];

$address = explode("/API/", $_SERVER['REQUEST_URI']);
if ($controller->check_null_no($address[1]) != NULL) {
    $address = array_filter(explode('/', $address[1]));
    if ($address[0]=='paymethods'){
        switch (@$address[1]) {
            case 'pay':
                $export = include('paymethods/pay.php');
                break;
            case 'confirm':
                $export = include('paymethods/confirm.php');
                break;
            default:
                $export = include('paymethods/list.php');
                break;
        }
    }
    elseif ($address[0] == 'customers') {
        switch ($address[1]) {
            case 'check_number':
                $export = include('customers/check_number.php');
                break;
            case 'confirm':
                $export = include('customers/confirm.php');
                break;
            case 'renew_confirm_code':
                $export = include('customers/renew_confirm_code.php');
                break;
            case 'fill':
                $export = include('customers/fill.php');
                break;
            case 'logout':
                $export = include('customers/logout.php');
                break;
            case 'profile':
                $export = include('customers/profile.php');
                break;
        }
    } elseif ($address[0] == 'locations') {
        switch ($address[1]) {
            case 'provinces':
                $export = include('locations/provinces.php');
                break;
            case 'area_suggestion':
                $export = include('locations/area_suggestion.php');
                break;
        }
    } elseif ($address[0] == 'loads') {
        switch ($address[1]) {
            case 'loads':
                $export = include('loads/loads.php');
                break;
            case 'load':
                $export = include('loads/load.php');
                break;
            case 'get_contact':
                $export = include('loads/get_contact.php');
                break;
            case 'bookmark_set':
                $export = include('loads/bookmark_set.php');
                break;
            case 'bookmark_remove':
                $export = include('loads/bookmark_remove.php');
                break;
            case 'bookmark_get':
                $export = include('loads/bookmark_get.php');
                break;
            case 'my_loads':
                $export = include('loads/my_loads.php');
                break;
            case 'add':
                $export = include('loads/load_add.php');
                break;
            case 'edit':
                $export = include('loads/load_edit.php');
                break;
            case 'delete':
                $export = include('loads/load_delete.php');
                break;
            case 'stats':
                $export = include('loads/load_stats.php');
                break;
            case 'offer':
                $export = include('loads/load_offer.php');
                break;
        }
    } elseif ($address[0] == 'others') {
        switch ($address[1]) {
            case 'server_time':
                $export = include('others/server_time.php');
                break;
            case 'invite':
                $export = include('others/invite.php');
                break;
        }
    } elseif ($address[0] == 'prices') {
        switch ($address[1]) {
            case 'list':
                $export = include('prices/prices_list.php');
                break;
        }
    }

    echo json_encode($export['data']);
    http_response_code($export['status']);
} else {
    echo "EggMarket, EggMarket is every where ...";
}