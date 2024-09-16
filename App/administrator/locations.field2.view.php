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
$controller->view->view_title = 'موارد پیشنهادی';
$controller->set_sidebar();
$controller->checker_page_access(95);
$errors = '';
//users start
if (isset($_POST['delet-selected'])) {
    foreach ($_REQUEST as $key => $val) {
        if (strpos($key, 'delete') !== false) {
            $key = str_replace('delete', '', $key);
            $access = $controller->model->db->prepare("delete from locations_field2 WHERE `id` = :delete");
            $access->execute(array(':delete' => $key));
            $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
        }
    }
}

$default_show_user = 50;
if (isset($_GET['page']) or isset($_POST['page'])) {
    if (isset($_GET['page']))
        $page = intval($_GET['page']);
    else
        $page = intval($_POST['page']);
} else
    $page = 1;

if ($page == '' or $page == ' ' or $page == NULL)
    $page = 1;
$show = $default_show_user;

$controller->view->view_nav = "
<a class='plusmenu' href='locations.field2.new.php'>مورد جدید</a>
<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();
$start_page = ($show * ($page - 1));
if ($start_page < 0)
    $start_page = 0;
$limit = " limit {$start_page},{$show}";

if(isset($_REQUEST['prov']) and $controller->check_null($_REQUEST['prov']) != NULL) {
    $provId = $controller->check_null($_REQUEST['prov']);
    $prov = "and province = '{$provId}'";
} else {
    $prov = '';
}

if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
    if (isset($_POST['search']))
        $search = $_POST['search'];
    else
        $search = $_GET['search'];
    $realsearch = $search;
    $user = $controller->model->db->prepare("select * from locations_field2 where 1 = 1 {$prov} and (`title` LIKE CONCAT('%', :search, '%')) order by `title` ASC {$limit}");
    $user->execute(array(':search' => $search));
    $status = '&search=' . $search;
} else {
    $realsearch = '';
    $user = $controller->model->db->prepare("select * from locations_field2 where 1 = 1 {$prov} order by `title` ASC {$limit}");
    $user->execute();
    $status = '';
}

if($prov != '') {
    $status .= '&prov='.$provId;
}

$realstatus = '';

$users = $user->fetchAll();


$provinces = $controller->model->db->prepare("SELECT id, title FROM locations_provinces");
$provinces->execute();
$provinces = $provinces->fetchAll(PDO::FETCH_KEY_PAIR);

$controller->view_box_header_beta = "";
$controller->view_box_header_beta .= "
	<span title='مکان ها -> موارد پیشنهادی'>موارد پیشنهادی</span>
	<div class='ui buttons mini custome-tools-button-place'>
	  <input type='submit' name='delet-selected' form='content' formaction='locations.field2.view.php?{$status}' class='ui button custome-tools-button' value='ح' title='حذف' onclick='return delete_checker()' />
	  <div class='or'></div>
	  <input type='submit' name='edit-selected' form='content' formaction='locations.field2.edit.php?{$status}' class='ui button custome-tools-button' value='و' title='ویرایش' />
	</div>
	{$errors}
	<form method='get' action='?'>";
$controller->view_box_header_beta .= "<input type='submit' value='جستجو' />";
if (isset($search)) {
    $controller->view_box_header_beta .= "<input type='text' name='search' value='" . $search . "' placeholder='کلید واژه ...' />";
} else {
    $controller->view_box_header_beta .= "<input type='text' name='search' placeholder='کلید واژه ...' />";
}
$controller->view_box_header_beta .= "<select name='prov' class='header-autosender'>";
if ((isset($_POST['prov']) and $_POST['prov'] == 'all') or (isset($_GET['prov']) and $_GET['prov'] == 'all') or (!isset($_POST['prov']) and (!isset($_GET['prov'])))) {
    $controller->view_box_header_beta .= "<option value='' selected>همه</option>";
} else {
    $controller->view_box_header_beta .= "<option value=''>همه</option>";
}
foreach ($provinces as $key => $val) {
    if ((isset($_POST['prov']) and $_POST['prov'] == $key) or (isset($_GET['prov']) and $_GET['prov'] == $key)) {
        $controller->view_box_header_beta .= "<option value='{$key}' selected>{$val}</option>";
    } else {
        $controller->view_box_header_beta .= "<option value='{$key}'>{$val}</option>";
    }
}
$controller->view_box_header_beta .= "</select>
	</form><span class='text'>فیلتر : </span>";
$controller->view->view_box_header = $controller->view_box_header_beta;
$controller->view_content_beta .= "
		<div class='content-place-full' id='scroll-place'>
			<form method='post' action='' id='content'>
            <table>
                <tr>
                    <td>کد</td>
                    <td>عنوان</td>
                    <td>استان</td>
					<td style='width:70px;'>ابزار</td>
                </tr>
				";
$query_num = count($users);
$show_num = $show;
$show_counter = 0;

foreach ($users as $key) {
    $show_counter++;
    $controller->view_content_beta .= "<tr>";
    $controller->view_content_beta .= "<td>{$key['id']}</td>";
    $controller->view_content_beta .= "<td>{$key['title']}</td>";
    $controller->view_content_beta .= "<td>{$provinces[$key['province']]}</td>";
    $controller->view_content_beta .= "<td>
					<div class='ui radio huge checkbox custome-checkbox-huge' title='ویرایش'>
						<input type='radio' name='edit' value='{$key[0]}'>
						<label></label>
					</div>
					<div class='ui huge checkbox custome-checkbox-huge' style='margin-right:15px;' title='حذف'>
						<input type='checkbox' name='delete{$key[0]}'>
						<label></label>
					</div>
					</td>
                </tr>
				 ";
    if ($show_counter == $show_num)
        break;
}


$realstatus = 'all';
if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
    if (isset($_POST['search']))
        $search = $_POST['search'];
    else
        $search = $_GET['search'];
    $realsearch = $search;
    $user = $controller->model->db->prepare("SELECT * FROM locations_field2 WHERE 1 = 1 {$prov} and (`title` LIKE CONCAT('%', :search, '%')) ORDER BY `title` ASC ");
    $user->execute(array(':search' => $search));
} else {
    $realsearch = '';
    $user = $controller->model->db->prepare("SELECT * FROM locations_field2 where 1 = 1 {$prov} ORDER BY `title` ASC ");
    $user->execute();
}

$users = $user->fetchAll();
$query_num = count($users);
if ($show_num < ($query_num)) {
    $page_count = ceil(($query_num) / $show_num);
    if ($page == 1)
        $firsthref = "";
    else
        $firsthref = "href='?page=" . ($page - 1) . "{$status}'";

    if ($page == $page_count)
        $lasthref = "";
    else
        $lasthref = "href='?page=" . ($page + 1) . "{$status}'";


    $controller->view_content_beta .= "
					 <tr>
						<td colspan='8' class='numberbox'>
							<div id='pages-nav'>
								<a {$firsthref} class='first-child'><i class='angle left icon'></i></a>
								<div>";
    for ($page_counter = 1; $page_counter <= $page_count; $page_counter++) {
        if ($page == $page_counter)
            $controller->view_content_beta .= "<a class='selected'>{$page_counter}</a>";
        else
            $controller->view_content_beta .= "<a href='?page={$page_counter}{$status}'>{$page_counter}</a>";
    }
    $controller->view_content_beta .= "</div>
								<a {$lasthref} class='last-child'><i class='angle right icon'></i></a>
							</div>
						</td>
					</tr>";
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