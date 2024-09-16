<?php
ob_start();
session_start();
require('../../Inc/config.inc.php');
require('../../Controller/admin.controller.php');
require('../../Model/model.php');
require('../../View/view.php');
require('../../Plugin/jdatetime.inc.php');
$controller = new controller;
$controller->checker_session_responsibility('valid_user');
$controller->view->view_title = 'خطا !ً';
$controller->set_sidebar();
$controller->view->view_nav = $controller->set_fastaccess();
$controller->view->view_box_header = "<span>خطا !</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='content'>
		<div class='block-error'>
			حساب کاربری شما بسته است !<br/>
			<span style='font-family:myriadpro; font-size:15px;'>! Your Account is Blocked</span>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>