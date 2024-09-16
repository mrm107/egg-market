<?php
//In The Name Of Allah
//@author : Hassan Zanjani
//@author contact : hassanzanjani1374@gmail.com , +989191515145
//CopyRight 2014 @ Hassan Zanjani
ob_start();
session_start();
require('../../Inc/config.inc.php');
require('../../Controller/admin.controller.php');
require('../../Model/model.php');
require('../../View/view.php');
require('../../Plugin/jdatetime.inc.php');
$controller = new controller;
$controller->checker_session_responsibility('valid_user');
$controller->view->view_title = 'حساب کاربری';
$errors = '';
if (isset($_POST['submit'])) {
    if ($_POST['password'] == 'cancel') {
        try {
            $update = $controller->model->db->prepare("update `users` set `email` = :email where `id` = :id ");
            $update->execute(array(':email' => $_POST['email'],
                ':id' => $controller->model->user_code
            ));
            $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
        } catch (PDOException $e) {
            $show_error = $e->getMessage();
            $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
        }
    } else {
        if ($_POST['password'] == $_POST['password-verify']) {
            try {
                $update = $controller->model->db->prepare("update `users` set `email` = :email ,`password` = :password where `id` = :id ");
                $update->execute(array(':email' => $_POST['email'],
                    ':password' => openssl_digest($_POST['password'], 'sha512'),
                    ':id' => $controller->model->user_code
                ));
                $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
            } catch (PDOException $e) {
                $show_error = $e->getMessage();
                $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
            }
        } else {
            $errors = "<span class='bad-alert'>عدم تطابق رمز عبور و تایید رمز عبور !</span>";
        }
    }
}
$edituser = $controller->model->fetch_one("users", "where id = " . $controller->model->user_code . " ");
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span>حساب کاربری {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='content'>
		<div class='form-elements'>
			<div><h6>نام : </h6><input type='text' style='background-color:#FCFCFC;' value='{$edituser[1]}' readonly /></div>  
			<div><h6>نام خانوادگی : </h6><input type='text' style='background-color:#FCFCFC;' value='{$edituser[2]}' readonly /></div>                                                                      
			<div><h6>پست الکترونیکی : </h6><input type='email' name='email' value='{$edituser['email']}' required /></div>      
			<div style='clear: right;'><h6>رمز عبور : </h6><input type='password' name='password' value='cancel' required /></div> 
			<div><h6>تایید رمز عبور : </h6><input type='password' name='password-verify' value='cancel' required /></div> 
			<div><h6>&nbsp;</h6></div>
			<div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ویرایش' /></div>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>