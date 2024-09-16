<?php
$status = 0;
$data = ["اطلاعات ارسالی صحیح نیست"];
if (!isset($controller)) {
    $controller = null;
}
$paymethod = @$_POST["paymethod"];
$callbackUrl = @$_POST["callbackurl"];
$price = intval(@$_POST["amount"]);
$OrderID = intval(@$_POST["orderid"]);

$paymethod = str_replace(["`"," "],"",$paymethod);
$OrderID = str_replace(["`"," "],"",$OrderID);
$paymethod = $controller->model->db->prepare("SELECT * FROM paymethods WHERE published=1 AND fldname='$paymethod'");
$paymethod->execute();
$paymethod = $paymethod->fetch();
if ($paymethod) {
    define("DS", DIRECTORY_SEPARATOR);
    $currentpath = dirname(__FILE__);
    $currentpath = explode(DS, $currentpath);
    array_pop($currentpath);
    array_pop($currentpath);
    $currentpath[] = "Paymethods";
    $currentpath = implode(DS, $currentpath);
    $paymethod = (object)$paymethod;
    if (file_exists($currentpath.DS."$paymethod->fldname.php")){
        include_once $currentpath.DS."$paymethod->fldname.php";
        $status = 200;
        $data = [];
        $return = gotoPaymethod($paymethod, $OrderID, $price, $callbackUrl);
        $data[] = $return;
    }
}

$export['status'] = $status;
$export['data'] = $data;

return $export;
