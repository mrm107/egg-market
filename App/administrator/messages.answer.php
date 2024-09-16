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
$controller->view->view_title = 'پاسخ به پیام';
$errors = '';
if (!isset($_POST['edit']) or (isset($_POST['edit']) and intval($_POST['edit']) == '')) {
    header('Location: messages.view.php');
    exit;
} else {
    $editid = intval($_POST['edit']);
    $editmessage = $controller->model->db->prepare("select * from messages where id = :id");
    $editmessage->execute(array(':id' => $_POST['edit']));
    if ($editmessage->rowCount() == 0) {
        header('Location: messages.view.php');
        exit;
    } else {
        $editmessage = $editmessage->fetch();
        if ($editmessage[5] != $controller->model->user_code) {
            header('Location: messages.view.php');
            exit;
        }
    }
}
if (isset($_GET['status'])) {
    $status = $_GET['status'];
} else
    $status = NULL;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
} else
    $search = NULL;
if ($search != NULL and $status != NULL)
    $back = "?status=$status&search=$search";
elseif ($search == NULL and $status != NULL)
    $back = "?status=$status";
elseif ($search != NULL and $status == NULL)
    $back = "?search=$search";
else
    $back = '';

if (isset($_POST['submit'])) {
    $statement = $controller->model->db->prepare("insert into messages (`type`,`title`,`text`,`date`,`receiver`,`sender`,`status`,`mother_message`) values ('inner',:title,:text,'" . $controller->datetime . "',:code,'" . $controller->model->user_code . "','unread',:id)");
    $statement->execute(array(':title' => $editmessage[2],
        ':text' => $_POST['text'],
        ':code' => $editmessage[6],
        ':id' => $editmessage['mother_message']));
    $statement = $controller->model->db->prepare("update messages set `status` = 'answerd' where id = :code");
    $statement->execute(array(':code' => $editid));
    $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
}
if ($editmessage['status'] == 'unread') {
    $statement = $controller->model->db->prepare("update messages set `status` = 'read' where id = :code");
    $statement->execute(array(':code' => $editid));
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='messages.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='پیام ها -> پاسخ به پیام'>پاسخ به پیام {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='content'>
		<div class='form-elements'>
			<div>
				<h6>عنوان پیام<a class='necessary-complete'>*</a></h6>
				<input type='text' name='title' style='background-color:#fcfcfc;' value='پاسخ به \"{$editmessage[2]}\"' readonly required />
			</div>
			<div>
				<h6>متن پیام </h6>
				<textarea required readonly style='background-color:#fcfcfc;'>{$editmessage[3]}</textarea>
			</div>
						<div>
				<h6>گیرنده <a class='necessary-complete'>*</a></h6>";
if ($editmessage[1] == 'inner') {
    $receivers = $controller->model->fetch_one('users', "where id = '$editmessage[6]'");
    $controller->view_content_beta .= "<input type='text' name='receiver' style='background-color:#fcfcfc;' value='{$receivers[1]} {$receivers[2]}' readonly />";
} else {
    $controller->view_content_beta .= "<textarea style='background-color:#fcfcfc;' readonly>{$editmessage[7]}\n{$editmessage[8]}\n{$editmessage[9]}</textarea>";
}
$controller->view_content_beta .= "
			</div>
			<div>
				<h6>پاسخ<a class='necessary-complete'>*</a></h6>
				<textarea name='text' required></textarea>
			</div>			
			<div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6>
				<input type='submit' class='submit' name='submit' value='افزودن' />
				<input type='hidden' name='edit' value='{$editmessage[0]}' />
			</div>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>