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
$controller->view->view_title = 'ارسال پیام';
$errors = '';
if (isset($_POST['submit'])) {
    $statement = $controller->model->db->prepare("SELECT * FROM users WHERE id = :code AND status <> 'deleted'");
    $statement->execute(array(':code' => $_POST['receiver']));
    if ($statement->rowCount() == 0)
        return false;
    else {
        $row = $statement->fetch();
        $title = $_POST['title'];
        $text = $_POST['text'];
        $statement = $controller->model->db->prepare("INSERT INTO messages (`type`,`title`,`text`,`date`,`receiver`,`sender`,`status`,`mother_message`) VALUES ('inner',:title,:text,'" . $controller->datetime . "',:code,'" . $controller->model->user_code . "','unread','no')");
        $statement->execute(array(':title' => $_POST['title'],
            ':text' => $_POST['text'],
            ':code' => $_POST['receiver']));
        $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
    }
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='messages.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='پیام ها -> ارسال پیام'>ارسال پیام {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='content'>
		<div class='form-elements'>
			<div>
				<h6>عنوان پیام<a class='necessary-complete'>*</a></h6>
				<input type='text' name='title' required />
			</div>
			<div>
				<h6>گیرنده <a class='necessary-complete'>*</a></h6>
				<select name='receiver'>";
$receivers = $controller->model->fetch_all('users', "where status <> 'deleted' order by `responsibility` ASC, `lastname` ASC, `firstname` ASC");
foreach ($receivers as $key) {
    $respon_receiver = $controller->model->fetch_one('responsibilities', "where `name` = '{$key['responsibility']}'");
    $title = $respon_receiver[1];
    if ($key[0] != $controller->model->user_code)
        $controller->view_content_beta .= "<option value='{$key[0]}'>{$key[1]} {$key[2]} [{$title}]</option>";
}

$controller->view_content_beta .= "
				</select>
			</div>
			<div>
				<h6>متن پیام <a class='necessary-complete'>*</a></h6>
				<textarea name='text' required></textarea>
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