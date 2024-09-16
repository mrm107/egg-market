<?php
//In The Name Of Allah
//@author : Hassan Zanjani
//@author contact : hassanzanjani1374@gmail.com , +989191515145
//CopyRight 2013 @ Hassan Zanjani
ob_start();
session_start();
require('../../Inc/config.inc.php');
require('../../Controller/admin.controller.php');
require('../../Model/model.php');
require('../../View/view.php');
require('../../Plugin/jdatetime.inc.php');
$controller = new controller;
$controller->view_content_beta = '';
$controller->checker_session_responsibility('valid_user');
if (isset($_POST['typefile']) and $_POST['typefile'] == 'image') {
    $file = "uploadimagefile";
    $output_dir = "../../File/";
    $category = 'image';
    $filesize = 2;
    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'svg');
    $error_ctf = "یک تصویر انتخاب کنید .<input type='hidden' name='imagefinal' id='imagefinal' value='' /></div>";

    $address = $controller->upload_file($file, $output_dir, $category, $filesize, $allowed_ext, $error_ctf);
    if ($controller->file_error !== false)
        $controller->view_content_beta = $address;
    else
        $controller->view_content_beta = "<img src='../../File/" . $address . "' ><input type='hidden' name='imagefinal' id='imagefinal' value='" . $address . "' />";
}
$controller->view->view_content = $controller->view_content_beta;
$controller->view->show('../../Template/admin.ajax.php');
?>