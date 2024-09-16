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
$controller->view->view_title = 'استان جدید';
$controller->checker_page_access(94);
$errors = '';
if (isset($_POST['submit'])) {
    try {

        $statement = $controller->model->db->prepare("INSERT INTO `locations_provinces` (`title`) VALUES (:title)");
        $statement->execute(array(
            ":title" => utils__arabic_character_to_persian($_POST['title'])
        ));

        $customer = $controller->model->db->lastInsertId();

        $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            $errors = '<span class="bad-alert">عنوان تکراری!</span>';
        } else {
            $show_error = $e->getMessage();
            $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
        }
    }
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='locations.provinces.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='مکان ها -> استان جدید'>استان جدید {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
			<div>
			    <h6>عنوان استان</h6>
			    <input autocomplete='off' type='text' name='title' required>    
            </div>	
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='افزودن' /></div>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>