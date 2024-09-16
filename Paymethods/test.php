<?php
// No direct access to this file
function gotoPaymethod($paymethod, $OrderID, $price, $callbackUrl): array
{
    $price = intval($price);
    $MerchantID = $paymethod->fldcode2;
    $Merchaurlsend = $paymethod->fldcode3;

    if ($price) {
        $message = sprintf("شما در حال ارسال به %s می باشید.", $paymethod->fldcode1);
        $data = [];
        $data["method"] = "POST";
        $data["actionURL"] = $Merchaurlsend;
        $data["Amount"] = $price;
        $data["ResNum"] = $OrderID;
        $data["paymethod"] = $paymethod->fldname;
        $data["MID"] = $MerchantID;
        $data["RedirectURL"] = $callbackUrl;
        $out = ["status" => 1, "message" => $message, "params" => $data];
    } else {
        $message = 'مبلغ پرداختی صحیح نمی باشد';
        $out = ["status" => 0, "message" => $message];
    }
    return $out;
}

function acceptPayMethod($getdata, $paymethod): array
{
    $State = $getdata['State'] == "OK" ? 1 : 0;
    $OrderID = $getdata['ResNum'];
    $TRACENO = $getdata['RefNum'];
    $cardnumber = @$getdata["cardnumber"];
    $message = $State ? "پرداخت موفق" : "انصراف از پرداخت";
    $params = json_encode($getdata, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($State) {
        return ["status" => $State, "message" => $message, "cardnumber" => $cardnumber, "TRACENO" => $TRACENO, "OrderID" => $OrderID, "params" => $params];
    } else {
        return ["status" => $State, "message" => $message, "OrderID" => $OrderID, "params" => $params];
    }
}