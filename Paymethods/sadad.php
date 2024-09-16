<?php
function gotoPaymethod($paymethod, $OrderID, $price, $callbackUrl): array
{
    $price = intval($price);
    $MerchantID = trim($paymethod->fldcode2);
    $TerminalId = trim($paymethod->fldcode3);
    $MerchantKey = trim($paymethod->fldcode4);
    $LocalDateTime = date("m/d/Y g:i:s a");
    if ($price) {
        $SignData = encrypt_pkcs7("$TerminalId;$OrderID;$price", "$MerchantKey");
        $data = array(
            'TerminalId' => $TerminalId,
            'MerchantId' => $MerchantID,
            'Amount' => $price,
            'SignData' => $SignData,
            'ReturnUrl' => $callbackUrl,
            'LocalDateTime' => $LocalDateTime,
            'OrderId' => $OrderID
        );
        $result = CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest', $data);

        if (is_object($result) && $result->ResCode == 0) {
            $Token = $result->Token;
            $url = "https://sadad.shaparak.ir/VPG/Purchase?Token=$Token";
            $message = sprintf("شما در حال ارسال به %s می باشید.", $paymethod->fldcode1);
            $params = [];
            $params["method"] = "GET";
            $params["actionURL"] = $url;
            $out = ["status" => 1, "message" => $message, "params" => $params];
        } elseif (is_object($result)) {
            $message = $result->Description;
            $out = ["status" => 0, "message" => $message];
        } else {
            $message = $result;
            $out = ["status" => 0, "message" => $message];
        }
    } else {
        $message = 'مبلغ پرداختی صحیح نمی باشد';
        $out = ["status" => 0, "message" => $message];
    }
    return $out;
}

function acceptPayMethod($getdata, $paymethod): array
{
    $Token = $getdata['token'];
    $ResCode = $getdata['ResCode'];
    $MerchantKey = $paymethod->fldcode4;
    $out = [];
    if ($ResCode == 0) {
        $verifyData = array(
            'Token' => $Token,
            'SignData' => encrypt_pkcs7($Token, $MerchantKey)
        );

        $result = CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify', $verifyData);
        if ($result->ResCode != -1 && $result->ResCode == 0) {
            $tracenumber = $result->SystemTraceNo;
            $cardnumber = $result->CardHolderFullName;
            $params = json_encode($result, JSON_UNESCAPED_UNICODE);
            $message = sprintf("پرداخت شما با کد رهگیری %s تایید شد", $tracenumber);
            $out = ["status" => 1, "message" => $message, "cardnumber" => $cardnumber, "TRACENO" => $tracenumber, "params" => $params];
        } else {
            $params = json_encode($result, JSON_UNESCAPED_UNICODE);
            $message = "تراکنش نا موفق بود در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمی گردد.";
            $out = ["status" => 0, "message" => $message, "params" => $params];
        }
    }
    return $out;
}

function encrypt_pkcs7($str, $key): string
{
    $key = base64_decode($key);
    $ciphertext = OpenSSL_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);

    return base64_encode($ciphertext);
}

function CallAPI($url, $data = false)
{
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        return !empty($result) ? json_decode($result) : false;
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}
