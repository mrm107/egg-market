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
$controller->view->view_title = 'ویرایش کاربر';
$controller->checker_page_access(2);
$errors = '';
if (!isset($_POST['edit']) or (isset($_POST['edit']) and intval($_POST['edit']) == '')) {
    header('Location: users.view.php');
    exit;
} else {
    $editid = intval($_POST['edit']);
    $editdoc = $controller->model->db->prepare("SELECT * FROM `users` WHERE id = :id");
    $editdoc->execute(array(':id' => $_POST['edit']));
    if ($editdoc->rowCount() == 0) {
        header('Location: users.view.php');
        exit;
    } else
        $editdoc = $editdoc->fetch();
    if ($controller->model->user_respon != 'admin' and $editdoc['responsibility'] == 'admin') {
        header('Location: users.view.php');
        exit;
    }
}
if (isset($_POST['submit'])) {

    if ($controller->model->user_respon != 'admin' and $_POST['respon'] == 'admin') {
        $errors = "<span class='bad-alert'>عدم دسترسی !</span>";
    } elseif ($_POST['password-conf'] == $_POST['password']) {
        $_POST['email'] = $controller->check_null_no($_POST['email']);
        if ($_POST['respon'] != 'cashier')
            $_POST['cash'] = NULL;

        try {
            if ($_POST['password'] == 'cancel') {
                $statement = $controller->model->db->prepare("UPDATE `users` SET `firstname` = :first, `lastname` = :last, `responsibility` = :respon, `email` = :email, `cach_access` = :cash, `status` = :status WHERE `id` = :id ");
                $statement->execute(array(':first' => $_POST['firstname'],
                    ':last' => $_POST['lastname'],
                    ':respon' => $_POST['respon'],
                    ':email' => $_POST['email'],
                    ':cash' => $_POST['cash'],
                    ':status' => $_POST['status'],
                    ':id' => $editid));
            } else {
                $statement = $controller->model->db->prepare("UPDATE `users` SET `firstname` = :first, `lastname` = :last, `responsibility` = :respon, `email` = :email, `password` = :pass, `cach_access` = :cash, `status` = :status WHERE `id` = :id ");
                $statement->execute(array(':first' => $_POST['firstname'],
                    ':last' => $_POST['lastname'],
                    ':respon' => $_POST['respon'],
                    ':email' => $_POST['email'],
                    ':pass' => openssl_digest($_POST['password'], 'sha512'),
                    ':cash' => $_POST['cash'],
                    ':status' => $_POST['status'],
                    ':id' => $editid));
            }
            $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
        } catch (PDOException $e) {
            $show_error = $e->getMessage();
            $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
        }
    } else {
        $errors = "<span class='bad-alert'>عدم تطابق رمز عبور و تایید آن !</span>";
    }
}
if (!isset($_POST['edit']) or (isset($_POST['edit']) and intval($_POST['edit']) == '')) {
    header('Location: users.view.php');
    exit;
} else {
    $editid = intval($_POST['edit']);
    $editdoc = $controller->model->db->prepare("SELECT * FROM `users` WHERE id = :id");
    $editdoc->execute(array(':id' => $_POST['edit']));
    if ($editdoc->rowCount() == 0) {
        header('Location: users.view.php');
        exit;
    } else
        $editdoc = $editdoc->fetch();
    if ($controller->model->user_respon != 'admin' and $editdoc['responsibility'] == 'admin') {
        header('Location: users.view.php');
        exit;
    }
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='users.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='کاربران -> ویرایش کاربر'>ویرایش کاربر {$errors}</span>";

$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
			<div style='clear:right;'>
				<h6>نام</h6>
				<input type='text' name='firstname' value='{$editdoc[1]}' required/>
			</div>
			<div>
				<h6>نام خانوادگی</h6>
				<input type='text' name='lastname' value='{$editdoc[2]}' required/>
			</div>				
			<div style='clear:right;'>
				<h6>وظیفه</h6>
				<select name='respon' id='users-new-respon' required>";
$respon = $controller->model->fetch_All('responsibilities', "where name <> 'customer' order by `tag` ASC");
foreach ($respon as $key) {
    if ($editdoc['responsibility'] == $key[0])
        $controller->view_content_beta .= "<option value='{$key[0]}' selected>{$key[1]}</option>";
    else
        $controller->view_content_beta .= "<option value='{$key[0]}'>{$key[1]}</option>";
}
$controller->view_content_beta .= "
				</select>
			</div>					
			<div id='cashier'>";
if ($editdoc['responsibility'] == 'cashier') {
    $controller->view_content_beta .= "<h6>صندوق </h6>";
    $controller->view_content_beta .= "<select name='cash' required>";
    $cashs = $controller->model->fetch_all('cashs', 'where status <> "deleted" order by `tag` ASC');
    foreach ($cashs as $key) {
        if ($editdoc['cach_access'] == $key[0])
            $controller->view_content_beta .= "<option value='{$key[0]}' selected>{$key[1]}</option>";
        else
            $controller->view_content_beta .= "<option value='{$key[0]}'>{$key[1]}</option>";
    }
    $controller->view_content_beta .= "</select>";
}
$controller->view_content_beta .= "
			</div>			
			<div style='clear: right'>
				<h6>پست الکترونیکی</h6>
				<input type='email' autocomplete='off' value='{$editdoc['email']}' name='email' required/>
			</div>		
			<div style='clear: right;'>
				<h6>وضعیت</h6>
				<select name='status'>";
if ($editdoc['status'] == 'active') {
    $controller->view_content_beta .= "<option value='active' selected>فعال</option>";
} else {
    $controller->view_content_beta .= "<option value='active'>فعال</option>";
}
if ($editdoc['status'] == 'deactive') {
    $controller->view_content_beta .= "<option value='deactive' selected>غیرفعال</option>";
} else {
    $controller->view_content_beta .= "<option value='deactive'>غیرفعال</option>";
}
$controller->view_content_beta .= "</select>
			</div>					
			<div style='clear:right;'>
				<h6>رمز عبور</h6>
				<input type='password' name='password' value='cancel' required/>
			</div>				
			<div>
				<h6>تایید رمز عبور</h6>
				<input type='password' name='password-conf' value='cancel' required/>
			</div>				
			<div><h6>&nbsp;</h6></div>
			<input type='hidden' name='edit' value='{$editdoc[0]}' />
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ویرایش' /></div>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>