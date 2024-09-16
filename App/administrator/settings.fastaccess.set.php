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
$controller->view->view_title = 'دسترسی سریع';
$controller->checker_page_access(55);
$errors = '';

if (isset($_POST['submit'])) {
    try {
        $submit_fast = array();
        $access = $controller->model->fetch_one('responsibilities', "where `name` = '" . $controller->model->user_respon . "'");
        $access = unserialize($access[2]);
        $fast_main = $controller->model->fetch_all('responsibilities_work', "where `mother_array` = 'no' order by `sort` ASC");
        $fast_access_user = unserialize($controller->model->user_fastaccess);
        foreach ($fast_main as $key) {
            if (in_array($key[0], $access)) {
                if (isset($_POST[$key[0]])) {
                    $submit_fast[] = $key[0];
                }
                $fast_sub = $controller->model->fetch_all('responsibilities_work', "where `mother_array` = '{$key[0]}' order by `sort` ASC");
                foreach ($fast_sub as $keysub) {
                    if (in_array($keysub[0], $access)) {
                        if (isset($_POST[$keysub[0]])) {
                            $submit_fast[] = $keysub[0];
                        }
                    }
                }
            }
        }
        $submit_fast = serialize($submit_fast);
        $statement = $controller->model->db->prepare("update users set `fast_access` = :submitfast where `id` = '" . $controller->model->user_code . "'");
        $statement->execute(array(':submitfast' => $submit_fast));
        $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
    } catch (PDOException $e) {
        $show_error = $e->getMessage();
        $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
    }
}

$controller->set_sidebar();
$controller->view->view_nav = $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='تنظیمات -> دسترسی سریع'>دسترسی سریع {$errors}</span><form><input type='submit' form='newdoc' name='submit' value='ویرایش' /></form>";


$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>";
$access = $controller->model->fetch_one('responsibilities', "where `name` = '" . $controller->model->user_respon . "'");
$access = unserialize($access[2]);
$fast_main = $controller->model->fetch_all('responsibilities_work', "where `mother_array` = 'no' order by `sort` ASC");
$row = $controller->model->fetch_one('users', "where `id` = '" . $controller->model->user_code . "'");
if ($controller->check_null_no($row['fast_access']) == NULL) {
    $fast_access_user = array();
} else {
    $fast_access_user = unserialize($row['fast_access']);
}
foreach ($fast_main as $key) {
    if (in_array($key[0], $access)) {
        $controller->view_content_beta .= "<div style='margin-right:0%;'>";
        $controller->view_content_beta .= "<div class='header' style='width:85%; margin-right:15%;'>{$key[2]}</div>";
        $controller->view_content_beta .= "<table style='width:85%; margin-right:10%;'>";
        $controller->view_content_beta .= "<tr><td style='width:50px;'>کد</td><td>نام</td><td style='width:60px;'>وضعیت</td></tr>";
        if ($key[8] == 'list') {
            $fast_sub = $controller->model->fetch_all('responsibilities_work', "where `mother_array` = '{$key[0]}' and `show_in_fast_access` = 'yes' order by `sort` ASC");
            foreach ($fast_sub as $keysub) {
                if (in_array($keysub[0], $access)) {
                    $controller->view_content_beta .= "
						<tr>
							<td style='height:30px;'>{$keysub[0]}</td>
							<td style='height:30px;'>{$keysub[2]}</td>
							<td style='height:30px;'>";
                    if (in_array($keysub[0], $fast_access_user))
                        $controller->view_content_beta .= "<input type='checkbox' style='margin:0px; width:35px;' name='{$keysub[0]}' checked />";
                    else
                        $controller->view_content_beta .= "<input type='checkbox' style='margin:0px; width:35px;' name='{$keysub[0]}' />";
                    $controller->view_content_beta .= "</td>
						</tr>";
                }
            }
        } else {
            $controller->view_content_beta .= "
			<tr>
				<td style='height:30px;'>{$key[0]}</td>
				<td style='height:30px;'>{$key[2]}</td>
				<td style='height:30px;'>";
            if (in_array($key[0], $fast_access_user))
                $controller->view_content_beta .= "<input type='checkbox' style='margin:0px; width:35px;' name='{$key[0]}' checked />";
            else
                $controller->view_content_beta .= "<input type='checkbox' style='margin:0px; width:35px;' name='{$key[0]}' />";
            $controller->view_content_beta .= "</td>
			</tr>";
        }
        $controller->view_content_beta .= "</table></div>";
    }
}

$controller->view_content_beta .= "<div style='clear:right;'><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ویرایش' /></div>
		</div>
		</form>
	</div>";

$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>