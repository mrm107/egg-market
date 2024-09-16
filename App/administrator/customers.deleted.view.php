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
$controller->view->view_title = 'مشتریان حذف شده';
$controller->set_sidebar();
$controller->checker_page_access(91);
$errors = '';
//users start
if (isset($_POST['delet-selected'])) {
    foreach ($_REQUEST as $key => $val) {
        if (strpos($key, 'delete') !== false) {
            $key = str_replace('delete', '', $key);
            $access = $controller->model->db->prepare("UPDATE customers SET status = 'accepted' WHERE `id` = :delete");
            $access->execute(array(':delete' => $key));
            $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
        }
    }
}

$default_show_user = 30;
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
<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();
$start_page = ($show * ($page - 1));
if ($start_page < 0)
    $start_page = 0;
$limit = " limit {$start_page},{$show}";


if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
    if (isset($_POST['search']))
        $search = $_POST['search'];
    else
        $search = $_GET['search'];
    $realsearch = $search;
    $user = $controller->model->db->prepare("select * from customers where `status` = 'deleted' and (`name` LIKE CONCAT('%', :search, '%') or `mobile` LIKE CONCAT('%', :search, '%')) order by `name` ASC {$limit}");
    $user->execute(array(':search' => $search));
    $status = '&search=' . $search;
} else {
    $realsearch = '';
    $user = $controller->model->db->prepare("select * from customers where `status` = 'deleted' order by `name` ASC {$limit}");
    $user->execute();
    $status = '';
}
$realstatus = '';

$users = $user->fetchAll();

$controller->view_box_header_beta = "";
$controller->view_box_header_beta .= "
	<span title='مشتریان حذف شده -> مشتریان حذف شده'>مشتریان حذف شده</span>
	<div class='ui buttons mini custome-tools-button-place'>
	  <input type='submit' name='delet-selected' form='content' formaction='customers.deleted.view.php?{$status}' class='ui button custome-tools-button' value='ب' title='بازگرداندن مشتری' onclick='if (!confirm(\"آیا اطمینان دارید که می خواهید این مشتریان را بازگردانید؟\")) return false; return true;' />
	</div>
	{$errors}
	<form method='post'>";
$controller->view_box_header_beta .= "<input type='submit' value='جستجو' />";
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
                    <td>نام</td>
                    <!--<td>نوع</td>-->
					<td>موبایل</td>
					<td>وضعیت</td>
					<td>ثبت نام</td>
					<td style='width:45px;'>ابزار</td>
                </tr>
				";
$query_num = count($users);
$show_num = $show;
$show_counter = 0;
foreach ($users as $key) {
    $show_counter++;

    $key['reg_date'] = explode(' ', $key['reg_date']);
    $date = explode('-', $key['reg_date'][0]);
    $date = $controller->jdate->toJalali($date[0], $date[1], $date[2]);
    $date = $date[0] . '-' . $date[1] . '-' . $date[2];

    $status = array("accepted" => "تایید شده", "pending" => "تایید نشده", "deleted" => "حذف شده");
//    $type = array("driver" => "راننده", "owner" => "صاحب بار");

    $controller->view_content_beta .= "<tr>";
    $controller->view_content_beta .= "<td>{$key['id']}</td>";
    $controller->view_content_beta .= "<td>{$key['name']}</td>";
//    $controller->view_content_beta .= "<td>{$type[$key['type']]}</td>";
    $controller->view_content_beta .= "<td>{$key['mobile']}</td>";
    $controller->view_content_beta .= "<td>{$status[$key['status']]}</td>";
    $controller->view_content_beta .= "<td><span style='font-size: 12px;'>{$date}</span><br>{$key['reg_client']}</td>";
    $controller->view_content_beta .= "<td>
					<div class='ui huge checkbox custome-checkbox-huge' style='margin-right:15px;' title='بازگرداندن مشتری'>
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
    $user = $controller->model->db->prepare("SELECT * FROM customers WHERE `status` = 'deleted' AND (`name` LIKE CONCAT('%', :search, '%') OR `mobile` LIKE CONCAT('%', :search, '%')) ORDER BY `name` ASC ");
    $user->execute(array(':search' => $search));
    $status = '&search=' . $search;
} else {
    $realsearch = '';
    $user = $controller->model->db->prepare("SELECT * FROM customers WHERE `status` = 'deleted' ORDER BY `name` ASC ");
    $user->execute();
    $status = '';
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
$controller->view->view_header = "
	<script type='text/javascript' src='../../Template/plugin/fancyBox.v18d1712/jquery.fancybox.pack.js?v=2.1.5'></script>
	<link rel='stylesheet' type='text/css' href='../../Template/plugin/fancyBox.v18d1712/jquery.fancybox.css?v=2.1.5' media='screen' />
	<script type='text/javascript'>
	$('.fancybox').fancybox();
	</script>";
$controller->view->show('../../Template/admin.index.php');
?>