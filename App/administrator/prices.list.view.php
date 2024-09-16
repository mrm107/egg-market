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
$controller->view->view_title = 'لیست قیمت';
$controller->checker_page_access(114);
$errors = '';

@$selected_date = $_REQUEST['selected_date'];
$has_date = !!$selected_date;

if (isset($_POST['submit'])) {
    try {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'price-') !== false) {

                $name = explode('-', $key);
                $id = $name[2];
                $type = $name[1];
                $resource = $name[4];

                $price_1 = $_POST["price-{$type}-{$id}-first-{$resource}"];
                $price_2 = $_POST["price-{$type}-{$id}-second-{$resource}"];
                $price_final = $_POST["price-{$type}-{$id}-final-{$resource}"];

                $delete = $controller->model->db->prepare("delete from prices_list where type=:type and type_id=:type_id and date=:date and resource=:resource");
                $delete->execute(array(":type" => $type, ":type_id" => $id, ":resource" => $resource, ":date" => $selected_date));

                $insert = $controller->model->db->prepare("insert into prices_list (`date`, `registrar`, `type`, `type_id`, `price_1`, `price_2`, `price_final`, `resource`) values (:date, :registrar, :type, :type_id, :price1, :price2, :price_final, :resource)");
                $insert->execute(array(":type" => $type, ":type_id" => $id, ":resource" => $resource, ":date" => $selected_date, ":registrar" => $controller->model->user_code, ":price1" => $price_1, ":price2" => $price_2, ":price_final" => $price_final));
            }
        }

//        $statement = $controller->model->db->prepare("UPDATE `prices_weights` SET `title` = :title, `sort` = :sort WHERE `id` = :id ");
//        $statement->execute(array(
//            ":title" => utils__arabic_character_to_persian($_POST['title']),
//            ":sort" => utils__arabic_character_to_persian($_POST['sort']),
//            ':id' => $editid));

        $errors = "<span class='good-alert'>وظیفه مورد نظر انجام شد .</span>";
    } catch (PDOException $e) {
        $show_error = $e->getMessage();
        $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
    }
}

$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='قیمت گذاری -> لیست قیمت'>لیست قیمت {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='get' id='newdoc' enctype='multipart/form-data'>
		<div class='form-elements'>
            <div>
			    <h6>تاریخ</h6>
			    <input type='date' name='selected_date' min='1996-12-19' value='{$selected_date}' required>    
            </div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='جستجو' /></div>
		</div>
		</form>
		<form></form>
	";

if ($has_date) {
    $resources = $controller->model->db->prepare("select * from prices_resources order by `sort` ASC, `title` ASC");
    $resources->execute();
    $resources = $resources->fetchAll();

    $cities = $controller->model->db->prepare("select * from prices_cities order by `sort` ASC, `title` ASC");
    $cities->execute();
    $cities = $cities->fetchAll();

    $weights = $controller->model->db->prepare("select * from prices_weights order by `sort` ASC, `title` ASC");
    $weights->execute();
    $weights = $weights->fetchAll();

    $controller->view_content_beta .= "<form method='post'>";
    foreach ($resources as $resource_item) {
        $controller->view_content_beta .= "
            <div style='font-family:byekan; background-color: black; font-size: 18px; height: 50px; color: white; float: right; width: 100%; text-align: center; line-height: 50px; margin-top: 50px;'>{$resource_item['title']}</div>
            <table id='commodities-place-factor'>
                <tr>
					<td colspan='4'>شهر ها</td>
				</tr>
				<tr>
					<td>عنوان</td>
					<td style='width:93px;'>قیمت اول</td>
					<td style='width:93px;'>قیمت دوم</td>
					<td style='width:93px;'>قیمت نهایی</td>
				</tr>";
        foreach ($cities as $item) {
            $item_query = $controller->model->db->prepare("select * from prices_list where type='city' and type_id='{$item['id']}' and date='{$selected_date}' and resource='{$resource_item['id']}'");
            $item_query->execute();
            $item_query = $item_query->fetch();

            $controller->view_content_beta .= "
                    <tr>
                        <td>{$item['title']}</td>
                        <td><input type='number' value='{$item_query['price_1']}' name='price-city-{$item['id']}-first-{$resource_item['id']}' style='width:82px;'/></td>
                        <td><input type='number' value='{$item_query['price_2']}' name='price-city-{$item['id']}-second-{$resource_item['id']}' style='width:82px;'/></td>
                        <td><input type='number' value='{$item_query['price_final']}' name='price-city-{$item['id']}-final-{$resource_item['id']}' style='width:82px;'/></td>
                    </tr>
                ";
        }
        $controller->view_content_beta .= "
			</table>
            <table id='commodities-place-factor'>
                <tr>
					<td colspan='4'>وزن ها</td>
				</tr>
				<tr>
					<td>عنوان</td>
					<td style='width:93px;'>قیمت اول</td>
					<td style='width:93px;'>قیمت دوم</td>
					<td style='width:93px;'>قیمت نهایی</td>
				</tr>";
        foreach ($weights as $item) {
            $item_query = $controller->model->db->prepare("select * from prices_list where type='weight' and type_id='{$item['id']}' and date='{$selected_date}' and resource='{$resource_item['id']}'");
            $item_query->execute();
            $item_query = $item_query->fetch();

            $controller->view_content_beta .= "
                    <tr>
                        <td>{$item['title']}</td>
                        <td><input type='number' value='{$item_query['price_1']}' name='price-weight-{$item['id']}-first-{$resource_item['id']}' style='width:82px;'/></td>
                        <td><input type='number' value='{$item_query['price_2']}' name='price-weight-{$item['id']}-second-{$resource_item['id']}' style='width:82px;'/></td>
                        <td><input type='number' value='{$item_query['price_final']}' name='price-weight-{$item['id']}-final-{$resource_item['id']}' style='width:82px;'/></td>
                    </tr>
                ";
        }
        $controller->view_content_beta .= "</table>";
    }
    $controller->view_content_beta .= "
			<div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ثبت' style='background: #7A6D56;border: 1px solid #7A6D56;border-radius: 3px;color: #FFFFFF;float: right;font-family: byekan;font-size: 15px;font-weight: normal;height: 40px;text-align: center;width: calc(50% - 80px);float: left;margin: 30px;' /></div>
        </form>
";
}
$controller->view_content_beta .= "</div>";

$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>