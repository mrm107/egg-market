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
$controller->view->view_title = 'مشاهده کاربران';
$controller->set_sidebar();
$controller->checker_page_access(2);
$errors = '';
//users start
if (isset($_POST['delet-selected'])) {
    foreach ($_REQUEST as $key => $val) {
        if (strpos($key, 'delete') !== false) {
            $key = str_replace('delete', '', $key);
            if ($controller->model->user_respon == 'admin')
                $access = $controller->model->db->prepare("update users set status = 'deleted' where `id` = :delete");
            else
                $access = $controller->model->db->prepare("update users set status = 'deleted' where `responsibility` <> 'admin' and `id` = :delete");
            $access->execute(array(':delete' => $key));
            $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
        }
    }
}

$controller->view->view_nav = "
<a class='plusmenu' href='users.new.php'>کاربر جدید</a>
<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();

if ((isset($_POST['status']) and $_POST['status'] == 'all') or (isset($_GET['status']) and $_GET['status'] == 'all') or (!isset($_POST['status']) and !isset($_GET['status']))) {
    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
        if (isset($_POST['search']))
            $search = $_POST['search'];
        else
            $search = $_GET['search'];
        $user = $controller->model->db->prepare("select * from users where `status` <> 'deleted' and (`firstname` LIKE CONCAT('%', :search, '%') or `lastname` LIKE CONCAT('%', :search, '%') or `email` LIKE CONCAT('%', :search, '%')) order by `lastname` ASC, `firstname` ASC");
        $user->execute(array(':search' => $search));
        $status = '&search=' . $search;
    } else {
        $user = $controller->model->db->prepare("select * from users where `status` <> 'deleted' order by `lastname` ASC, `firstname` ASC");
        $user->execute();
        $status = '';
    }
} else {
    if (isset($_POST['status']))
        $status = $_POST['status'];
    else
        $status = $_GET['status'];
    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
        if (isset($_POST['search']))
            $search = $_POST['search'];
        else
            $search = $_GET['search'];
        $user = $controller->model->db->prepare("select * from users where `status` <> 'deleted' and responsibility = :status and (`firstname` LIKE CONCAT('%', :search, '%') or `lastname` LIKE CONCAT('%', :search, '%') or `email` LIKE CONCAT('%', :search, '%')) order by `lastname` ASC, `firstname` ASC");
        $user->execute(array(':search' => $search, ':status' => $status));
        $status = '&status=' . $status . '&search=' . $search;
    } else {
        $user = $controller->model->db->prepare("select * from users where `status` <> 'deleted' and responsibility = :status order by `lastname` ASC, `firstname` ASC");
        $user->execute(array(':status' => $status));
        $status = '&status=' . $status;
    }
}
$users = $user->fetchAll();
$controller->view_box_header_beta = "";
$controller->view_box_header_beta .= "
	<span title='کاربران -> مشاهده'>مشاهده کاربران</span>
	<div class='ui buttons mini custome-tools-button-place'>
	  <input type='submit' name='delet-selected' form='content' formaction='users.view.php?{$status}' class='ui button custome-tools-button' value='ح' title='حذف' onclick='return delete_checker()' />
	  <div class='or'></div>
	  <input type='submit' name='edit-selected' form='content' formaction='users.edit.php?{$status}' class='ui button custome-tools-button' value='و' title='ویرایش' />
	</div>
	{$errors}
	<form method='post'>";
$controller->view_box_header_beta .= "<input type='submit' value='جستجو' />";
$controller->view_box_header_beta .= "<select name='status' class='header-autosender'>";
if ((isset($_POST['status']) and $_POST['status'] == 'all') or (isset($_GET['status']) and $_GET['status'] == 'all') or (!isset($_POST['status']) and (!isset($_GET['status'])))) {
    $controller->view_box_header_beta .= "<option value='all' selected>همه</option>";
} else {
    $controller->view_box_header_beta .= "<option value='all'>همه</option>";
}

$ress = $controller->model->fetch_all('responsibilities', "order by `tag` ASC");
foreach ($ress as $key) {
    if ((isset($_POST['status']) and $_POST['status'] == $key[0]) or (isset($_GET['status']) and $_GET['status'] == $key[0])) {
        $controller->view_box_header_beta .= "<option value='{$key[0]}' selected>{$key[1]}</option>";
    } else {
        $controller->view_box_header_beta .= "<option value='{$key[0]}'>{$key[1]}</option>";
    }
}

$controller->view_box_header_beta .= "</select>";
if (isset($search)) {
    $controller->view_box_header_beta .= "<input type='text' name='search' value='" . $search . "' placeholder='کلید واژه ...' />";
} else {
    $controller->view_box_header_beta .= "<input type='text' name='search' placeholder='کلید واژه ...' />";
}
$controller->view_box_header_beta .= "
	</form><span class='text'>فیلتر : </span>";
$controller->view->view_box_header = $controller->view_box_header_beta;
$controller->view_content_beta .= "
		<div class='content-place-full' id='scroll-place'>
			<form method='post' action='' id='content'>
            <table>
                <tr>
                    <td>کد</td>
                    <td>نام و نام خانوادگی</td>
					<td>مسئولیت</td>
					<td>ایمیل</td>
					<td>وضعیت</td>
					<td style='width:70px;'>ابزار</td>
                </tr>";
$query_num = count($users);
$show_counter = 0;
foreach ($users as $key) {
    $show_counter++;

    if ($key['status'] == 'active')
        $key['status'] = 'فعال';
    elseif ($key['status'] == 'deactive')
        $key['status'] = '<span style="color:red;">غیرفعال</span>';
    else
        $key['status'] = '<span style="color:red;">نامشخص</span>';
    $respon = $controller->model->fetch_one("responsibilities", "where `name` =\"{$key['responsibility']}\" ");
    $respon = $respon[1];

    if($key['responsibility'] == 'cashier') {
        $cache = $controller->model->fetch_one("cashs", "where id = \"{$key['cach_access']}\" ");
        $respon .= "<br><span style='font-size: 12px; color: gray;'>[{$cache[1]}]</span>";
    }

    $controller->view_content_beta .= "
				<tr>
                    <td>{$key[0]}</td>
					<td>{$key[1]} {$key[2]}</td>
					<td>{$respon}</td>
					<td style='font-family:myriadpro; direction:ltr;'>{$key['email']}</td>
					<td>{$key['status']}</td>
                    <td>
					<div class='ui radio huge checkbox custome-checkbox-huge' title='ویرایش'>
						<input type='radio' name='edit' value='{$key[0]}'>
						<label></label>
					</div>";
    if ($controller->model->user_code != $key[0]) {
        $controller->view_content_beta .= "<div class='ui huge checkbox custome-checkbox-huge' style='margin-right:15px;' title='حذف'>
						<input type='checkbox' name='delete{$key[0]}'>
						<label></label>
					</div>";
    }
    $controller->view_content_beta .= "</td>
                </tr>
				 ";
}
$controller->view_content_beta .= "
				</table>
			</form>
		</div>";
//users end
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>