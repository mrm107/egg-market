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
$controller->view->view_title = 'روش پرداخت';
$controller->set_sidebar();
$controller->checker_page_access(85);
$errors = '';
//paymethods start

$default_show_paymethod = 30;

if (isset($_POST['fldname'])) {
    $Paymethods = [];
    $ordering = 1;
    $modified = date("Y-m-d HH:i:s");
    $update = $controller->model->db->prepare("DELETE FROM paymethods");
    $update->execute();

    foreach ($_POST['fldname'] as $k => $v)
    {
        $k                        = explode("-", $k);
        $Paymethods[$k[0]][$k[1]] = $v;
        if (!isset($Paymethods[$k[0]]["ordering"]))
        {
            $Paymethods[$k[0]]["ordering"] = $ordering++;
            $Paymethods[$k[0]]["published"] = 0;
        }
    }
    foreach ($_POST['published'] as $k => $v)
    {
        $k                        = explode("-", $k);
        $Paymethods[$k[0]][$k[1]] = $v;
    }
    $fields = ["fldname","fldcode1","fldcode2","fldcode3","fldcode4","fldcode5","fldcode6","fldcode7","fldcode8","created","published","ordering"];
    foreach ($Paymethods as $k => $v)
    {
        $data               = array();
        $data["fldname"]    = $k;
        $data["fldcode1"]   = "";
        $data["fldcode2"]   = "";
        $data["fldcode3"]   = "";
        $data["fldcode4"]   = "";
        $data["fldcode5"]   = "";
        $data["fldcode6"]   = "";
        $data["fldcode7"]   = "";
        $data["fldcode8"]   = "";
        $data["created"]    = $modified;
        $data["published"]   = 0;
        $data["ordering"]   = 0;
        $j                  = 1;
        foreach ($v as $k1 => $v1)
        {
            if (!in_array($k1,["published","ordering"]))
            {
                $data["fldcode" . ($j++)] = $v1;
            }
            elseif ($k1=="published") {
                $data["published"] = $v1;
            }
            elseif ($k1=="ordering") {
                $data["ordering"] = $v1;
            }
        }
        $update = $controller->model->db->prepare("INSERT IGNORE INTO paymethods (`".implode("`,`",$fields)."`) VALUES ('".implode("','",$data)."')");
        $update->execute();
    }
}
$show = $default_show_paymethod;

$controller->view->view_nav = "
<a>روش پرداخت</a>
<a class='plusmenu' href='index.php'>بازگشت</a>" . $controller->set_fastaccess();

$paymethod = $controller->model->db->prepare("select * from paymethods ORDER BY `ordering` ASC");
$paymethod->execute();
$status = '';
$realstatus = $status;

$paymethods = $paymethod->fetchAll();

$controller->view_box_header_beta = "";
$controller->view_box_header_beta .= "
	<span title='روش پرداخت'></span>
	{$errors}";
$controller->view->view_box_header = $controller->view_box_header_beta;
$controller->view_content_beta .= "
		<div class='content-place-full' id='scroll-place'>
			<form method='post' action='' id='content'>
				";
$query_num = count($paymethods);
$show_num = $show;
$show_counter = 0;
define("DS", DIRECTORY_SEPARATOR);
$currentpath = dirname(__FILE__);
$currentpath = explode(DS, $currentpath);
array_pop($currentpath);
array_pop($currentpath);
$currentpath = implode(DS, $currentpath);
$currentpath.=DS."Paymethods";
if (is_dir($currentpath)) {
    $files = scandir($currentpath);
    foreach ($files as $key => $file) {
        if (!preg_match("/\.xml$/", $file)) {
            unset($files[$key]);
        }
    }
    if (count($files)) {
        $PayMethod = new stdClass;
        $PayMethod->fldname = array();
        $PayMethod->published = array();
        foreach ($paymethods as $row) {
            $row = (object)$row;
            $PayMethod->fldname[$row->fldname] = $row->fldname;
            $PayMethod->published[$row->fldname] = $row->published;
            $PayMethod->fldcode1[$row->fldname] = $row->fldcode1;
            $PayMethod->fldcode2[$row->fldname] = $row->fldcode2;
            $PayMethod->fldcode3[$row->fldname] = $row->fldcode3;
            $PayMethod->fldcode4[$row->fldname] = $row->fldcode4;
            $PayMethod->fldcode5[$row->fldname] = $row->fldcode5;
            $PayMethod->fldcode6[$row->fldname] = $row->fldcode6;
            $PayMethod->fldcode7[$row->fldname] = $row->fldcode7;
            $PayMethod->fldcode8[$row->fldname] = $row->fldcode8;
            $PayMethod->ordering[$row->fldname] = $row->ordering;
            $PayMethod->id[$row->fldname] = $row->id;
        }

        $controller->view_content_beta .= '<table>';
        $controller->view_content_beta .= '<tbody>';
        $controller->view_content_beta .= '<tr>';
        $controller->view_content_beta .= '<td align="center">نماد</td>';
        $controller->view_content_beta .= '<td align="center">روش پرداخت</td>';
        $controller->view_content_beta .= '<td align="center">وضعيت استفاده</td>';
        $controller->view_content_beta .= '<td align="center">اطلاعات</td>';
        $controller->view_content_beta .= '</tr>';
        $Y = $PayMethod->fldname;
        $Z = $PayMethod->published;

        $tmp = [];
        $flipfiles = array_flip($files);
        $ordering = 0;
        if (isset($PayMethod->ordering)) {
            foreach ($PayMethod->ordering as $file => $ordering) {
                $file = "$file.xml";
                if (isset($flipfiles[$file])) {
                    $tmp[] = $files[$flipfiles[$file]];
                }
            }
        }

        foreach ($files as $file) {
            if (!in_array($file, $tmp)) {
                $tmp[] = $file;
                $ordering++;
                $file = str_replace(".xml", "", $file);
                $PayMethod->ordering[$file] = $ordering;
                $PayMethod->id[$file] = 0;
            }
        }
        $files = $tmp;
        $xml = array();
        $ordering = 1;
        foreach ($files as $kf => $file) {
            $xml = simplexml_load_file($currentpath . DS . $file);
            if (!$xml) {
                continue;
            }
            $data = array();
            $file = str_replace('.xml', '', $file);
            if (!isset($Z[$file])) {
                $Z[$file] = 0;
            }
            $pv = (int)@$Z[$file];
            $element = (isset($PayMethod->fldcode1[$file]) && strlen($PayMethod->fldcode1[$file])) ? $PayMethod->fldcode1[$file] : $xml->name;
            $data['name'] = $element;
            $element = $xml->image;
            $data['image'] = $element ?: 'ندارد';
            $element = $xml->version;
            $data['version'] = $element;
            $element = $xml->description;
            $data['description'] = $element;
            $element = (array)$xml->fields->field;
            $A = (array)$xml->fields;
            $n = count($A["field"]);
            $data['fields'] = array();
            if ($n > 1) {
                foreach ($A["field"] as $kc => $vc) {
                    $vc = (array)$vc;
                    $data['fields'][] = $vc["@attributes"];
                }
            } else {
                $X = (array)$A["field"];
                $data['fields'][] = $X["@attributes"];
            }
            $controller->view_content_beta .= '<tr>';
            $controller->view_content_beta .= '<td>';
            $controller->view_content_beta .= '<div align="center"><img class="inputbox_mydomain" width="64" height="64" src="../../Paymethods/' . $data['image'] . '" /></div>';
            $controller->view_content_beta .= '</td>';
            $controller->view_content_beta .= '<td>';
            $controller->view_content_beta .= '<div class="form-elements"><input type="text" name="fldname[' . $file . '-name]" value="' . $data['name'] . '" style="width: calc(100% - 10px);"/></div>';
            $controller->view_content_beta .= '</td>';
            $controller->view_content_beta .= '<td>';
            $controller->view_content_beta .= '<div>';
            $controller->view_content_beta .= '<select name="published[' . $file . '-published]">';
            $controller->view_content_beta .= '<option value="0"' . ($Z[$file] ? "" : " selected") . '>' . 'خیر' . '</option>';
            $controller->view_content_beta .= '<option value="1"' . ($Z[$file] ? " selected" : "") . '>' . 'بلی' . '</option>';
            $controller->view_content_beta .= '</select>';
            $controller->view_content_beta .= '<input type="hidden" name="ordering[' . $file . '-published]" value="' . $ordering . '">';
            $controller->view_content_beta .= '</div>';
            $controller->view_content_beta .= '</td>';
            $controller->view_content_beta .= '<td>';
            $ordering++;
            $n = count($data['fields']);
            $XField = array();
            $controller->view_content_beta .='<table>';
            foreach ($data['fields'] as $k => $X) {
                eval('$value = @$PayMethod->fldcode' . ($k + 2) . "[\$file];");
                $id = "fldname_" . $X["name"] . "_$k" . "_$file";
                $XField = '';
                $dir = "ltr";
                $size = isset($X["size"]) ? $X["size"] : "";
                $default = isset($X["default"]) ? $X["default"] : "";
                $value = (isset($value) && strlen($value)) ? $value : $default;
                $cols = isset($X["cols"]) ? $X["cols"] : "50";
                $rows = isset($X["rows"]) ? $X["rows"] : "8";
                switch ($X["type"]) {
                    case "hidden":
                        $XField .= "<input type=\"hidden\" name=\"fldname[$file" . "-" . "" . $X["name"] . "]\" id=\"$id\" value=\"$value\">";
                        $controller->view_content_beta .= $XField;
                        break;
                    case "textarea":
                        $controller->view_content_beta .='<tr>';
                        $controller->view_content_beta .= '<td>'. $X["lable"] . "</td>";
                        $controller->view_content_beta .= '<td><div class="form-elements">'."<textarea style=\"width: calc(100% - 10px);direction:$dir;\" cols=\"$cols\" rows=\"$rows\" name=\"fldname[$file" . "-" . "" . $X["name"] . "]\">$value</textarea></div></td>";
                        $controller->view_content_beta .='</tr>';
                        break;
                    default:
                        $controller->view_content_beta .='<tr>';
                        $controller->view_content_beta .= '<td>'. $X["lable"] . "</td>";
                        $controller->view_content_beta .= '<td><div class="form-elements">'."<input style=\"width: calc(100% - 10px);direction:$dir;\" size=\"$size\" type=\"text\" name=\"fldname[$file" . "-" . "" . $X["name"] . "]\" id=\"$id\" value=\"$value\"></div></td>";
                        $controller->view_content_beta .='</tr>';
                        break;
                }
            }
            $controller->view_content_beta .='</table>';
            $controller->view_content_beta .= '</td>';
            $controller->view_content_beta .= '</tr>';
        }
        $controller->view_content_beta .= '<tr>';
        $controller->view_content_beta .= '<td colspan="4" align="center">';
        $controller->view_content_beta .= '<input type="submit" value="ذخیره تغییرات" >';
        $controller->view_content_beta .= '</td>';
        $controller->view_content_beta .= '</tr>';
        $controller->view_content_beta .= '</tbody>';
        $controller->view_content_beta .= '</table>';
    } else {
        $controller->view_content_beta .= '<center><h1>هیچ روش پرداختی یافت نشد</h1></center>';
    }
}

$controller->view_content_beta .= "
			</form>
		</div>";
//paymethods end
$controller->view->view_content = $controller->view_content_beta;
$controller->view->view_name = $controller->view_name;
$controller->view->show('../../Template/admin.index.php');
?>