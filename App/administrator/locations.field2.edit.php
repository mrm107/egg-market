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
$controller->view->view_title = 'ویرایش مورد پیشنهادی';
$controller->checker_page_access(95);
$errors = '';
if (!isset($_REQUEST['edit']) or (isset($_REQUEST['edit']) and intval($_REQUEST['edit']) == '')) {
    header('Location: locations.field2.view.php');
    exit;
} else {
    $editid = intval($_REQUEST['edit']);
    $editdoc = $controller->model->db->prepare("SELECT * FROM `locations_field2` WHERE id = :id");
    $editdoc->execute(array(':id' => $_REQUEST['edit']));
    if ($editdoc->rowCount() == 0) {
        header('Location: locations.field2.view.php');
        exit;
    } else
        $editdoc = $editdoc->fetch();
}
if (isset($_POST['submit'])) {
    try {

        $statement = $controller->model->db->prepare("UPDATE `locations_field2` SET `title` = :title, `province` = :province WHERE `id` = :id ");
        $statement->execute(array(
            ":title" => utils__arabic_character_to_persian($_POST['title']),
            ":province" => $_POST['province'],
            ':id' => $editid));

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
if (!isset($_REQUEST['edit']) or (isset($_REQUEST['edit']) and intval($_REQUEST['edit']) == '')) {
    header('Location: locations.field2.view.php');
    exit;
} else {
    $editid = intval($_REQUEST['edit']);
    $editdoc = $controller->model->db->prepare("SELECT * FROM `locations_field2` WHERE id = :id");
    $editdoc->execute(array(':id' => $_REQUEST['edit']));
    if ($editdoc->rowCount() == 0) {
        header('Location: locations.field2.view.php');
        exit;
    } else
        $editdoc = $editdoc->fetch();
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='locations.field2.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='مکان ها -> ویرایش مورد پیشنهادی'>ویرایش مورد پیشنهادی {$errors}</span>";

$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
            <div>
			    <h6>عنوان</h6>
			    <input autocomplete='off' type='text' name='title' value='{$editdoc['title']}' required>    
            </div>
            <div>
                <h6>استان</h6>
                <select name='province' required>
                    <option selected></option>";
                    $provinces = $controller->model->fetch_all("locations_provinces", "order by title ASC");
                    foreach ($provinces as $province) {
                        if($editdoc['province'] == $province['id']) {
                            $controller->view_content_beta .= "<option value='{$province['id']}' selected>{$province['title']}</option>";
                        } else {
                            $controller->view_content_beta .= "<option value='{$province['id']}'>{$province['title']}</option>";
                        }
                    }
    $controller->view_content_beta .= "
                </select>
            </div>";
$controller->view_content_beta .= "
			<input type='hidden' name='edit' value='{$editdoc[0]}' />
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ویرایش' /></div>
		</div>
		</form>
	</div>";

$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>