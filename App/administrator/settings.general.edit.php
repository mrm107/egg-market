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
$controller->view->view_title = 'تنظیمات عمومی';
$controller->checker_page_access(7);
$errors = '';

if (isset($_POST['submit'])) {
    try {
        $statement = $controller->model->db->prepare("UPDATE `setting` SET `value` = :name WHERE `id` = 1 ;");
        $statement->execute(array(':name' => $_POST['name']));

        $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
    } catch (PDOException $e) {
        $show_error = $e->getMessage();
        $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
    }
}

$controller->set_sidebar();
$controller->view->view_nav = $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='تنظیمات -> عمومی'>تنظیمات عمومی {$errors}</span>";

$settings = $controller->model->fetch_all("setting", "order by id ASC");

$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
			<!--<div>
				<h6>شعار مجموعه</h6>
				<input type='text' name='name' value='{$settings[0][2]}' required/>
			</div>	
			<div style='clear: right;'><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ویرایش' /></div>-->
		</div>
		</form>
	</div>";

$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>