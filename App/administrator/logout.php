<?php
ob_start();
session_start();
require('../../Inc/config.inc.php');
require('../../Controller/admin.controller.php');
require('../../Model/model.php');
require('../../View/view.php');
require('../../Plugin/jdatetime.inc.php');
$controller = new controller;
@$controller->model->checker_session($_SESSION['valid_user']);
@$update = $controller->model->db->prepare("update users set `session` = NULL where `session` = :id");
@$update->execute(array(":id" => $_SESSION['valid_user']));
session_unset();
header('Location: ../login.php');
?>