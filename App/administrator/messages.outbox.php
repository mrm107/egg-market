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
$controller->view->view_title = 'صندوق خروجی پیام ها';
$controller->set_sidebar();
$errors = '';
//messages start
$default_show_message = 30;
if (isset($_GET['page']) or isset($_POST['page'])) {
    if (isset($_GET['page']))
        $page = intval($_GET['page']);
    else
        $page = intval($_POST['page']);
} else
    $page = 1;

if ($page == '' or $page == ' ' or $page == NULL)
    $page = 1;
$show = $default_show_message;

$controller->view->view_nav = "
<a class='plusmenu' href='messages.new.php'>پیام جدید</a>
<a class='plusmenu' href='messages.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$start_page = ($show * ($page - 1));
if ($start_page < 0)
    $start_page = 0;
$limit = " limit {$start_page},{$show}";
if ((isset($_POST['status']) and $_POST['status'] == 'all') or (isset($_GET['status']) and $_GET['status'] == 'all') or (!isset($_POST['status']) and !isset($_GET['status']))) {
    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
        if (isset($_POST['search']))
            $search = $_POST['search'];
        else
            $search = $_GET['search'];
        $message = $controller->model->db->prepare("select * from messages where (`title` LIKE CONCAT('%', :search, '%') or `text` LIKE CONCAT('%', :search, '%')) and `sender` = '" . $controller->model->user_code . "' order by `date` DESC {$limit}");
        $message->execute(array(':search' => $search));
        $status = '&search=' . $search;
    } else {
        $message = $controller->model->db->prepare("select * from messages where `sender` = '" . $controller->model->user_code . "' order by `date` DESC {$limit}");
        $message->execute();
        $status = '';
    }
} else {
    if (isset($_POST['search']))
        $search = $_POST['search'];
    else
        $search = $_GET['search'];
    if (isset($_POST['status']))
        $status = $_POST['status'];
    else
        $status = $_GET['status'];
    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
        $message = $controller->model->db->prepare("select * from messages where (`title` LIKE CONCAT('%', :search, '%') or `text` LIKE CONCAT('%', :search, '%')) and `sender` = '" . $controller->model->user_code . "' and `status` = :status order by `date` DESC {$limit}");
        $message->execute(array(':search' => $search, ':status' => $status));
        $status = '&status=' . $status . '&search=' . $search;
    } else {
        $message = $controller->model->db->prepare("select * from messages where `sender` = '" . $controller->model->user_code . "' and `status` = :status order by `date` DESC {$limit}");
        $message->execute(array(':status' => $status));
        $status = '&status=' . $status;
    }
}
$messages = $message->fetchAll();
$controller->view_box_header_beta = "";
$controller->view_box_header_beta .= "
	<span title='پیام ها -> صندوق خروجی'>صندوق خروجی پیام ها</span>
	{$errors}
	<form method='post'>";
$controller->view_box_header_beta .= "<input type='submit' value='جستجو' />";
$controller->view_box_header_beta .= "<select name='status' class='header-autosender'>";
if ((isset($_POST['status']) and $_POST['status'] == 'all') or (isset($_GET['status']) and $_GET['status'] == 'all') or (!isset($_POST['status']) and (!isset($_GET['status'])))) {
    $controller->view_box_header_beta .= "<option value='all' selected>همه</option>";
} else {
    $controller->view_box_header_beta .= "<option value='all'>همه</option>";
}
if ((isset($_POST['status']) and $_POST['status'] == 'read') or (isset($_GET['status']) and $_GET['status'] == 'read')) {
    $controller->view_box_header_beta .= "<option value='read' selected>خوانده شده</option>";
} else {
    $controller->view_box_header_beta .= "<option value='read'>خوانده شده</option>";
}
if ((isset($_POST['status']) and $_POST['status'] == 'unread') or (isset($_GET['status']) and $_GET['status'] == 'unread')) {
    $controller->view_box_header_beta .= "<option value='unread' selected>خوانده نشده</option>";
} else {
    $controller->view_box_header_beta .= "<option value='unread'>خوانده نشده</option>";
}
if ((isset($_POST['status']) and $_POST['status'] == 'answerd') or (isset($_GET['status']) and $_GET['status'] == 'answerd')) {
    $controller->view_box_header_beta .= "<option value='answerd' selected>پاسخ داده شده</option>";
} else {
    $controller->view_box_header_beta .= "<option value='answerd'>پاسخ داده شده</option>";
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
                    <td>عنوان</td>
                    <td>گیرنده</td>
					<td>زمان ارسال</td>
					<td>وضعیت</td>
                </tr>";
$query_num = count($messages);
$show_num = $show;
$show_counter = 0;
foreach ($messages as $key) {
    $show_counter++;
    if ($key[1] == 'inner') {
        $sender_info = $controller->model->fetch_one('users', "where id = '{$key[5]}'");
        $sender_info = "{$sender_info[1]} {$sender_info[2]}<br/><span style='font-size:11px;'>[کاربر شماره {$key[5]}]</span>";
    } else {
        $sender_info = "{$key[7]}<br/><strong>{$key[8]}</strong><br/>{$key[9]}";
    }
    $key[4] = explode(' ', $key[4]);
    $date = explode('-', $key[4][0]);
    $date = $controller->jdate->toJalali($date[0], $date[1], $date[2]);
    $date = $date[0] . '-' . $date[1] . '-' . $date[2];
    if ($key['status'] == 'read')
        $key['status'] = '<span style="color:green;">خوانده شده</span>';
    elseif ($key['status'] == 'unread')
        $key['status'] = '<span style="color:red;">خوانده نشده</span>';
    elseif ($key['status'] == 'answerd')
        $key['status'] = 'پاسخ داده شده';
    $controller->view_content_beta .= "
				<tr>
                    <td>{$key[0]}</td>
                    <td>{$key[2]}</td>
                    <td style='white-space:nowrap;'>{$sender_info}</td>
					<td style='white-space:nowrap;'>{$date}<br/>{$key[4][1]}</td>
					<td style='white-space:nowrap;'>{$key['status']}</td>
                </tr>
				<tr>
					<td colspan='5' class='description'><div class='show-title-text'>متن پیام {$key[0]} : </div>{$key[3]}</td>
                </tr>
				 ";
    if ($show_counter == $show_num)
        break;
}
if ((isset($_POST['status']) and $_POST['status'] == 'all') or (isset($_GET['status']) and $_GET['status'] == 'all') or (!isset($_POST['status']) and !isset($_GET['status']))) {
    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
        if (isset($_POST['search']))
            $search = $_POST['search'];
        else
            $search = $_GET['search'];
        $message = $controller->model->db->prepare("select * from messages where (`title` LIKE CONCAT('%', :search, '%') or `text` LIKE CONCAT('%', :search, '%')) and `sender` = '" . $controller->model->user_code . "' order by `date` DESC");
        $message->execute(array(':search' => $search));
        $status = '&search=' . $search;
    } else {
        $message = $controller->model->db->prepare("select * from messages where `sender` = '" . $controller->model->user_code . "' order by `date` DESC");
        $message->execute();
        $status = '';
    }
} else {
    if (isset($_POST['search']))
        $search = $_POST['search'];
    else
        $search = $_GET['search'];
    if (isset($_POST['status']))
        $status = $_POST['status'];
    else
        $status = $_GET['status'];
    if ((isset($_POST['search']) and trim($_POST['search']) != '') or (isset($_GET['search']) and trim($_GET['search']) != '')) {
        $message = $controller->model->db->prepare("select * from messages where (`title` LIKE CONCAT('%', :search, '%') or `text` LIKE CONCAT('%', :search, '%')) and `sender` = '" . $controller->model->user_code . "' and `status` = :status order by `date` DESC");
        $message->execute(array(':search' => $search, ':status' => $status));
        $status = '&status=' . $status . '&search=' . $search;
    } else {
        $message = $controller->model->db->prepare("select * from messages where `sender` = '" . $controller->model->user_code . "' and `status` = :status order by `date` DESC");
        $message->execute(array(':status' => $status));
        $status = '&status=' . $status;
    }
}
$messages = $message->fetchAll();
$query_num = count($messages);
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
						<td colspan='5' class='numberbox'>
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
//messages end
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>