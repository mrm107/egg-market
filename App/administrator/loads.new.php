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
$controller->view->view_title = 'آگهی جدید';
$controller->checker_page_access(98);
$errors = '';
if (isset($_POST['submit']) || isset($_POST['submit_exit'])) {
    try {

        $statement = $controller->model->db->prepare("INSERT INTO `loads` (`origin_field1`, `origin_field2`, `reg_date`, `reg_client`, `reg_user`, `status`, `description`, `owner_name`, `weight`, `count`, `person_owner_name`, `print_type`, `yolk_type`, `box_type`, `stage_type`, `type`, `pack_type`, `reg_customer`, `price`, `quality`)
                                                                             VALUES (:origin_field1, :origin_field2, :reg_date, :reg_client, :reg_user, :status, :description, :owner_name, :weight, :count, :person_owner_name, :print_type, :yolk_type, :box_type, :stage_type, :type, :pack_type, :reg_customer, :price, :quality)");
        $statement->execute(array(
            ':origin_field1' => $_POST['origin_field1'],
            ':origin_field2' => utils__arabic_character_to_persian($controller->check_null_no($_POST['origin_field2'])),
            ':reg_date' => $controller->datetime,
            ':reg_client' => 'core',
            ':reg_user' => $controller->model->user_code,
            ':status' => $_POST['status'],
            ':description' => utils__arabic_character_to_persian($controller->check_null_no($_POST['description'])),
            ':owner_name' => utils__arabic_character_to_persian($controller->check_null_no($_POST['owner_name'])),
            ':weight' => utils__arabic_character_to_persian($controller->check_null_no($_POST['weight'])),
            ':count' => utils__arabic_character_to_persian($controller->check_null_no($_POST['count'])),
            ':print_type' => utils__arabic_character_to_persian($controller->check_null_no($_POST['print_type'])),
            ':yolk_type' => utils__arabic_character_to_persian($controller->check_null_no($_POST['yolk_type'])),
            ':box_type' => utils__arabic_character_to_persian($controller->check_null_no($_POST['box_type'])),
            ':stage_type' => utils__arabic_character_to_persian($controller->check_null_no($_POST['stage_type'])),
            ':person_owner_name' => utils__arabic_character_to_persian($controller->check_null_no($_POST['person_owner_name'])),
            ':type' => $_POST['type'],
            ':pack_type' => $_POST['pack_type'],
            ':reg_customer' => $controller->check_null($_POST['reg_customer']),
            ':price' => $controller->check_null_no($_POST['price']),
            ':quality' => $_POST['quality']
        ));
        $loadID = $controller->model->db->lastInsertId();

        $phone_arrays = [];
        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'phone-') !== false) {
                if ($controller->check_null_no($value) != NULL) {
                    $phone = utils__fatoen_numbers(utils__arabic_character_to_persian($controller->check_null_no($value)));
                    if (!in_array($phone, $phone_arrays)) {
                        $add_phone = $controller->model->db->prepare("INSERT INTO loads_phones (`load`, `phone`) VALUES (:load, :phone)");
                        $add_phone->execute(array(":load" => $loadID, ":phone" => utils__fatoen_numbers($phone)));
                        $phone_arrays[] = $phone;
                    }
                }
            }
        }

        if(isset($_POST['update_customer']) and $_POST['reg_customer']) {
            $statement = $controller->model->db->prepare("UPDATE `customers` SET `owner_name` = :owner_name, `person_owner_name` = :person_owner_name, `phone1` = :phone1, `phone2` = :phone2, `phone3` = :phone3, `phone4` = :phone4 WHERE `id` = :id ");
            $statement->execute(array(
                ":owner_name" => utils__arabic_character_to_persian($_POST['owner_name']),
                ":person_owner_name" => utils__arabic_character_to_persian($_POST['person_owner_name']),
                ":phone1" => utils__fatoen_numbers($_POST['phone-1']),
                ":phone2" => utils__fatoen_numbers($_POST['phone-2']),
                ":phone3" => utils__fatoen_numbers($_POST['phone-3']),
                ":phone4" => utils__fatoen_numbers($_POST['phone-4']),
                ':id' => $controller->check_null($_POST['reg_customer'])));
        }

        require ('../triggers/after-publish-load.php');
        $message_id = after_publish_load($loadID);

        $update = $controller->model->db->prepare("update loads set `telegram_message_id` = :message_id where id = :load_id");
        $update->execute(array(":message_id" => $message_id, ":load_id" => $loadID));

        if (isset($_POST['submit_exit'])) {
            $errors = "
                <script>
                    alert('وظیفه مورد نظر انجام شد .');
                    window.location.href = 'loads.view.php';
                </script>
            ";
        } else {
            $errors = "
            <script>
                alert('وظیفه مورد نظر انجام شد .');
                window.location.href = 'loads.new.php';
            </script>
        ";
        }
    } catch (PDOException $e) {
        $show_error = $e->getMessage();
        $errors = '<span class="bad-alert">خطای پایگاه داده</span><script type="text/javascript">alert("' . $show_error . '");</script>';
    }
}
$controller->set_sidebar();
$controller->view->view_nav = "<a class='plusmenu' href='loads.view.php'>بازگشت</a>" . $controller->set_fastaccess();
$controller->view->view_box_header = "<span title='آگهی ها -> آگهی جدید'>آگهی جدید {$errors}</span>";
$controller->view_content_beta .= "
	<div class='content-place-full' id='scroll-place'>
		<form method='post' id='newdoc' enctype='multipart/form-data' autocomplete='off' autofill='off' onsubmit='return confirm(\"از ثبت آگهی اطمینان دارید؟\");'>
		<div class='form-elements'>
		    <div>
                <h6>نوع</h6>
                <select name='type' required>
                    <option value='announcement' selected>اعلام بار</option>
                    <option value='request'>درخواست بار</option>
                </select>
            </div>
            <div>
                <h6>نوع بسته بندی</h6>
                <select name='pack_type' required>
                    <option value='bulk' selected>فله (شانه ای)</option>
                    <option value='box'>بسته بندی</option>
                </select>
            </div>
            <div>
                <h6>وزن/تعداد در کارتن</h6>
                <input type='text' name='weight' required>
            </div>
            <div>
                <h6>تعداد کارتن</h6>
                <input type='text' name='count' required>
            </div>
            <div>
                <h6>نوع زرده</h6>
                <select name='yolk_type'>
                    <option></option>
                    <option value='golden'>طلایی</option>
                    <option value='simple'>ساده</option>
                    <option value='corn'>ذرتی</option>
                </select>
            </div>
            <div>
                <h6>نوع پرینت</h6>
                <select name='print_type'>
                    <option></option>
                    <option value='with'>با پرینت</option>
                    <option value='without'>بدون پرینت</option>
                    <option value='ability'>با قابلیت پرینت</option>
                </select>
            </div>
            <div>
                <h6>نوع کارتن</h6>
                <input type='text' name='box_type'>
            </div>            
            <div>
                <h6>نوع شانه</h6>
                <input type='text' name='stage_type'>
            </div>
            <div>
                <h6>کیفیت</h6>
                <select name='quality'>
                    <option></option>
                    <option value='lux'>لوکس</option>
                    <option value='grade-1'>درجه ۱</option>
                    <option value='grade-2'>درجه ۲</option>
                    <option value='for-factories'>درجه ۳ (کارخانه‌ای)</option>
                </select>
            </div>
            <div>
                <h6>قیمت</h6>
                <input type='text' name='price'>
            </div>
            <div style='clear: right;'>
                <h6>محل: استان</h6>
			    <select name='origin_field1' id='load-origin-field1' required>
			        <option selected></option>";
$provinces = $controller->model->fetch_all("locations_provinces", "order by `title` ASC");
foreach ($provinces as $province) {
    $controller->view_content_beta .= "<option value='{$province['id']}'>{$province['title']}</option>";
}
$controller->view_content_beta .= "
                </select>   
            </div>
            <div>
                <h6>محل: منطقه</h6>
                <input type='text' name='origin_field2' placeholder='شهرک صنعتی فلان ...' id='load-origin-field2' maxlength='200' />
            </div>
            <div>
                <h6>وضعیت</h6>
                <select name='status' required>
                    <option value='accepted' selected>تایید شده</option>
                    <option value='rejected'>رد شده</option>
                    <option value='pending'>در انتظار</option>
                    <option value='deactive'>حذف شده</option>
                    <option value='sold'>فروخته شده</option>
                    <option value='expired'>منقضی شده</option>
                </select>
            </div>
            <div>
                <h6>توضیحات</h6>
                <textarea name='description'></textarea>
            </div>
            <div>
                <h6>جستجو</h6>
                <input type='text' name='customer_search' placeholder='مجموعه، شخص، تلفن و ...' id='customer_search' />
            </div>
            <div>
                <h6>مشتری متصل</h6>
                <input name='reg_customer' type='hidden' />
			    <select name='reg_customer_show' disabled>
			        <option selected></option>";
$customers = $controller->model->db->prepare("select id,mobile,name,reg_date,reg_client,confirm_code,description,status,registrar,owner_name,person_owner_name,phone1,phone2,phone3,phone4 from customers where `status` = 'accepted' order by `name` ASC");
$customers->execute();
$customers = $customers->fetchAll();
foreach ($customers as $customer) {
    $controller->view_content_beta .= "<option value='{$customer['id']}'>{$customer['name']}</option>";
}
$controller->view_content_beta .= "
                </select>   
            </div>
            <div style='clear:right'>
                <h6>نام مجموعه</h6>
                <input type='text' name='owner_name'>
            </div>
            
            <div>
                <h6>نام شخص</h6>
                <input type='text' name='person_owner_name'>
            </div>";
for ($i = 1; $i <= 4; $i++) {
    $req = $i == 1 ? 'required' : '';
    $controller->view_content_beta .= "<div><h6>شماره تماس {$i}</h6><input type='text' name='phone-{$i}' {$req}></div>";
}
$controller->view_content_beta .= "   
            <div><h6>آپدیت مشتری؟</h6><input type='checkbox' name='update_customer' checked style='padding: 0;margin: 0;width: 30px;height: 30px;'/></div>
            <div><h6>&nbsp;</h6></div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ذخیره و جدید' /></div>
            <div><h6>&nbsp;</h6><input type='submit' class='submit' name='submit_exit' value='ذخیره و خروج' /></div>
		</div>
		</form>
	</div>";
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->view_header = "
<link rel='stylesheet' type='text/css' href='../../Template/plugin/EasyAutocomplete-1.3.5/easy-autocomplete.css'>
<link rel='stylesheet' type='text/css' href='../../Template/plugin/EasyAutocomplete-1.3.5/easy-autocomplete.themes.css'>";
$controller->view->view_footer = "
<script type='text/javascript' src='../../Template/plugin/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js'></script>
<script> 
var provinces = JSON.parse('".json_encode($provinces)."');
var easyAutocompleteWrapper = function(elementID) {
    var options = {
      url: function(phrase) {
        return '{$controller->conf_database->conf_address}{$controller->conf_database->conf_dir}API/locations/area_suggestion';
      },
      listLocation: 'suggestions',
      getValue: 'title',
      ajaxSettings: {
        contentType: 'application/json',
        method: 'POST',
        data: {}
      },
      preparePostData: function(data) {
        data.area = $('#'+elementID+'2').val();
        data.province = $('#'+elementID+'1').val();
        return JSON.stringify(data);
      },
      requestDelay: 0,
      highlightPhrase: false,
      template: {
        type: 'custom',
        method: function(value, item) {
            var parent = provinces.find(province => province.id === item.province).title;
            return item.title + ' - ' + '<span>'+ parent +'</span>';
        }
      }, 
      theme: 'plate-dark',
      list: {
        maxNumberOfElements: 20,
        onChooseEvent: function() {
          var data = $('#'+elementID+'2').getSelectedItemData();
          $('#'+elementID+'1').val(data.province);
        }
      }
    };
    $('#'+elementID+'2').easyAutocomplete(options);
}
easyAutocompleteWrapper('load-origin-field');


var customers = JSON.parse('".json_encode($customers)."');
var options2 = {
    data: customers,
    getValue: 'id',
    requestDelay: 0,
    highlightPhrase: true,
    template: {
        type: 'custom',
        method: function(value, item) {
            return '<div style=\'white-space: nowrap;\'>' + item.name + ' - ' + '<span>['+ item.owner_name +']</span>'  + ' <span>['+ item.phone1 +']</span></div>';
        }
    }, 
    theme: 'plate-dark',
    list: {
        maxNumberOfElements: 20,
        onChooseEvent: function() {
            var cust_id = $('#customer_search').val();
            var cust_item = customers.find(customer => customer.id === cust_id);

            $('input[name=reg_customer]').val(cust_id);
            $('select[name=reg_customer_show]').val(cust_id);
            $('input[name=owner_name]').val(cust_item.owner_name);
            $('input[name=person_owner_name]').val(cust_item.person_owner_name);
            $('input[name=phone-1]').val(cust_item.phone1);
            $('input[name=phone-2]').val(cust_item.phone2);
            $('input[name=phone-3]').val(cust_item.phone3);
            $('input[name=phone-4]').val(cust_item.phone4);
        },
        match: {
            enabled: true,
            method: function(element, phrase) {
                var item = customers.find(customer => customer.id === element);
                if(JSON.stringify(item).search(phrase) > -1) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
};
$('#customer_search').easyAutocomplete(options2);
</script>";
$controller->view->show('../../Template/admin.index.php');
?>