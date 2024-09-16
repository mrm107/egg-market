<?php
//In The Name Of Allah
//@author : Hassan Zanjani
//@author contact : hassanzanjani1374@gmail.com , +989191515145
//CopyRight 2014 @ Hassan Zanjani

ob_start();
session_start();
require('../Inc/config.inc.php');
require('admin.controller.php');
require('../Model/model.php');
require('../View/view.php');
require('../Plugin/jdatetime.inc.php');
require('../Util/persian-utils.php');
$controller = new controller;
$controller->view_content_beta = '';
$controller->checker_session_responsibility('valid_user');
$controller->view_content_beta = '';

if (isset($_GET['notice-reloader'])) {
    //all pages -> notification reloader
    $message_count = $controller->model->get_count_db('`messages`', "`date` < '" . date('Y-m-d H:i:s') . "' and `receiver` = '{$controller->model->user_code}' and `status` = 'unread'");
    $all_count = $message_count;
    $dashboard['all-count'] = $all_count;
    $dashboard['messages-count'] = $message_count;
    if ($all_count == 0)
        $dashboard['title'] = "<i class='home icon'></i>پیشخوان";
    else
        $dashboard['title'] = "<i class='home icon'></i>پیشخوان<span class='custome-number-label'>{$all_count}</span>";
    $controller->view_content_beta = json_encode($dashboard);

}

$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../Template/admin.ajax.php');
?>