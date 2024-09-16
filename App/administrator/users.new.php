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
require('../../Util/persian-utils.php');
$controller = new controller;
$controller->checker_session_responsibility('valid_user');
$controller->view->view_title = 'کاربر جدید';
$controller->checker_page_access(2);
$errors = '';
if (isset($_POST['submit'])) {

    if ($controller->model->user_respon != 'admin' and $_POST['respon'] == 'admin') {
        $errors = "<span class='bad-alert'>عدم دسترسی !</span>";
    } elseif ($_POST['password-conf'] == $_POST['password']) {
        $_POST['email'] = $controller->check_null_no($_POST['email']);
        if ($_POST['respon'] != 'cashier')
            $_POST['cash'] = NULL;

        try {
            $statement = $controller->model->db->prepare("INSERT INTO users (`firstname`, `lastname`, `responsibility`, `email`, `password`, `fast_access`, `cach_access`, `status`) 
																		VALUES (:first, :last, :respon, :email, :pass, NULL, :cash, :status)");
            $statement->execute(array(
                ':first' => $_POST['firstname'],
                ':last' => $_POST['lastname'],
                ':respon' => $_POST['respon'],
                ':email' => $_POST['email'],
                ':pass' => openssl_digest($_POST['password'], 'sha512'),
                ':cash' => $_POST['cash'],
                ':status' => $_POST['status']));
            $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
        } catch (PDOException $e) {
            $show_error = $e->getMessage();
            $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
        }
    } else {
        $errors = "<span class='bad-alert'>عدم تطابق رمز عبور و تایید آن !</span>";
    }
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='users.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='کاربران -> کاربر جدید'>کاربر جدید {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
			<div style='clear:right;'>
				<h6>نام</h6>
				<input type='text' name='firstname' required/>
			</div>
			<div>
				<h6>نام خانوادگی</h6>
				<input type='text' name='lastname' required/>
			</div>				
			<div style='clear:right;'>
				<h6>وظیفه</h6>
				<select name='respon' id='users-new-respon' required>
					<option selected></option>";
$respon = $controller->model->fetch_All('responsibilities', "where name <> 'customer' order by `tag` ASC");
foreach ($respon as $key)
    $controller->view_content_beta .= "<option value='{$key[0]}'>{$key[1]}</option>";
$controller->view_content_beta .= "
				</select>
			</div>					
			<div id='cashier'></div>		
			<div style='clear: right'>
				<h6>پست الکترونیکی</h6>
				<input type='email' autocomplete='off' name='email' required/>
			</div>		
			<div style='clear: right;'>
				<h6>وضعیت</h6>
				<select name='status'>
					<option value='active'>فعال</option>
					<option value='deactive'>غیرفعال</option>
				</select>
			</div>						
			<div style='clear:right;'>
				<h6>رمز عبور</h6>
				<input type='password' name='password' required/>
			</div>				
			<div>
				<h6>تایید رمز عبور</h6>
				<input type='password' name='password-conf' required/>
			</div>				
			<div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='افزودن' /></div>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>