<?php
include_once "library/sepehrlibs.php";
function gotoPaymethod($paymethod, $OrderID,$price,$callbackUrl): array
{
    $price = intval($price);
    $MerchantID = $paymethod->fldcode2;
    if ($price) {
        list($status,$data) = sepehrClass::setMabna($price, $MerchantID, $callbackUrl, $OrderID);
        if(!$status){
            return ["status" => 0, "message" => $data];
        }
        $message = sprintf("شما در حال ارسال به %s می باشید.",$paymethod->fldcode1);
        return ["status" => 1, "message" => $message,"params"=>$data];
    } else {
        $message = 'مبلغ پرداختی صحیح نمی باشد';
        return ["status" => 0, "message" =>$message];
    }
}

function acceptPayMethod($getdata,$paymethod): array
{
    $OrderID = $_POST['invoiceid'];
    $MerchantID = $paymethod->fldcode2;
    $params = json_encode($getdata, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    list($status, $cardnumber,$tracenumber,$message) = sepehrClass::checkTransaction($MerchantID);
    if($status == 1) {
        $message = "پرداخت موفق";
        return ["status"=>1,"message"=>$message,"cardnumber"=>$cardnumber,"TRACENO"=>$tracenumber,"OrderID"=>$OrderID,"params"=>$params];
    } else {
        return ["status"=>0,"message"=>$message,"OrderID"=>$OrderID,"params"=>$params];
    }
}