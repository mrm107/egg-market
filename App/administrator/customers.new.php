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
$controller->view->view_title = 'مشتری جدید';
$controller->checker_page_access(85);
$errors = '';
if (isset($_POST['submit'])) {
    try {

        $statement = $controller->model->db->prepare("INSERT INTO `customers` (`mobile`, `name`, `reg_date`, `reg_client`, `description`, `status`, `registrar`, `owner_name`, `person_owner_name`, `phone1`, `phone2`, `phone3`, `phone4`) VALUES (:mobile, :name, :date, :client, :desc, :status, :reg, :owner_name, :person_owner_name, :phone1, :phone2, :phone3, :phone4)");
        $statement->execute(array(
            ":mobile" => utils__fatoen_numbers($_POST['mobile']),
            ":name" => utils__arabic_character_to_persian($_POST['name']),
            ":date" => $controller->datetime,
            ":client" => "core",
            ":desc" => utils__arabic_character_to_persian($_POST['desc']),
            ":status" => "accepted",
            ":reg" => $controller->model->user_code,
            ":owner_name" => utils__arabic_character_to_persian($_POST['owner_name']),
            ":person_owner_name" => utils__arabic_character_to_persian($_POST['person_owner_name']),
            ":phone1" => utils__fatoen_numbers($_POST['phone-1']),
            ":phone2" => utils__fatoen_numbers($_POST['phone-2']),
            ":phone3" => utils__fatoen_numbers($_POST['phone-3']),
            ":phone4" => utils__fatoen_numbers($_POST['phone-4'])
        ));

        $customer = $controller->model->db->lastInsertId();

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
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='customers.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='مشتریان -> مشتری جدید'>مشتری جدید {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
			<div style='clear: right;'>
				<h6>شماره موبایل</h6>
				<input autocomplete='off' type='text' name='mobile' pattern='09([0-9][0-9])-?[0-9]{3}-?[0-9]{4}' style='text-align: center; direction: ltr;' required/>
			</div>
			<div>
			    <h6>نام مشتری</h6>
			    <input autocomplete='off' type='text' name='name' required>    
            </div>	
            <!--<div>
			    <h6>نوع مشتری</h6>
			    <select name='type' required>
			        <option selected></option>
			        <option value='driver'>راننده</option>
			        <option value='owner'>صاحب بار</option>
                </select>    
            </div>-->	
            <div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6></div>
            <div style='clear:right'>
                <h6>نام مجموعه</h6>
                <input type='text' name='owner_name'>
            </div>
            <div>
                <h6>نام شخص</h6>
                <input type='text' name='person_owner_name'>
            </div>";
            for ($i = 1; $i <= 4; $i++) {
                $controller->view_content_beta .= "<div><h6>شماره تماس {$i}</h6><input type='text' name='phone-{$i}'></div>";
            }
            $controller->view_content_beta .= "   
                <div><h6>&nbsp;</h6></div>
                <div><h6>&nbsp;</h6></div>
                <div style='clear: right;'>
                    <h6>توضیحات</h6>
                    <textarea name='desc'></textarea>    
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