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
$controller->view->view_title = 'تخم مرغ';
$controller->set_sidebar();
$controller->checker_page_access(98);
$errors = '';
//users start
if (isset($_POST['delet-selected'])) {
    foreach ($_REQUEST as $key => $val) {
        if (strpos($key, 'delete') !== false) {
            $key = str_replace('delete', '', $key);
            $access = $controller->model->db->prepare("update loads set `status` = 'sold' WHERE `id` = :delete");
            $access->execute(array(':delete' => $key));

            $check = $controller->model->db->prepare("select * from loads where id = :id");
            $check->execute(array(":id" => $key));
            $load_data = $check->fetch();

            $message_id = $load_data['telegram_message_id'];

            if ($controller->check_null($message_id) != NULL) {
                require_once('../triggers/remove-load.php');
                remove_load($message_id);
            }

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
<a class='plusmenu' href='loads.new.php'>آگهی جدید</a>
<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();
$start_page = ($show * ($page - 1));
if ($start_page < 0)
    $start_page = 0;
$limit = " limit {$start_page},{$show}";
if (isset($_REQUEST['customer_id'])) {
    $customer_id = $_REQUEST['customer_id'];
    $user = $controller->model->db->prepare("select * from loads where reg_customer = :customer_id order by `id` DESC {$limit}");
    $user->execute(array(':customer_id' => $customer_id));
    $status = '&customer_id=' . $customer_id;

    $customer_data = $controller->model->db->prepare("select * from customers where id = :customer_id");
    $customer_data->execute(array(':customer_id' => $customer_id));
    $customer_data = $customer_data->fetch();

} elseif ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
    $search = $_REQUEST['search'];
    $user = $controller->model->db->prepare("select * from loads where (`id` LIKE CONCAT('%', :search, '%') or `title` LIKE CONCAT('%', :search, '%')) order by `id` DESC {$limit}");
    $user->execute(array(':search' => $search));
    $status = '&search=' . $search;
} else {
    $user = $controller->model->db->prepare("select * from loads order by `id` DESC {$limit}");
    $user->execute();
    $status = '';
}
$users = $user->fetchAll();

$provinces = $controller->model->db->prepare("SELECT id, title FROM locations_provinces");
$provinces->execute();
$provinces = $provinces->fetchAll(PDO::FETCH_KEY_PAIR);


$menu_title_extra = '';
if (isset($customer_id)) {
    $menu_title_extra = " - مشتری شماره {$customer_id} ({$customer_data['name']})";
}

$controller->view_box_header_beta = "";
$controller->view_box_header_beta .= "
	<span>تخم مرغ{$menu_title_extra}</span>
	<div class='ui buttons mini custome-tools-button-place'>
	  <input type='submit' name='delet-selected' form='content' formaction='loads.view.php?{$status}' class='ui button custome-tools-button' value='ح' title='حذف' onclick='return delete_checker()' />
	  <div class='or'></div>
	  <input type='submit' name='edit-selected' form='content' formaction='loads.edit.php?{$status}' class='ui button custome-tools-button' value='و' title='ویرایش' />
	</div>
	{$errors}
	<form method='post' action='?'>";
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
                    <td>وزن</td>
                    <td>تعداد</td>
                    <td>مشخصات</td>
                    <td>محل / مشتری</td>
                    <td>وضعیت</td>
                    <td>ثبت</td>
					<td style='width:70px;'>ابزار</td>
                </tr>
				";
$query_num = count($users);
$show_num = $show;
$show_counter = 0;

$load_status = array("accepted" => 'تایید شده', "rejected" => 'رد شده', "pending" => '<span style="background-color: orange; padding: 0px 3px; color: white;">در انتظار</span>', "deactive" => 'حذف شده', "expired" => 'منقضی شده', "sold" => 'فروخته شده');

$print_types = [
    "with" => "با پرینت",
    "without" => "بدون پرینت",
    "ability" => "با قابلیت پرینت",
    NULL => "نامشخص",
];

$yolk_type = [
    "golden" => "طلایی",
    "simple" => "ساده",
    "corn" => "ذرتی",
    NULL => "نامشخص",
];

$pack_type = [
    "bulk" => "فله (شانه ای)",
    "box" => "بسته بندی",
    NULL => "نامشخص",
];

$type = [
    "announcement" => "اعلام بار",
    "request" => "درخواست بار",
];

$quality = [
    "lux" => "لوکس",
    "grade-1" => "درجه ۱",
    "grade-2" => "درجه ۲",
    "for-factories" => "درجه ۳ (کارخانه‌ای)",
    NULL => "نامشخص",
];

foreach ($users as $key) {
    $key['reg_date'] = explode(' ', $key['reg_date']);
    $date = explode('-', $key['reg_date'][0]);
    $date = $controller->jdate->toJalali($date[0], $date[1], $date[2]);
    $date = $key['reg_date'][1] . '<br/>' . $date[0] . '-' . $date[1] . '-' . $date[2];

    $reg_user = $controller->model->fetch_one("users", "where id = '{$key['reg_user']}' ");

    $props = '';
    $props .= $type[$key['type']];
    $props .= ' - ';
    $props .= $pack_type[$key['pack_type']];
    $props .= ' - ';
    $props .= $print_types[$key['print_type']];
    $props .= ' - ';
    $props .= $quality[$key['quality']];
    $props .= ' - ';
    $props .= $yolk_type[$key['yolk_type']];
    $props .= ($controller->check_null_no($key['box_type']) != null) ? "<br>{$key['box_type']}" : '';
    $props .= ($controller->check_null_no($key['stage_type']) != null) ? "<br>{$key['stage_type']}" : '';

    if ($controller->check_null($key['reg_customer']) != NULL) {
        $customer_data = $controller->model->fetch_one("customers", "where id = {$key['reg_customer']} ");
        $customer = "{$customer_data['name']} <span style='font-size: 12px;'>(<a style='color: blue;' href='?customer_id={$key['reg_customer']}'>آگهی ها</a> - <a style='color: blue;' href='customers.edit.php?edit={$key['reg_customer']}'>پروفایل</a>)</span>";
    } else {
        $customer = '';
    }

    $show_counter++;
    $controller->view_content_beta .= "<tr>";
    $controller->view_content_beta .= "<td>{$key['id']}</td>";
    $controller->view_content_beta .= "<td>{$key['weight']}</td>";
    $controller->view_content_beta .= "<td>{$key['count']}</td>";
    $controller->view_content_beta .= "<td>{$props}</td>";
    $controller->view_content_beta .= "<td>
        {$provinces[$key['origin_field1']]} <span style='color: gray; font-size: 12px'>{$key['origin_field2']}</span><br>
        {$customer}
    </td>";
    $controller->view_content_beta .= "<td>{$load_status[$key['status']]}</td>";
    $controller->view_content_beta .= "<td>{$date}<br>{$key['reg_client']}<br/><span style='color: gray; font-size: 12px'>{$reg_user['firstname']} {$reg_user['lastname']}</span></td>";
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

//if (isset($_REQUEST['marketer-load-id'])) {
//    $users = [];
//} else {
//    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
//        $search = $_REQUEST['search'];
//        $user = $controller->model->db->prepare("SELECT * FROM loads WHERE (`id` LIKE CONCAT('%', :search, '%') or `title` LIKE CONCAT('%', :search, '%')) ORDER BY `id` DESC ");
//        $user->execute(array(':search' => $search));
//        $status = '&search=' . $search;
//    } else {
//        $user = $controller->model->db->prepare("SELECT * FROM loads ORDER BY `id` DESC ");
//        $user->execute();
//        $status = '';
//    }
//
//    $users = $user->fetchAll();
//}
//$query_num = count($users);
//if ($show_num < ($query_num)) {
//    $page_count = ceil(($query_num) / $show_num);
//    if ($page == 1)
//        $firsthref = "";
//    else
$firsthref = "href='?page=" . ($page - 1) . "{$status}'";

//    if ($page == $page_count)
//        $lasthref = "";
//    else
$lasthref = "href='?page=" . ($page + 1) . "{$status}'";


$controller->view_content_beta .= "
					 <tr>
						<td colspan='8' class='numberbox'>
							<div id='pages-nav'>
								<a {$firsthref} class='first-child'><i class='angle left icon'></i></a>
								<div>";
//    for ($page_counter = 1; $page_counter <= $page_count; $page_counter++) {
//        if ($page == $page_counter)
//            $controller->view_content_beta .= "<a class='selected'>{$page_counter}</a>";
//        else
//            $controller->view_content_beta .= "<a href='?page={$page_counter}{$status}'>{$page_counter}</a>";
//    }
$controller->view_content_beta .= "</div>
								<a {$lasthref} class='last-child'><i class='angle right icon'></i></a>
							</div>
						</td>
					</tr>";
//}
$controller->view_content_beta .= "
				</table>
			</form>
		</div>";
//users end
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>