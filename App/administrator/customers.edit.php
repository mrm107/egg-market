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
$controller->view->view_title = 'ویرایش مشتری';
$controller->checker_page_access(85);
$errors = '';
if (!isset($_REQUEST['edit']) or (isset($_REQUEST['edit']) and intval($_REQUEST['edit']) == '')) {
    header('Location: customers.view.php');
    exit;
} else {
    $editid = intval($_REQUEST['edit']);
    $editdoc = $controller->model->db->prepare("SELECT * FROM `customers` WHERE id = :id");
    $editdoc->execute(array(':id' => $_REQUEST['edit']));
    if ($editdoc->rowCount() == 0) {
        header('Location: customers.view.php');
        exit;
    } else
        $editdoc = $editdoc->fetch();
}
if (isset($_POST['submit'])) {
    try {

        $statement = $controller->model->db->prepare("UPDATE `customers` SET `mobile` =  :mobile, `name` = :name, `description` = :desc, `owner_name` = :owner_name, `person_owner_name` = :person_owner_name, `phone1` = :phone1, `phone2` = :phone2, `phone3` = :phone3, `phone4` = :phone4 WHERE `id` = :id ");
        $statement->execute(array(
            ":mobile" => utils__fatoen_numbers($_POST['mobile']),
            ":name" => utils__arabic_character_to_persian($_POST['name']),
            ":desc" => utils__arabic_character_to_persian($_POST['desc']),
            ":owner_name" => utils__arabic_character_to_persian($_POST['owner_name']),
            ":person_owner_name" => utils__arabic_character_to_persian($_POST['person_owner_name']),
            ":phone1" => utils__fatoen_numbers($_POST['phone-1']),
            ":phone2" => utils__fatoen_numbers($_POST['phone-2']),
            ":phone3" => utils__fatoen_numbers($_POST['phone-3']),
            ":phone4" => utils__fatoen_numbers($_POST['phone-4']),
            ':id' => $editid));

        $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            $errors = '<span class="bad-alert">شماره موبایل تکراری!</span>';
        } else {
            $show_error = $e->getMessage();
            $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
        }
    }
}
if (!isset($_REQUEST['edit']) or (isset($_REQUEST['edit']) and intval($_REQUEST['edit']) == '')) {
    header('Location: customers.view.php');
    exit;
} else {
    $editid = intval($_REQUEST['edit']);
    $editdoc = $controller->model->db->prepare("SELECT * FROM `customers` WHERE id = :id");
    $editdoc->execute(array(':id' => $_REQUEST['edit']));
    if ($editdoc->rowCount() == 0) {
        header('Location: customers.view.php');
        exit;
    } else
        $editdoc = $editdoc->fetch();
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='customers.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='مشتریان -> ویرایش مشتری'>ویرایش مشتری {$errors}</span>";

$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
			<div>
				<h6>شماره موبایل</h6>
				<input autocomplete='off' type='text' name='mobile' pattern='09([0-9][0-9])-?[0-9]{3}-?[0-9]{4}' style='text-align: center; direction: ltr;' value='{$editdoc['mobile']}' required/>
			</div>
            <div>
			    <h6>نام مشتری</h6>
			    <input autocomplete='off' type='text' name='name' value='{$editdoc['name']}' required>    
            </div>
            <!--<div>
			    <h6>نوع مشتری</h6>
			    <select name='type' required>
			        <option value='driver' ".($editdoc['type'] == 'driver' ? 'selected' : '').">راننده</option>
			        <option value='owner' ".($editdoc['type'] == 'owner' ? 'selected' : '').">صاحب بار</option>
                </select>    
            </div>-->	
            <div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6></div>
            <div style='clear: right;'>
                <h6>نام مجموعه</h6>
                <input type='text' name='owner_name' value='{$editdoc['owner_name']}'>
            </div>
            <div>
                <h6>نام شخص</h6>
                <input type='text' name='person_owner_name' value='{$editdoc['person_owner_name']}'>
            </div>";
    for ($i = 1; $i <= 4; $i++) {
        $controller->view_content_beta .= "<div><h6>شماره تماس {$i}</h6><input type='text' value='{$editdoc['phone'.$i]}' name='phone-{$i}'></div>";
    }
    $controller->view_content_beta .= "
            <div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6></div>
			<div style='clear:right;'>
			    <h6>توضیحات</h6>
			    <textarea name='desc'>{$editdoc['description']}</textarea>    
            </div>";
$controller->view_content_beta .= "
			<input type='hidden' name='edit' value='{$editdoc[0]}' />
			<div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ویرایش' /></div>
		</div>
		</form>
	</div>";

$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>