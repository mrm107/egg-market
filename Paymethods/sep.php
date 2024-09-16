<?php

function gotoPaymethod($paymethod, $OrderID, $price, $callbackUrl): array
{
    $price = intval($price);
    $MerchantID = $paymethod->fldcode2;
    if ($price) {
        list($status, $data) = getToken($price, $MerchantID, $callbackUrl, $OrderID);
        if (!$status) {
            return ["status" => 0, "message" => $data];
        }
        $message= sprintf("شما در حال ارسال به %s می باشید.", $paymethod->fldcode1);
        return ["status" => 1, "message" => $message, "params" => $data];
    } else {
        $message = 'مبلغ پرداختی صحیح نمی باشد';

        return ["status" => 0, "message" => $message];
    }
}

function acceptPayMethod($getdata, $paymethod): array
{
    $State = $getdata['State'];
    $TerminalId = $getdata['TerminalId'];
    $RefNum = $getdata['RefNum'];
    $TraceNo = $getdata['TraceNo'];
    $OrderID = $getdata['ResNum'];
    $MerchantID = $paymethod->fldcode2;

    $message = "اطلاعات ارسالی صحیح نیست";
    $out = [];
    if ($TerminalId == $MerchantID) {
        if (strtolower($State) == 'ok') {
            $post = [
                'TerminalNumber' => $MerchantID,
                'RefNum' => $RefNum,
                'CellNumber' => '',
                'NationalCode' => '',
                'IgnoreNationalcode' => true
            ];
            $header = [
                'Content-Type: application/json',
                'Accept: application/json',
                'Cache-Control: no-cache'
            ];
            $url = 'https://sep.shaparak.ir/verifyTxnRandomSessionkey/ipg/VerifyTransaction';

            $response = CallCurlAPI($url, $post, $header);
            $error = $response["error"];
            $verify = json_decode($response["response"]);
            if (is_object($verify)) {
                if ($verify->ResultCode == 0) {
                    if ($TraceNo) {
                        $cardnumber = $getdata["SecurePan"];
                        $params = json_encode($getdata, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        $message = sprintf("پرداخت شما با کد رهگیری %s", $TraceNo);
                        $out = ["status" => 1, "message" => $message, "cardnumber" => $cardnumber, "TRACENO" => $TraceNo, "OrderID" => $OrderID, "params" => $params];
                    } else {
                        $out = ["status" => 0, "message" => $message];
                    }
                } else {
                    $errMsg = @$verify->errorDesc ?: (@$verify->ResultCode ?: 'NoErrorCode?');
                    $message = sprintf('پرداخت ناموفق بوده است : %s', $errMsg);
                    $out = ["status" => 0, "message" => $message];
                }
            } else {
                $errMsg = $error ?: 'UnExpected Error!';
                $message = sprintf('پرداخت ناموفق بوده است : %s', $errMsg);
                $out = ["status" => 0, "message" => $message];
            }
        } else {
            $message = 'پرداخت از سوی کاربر لغو شد';
            $out = ["status" => 0, "message" => $message];
        }
    }
    return $out;
}

function getToken($price, $MerchantID, $callbackUrl, $OrderID): array
{
    $header = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Cache-Control: no-cache'
    ];
    $post = [
        'Action' => 'Token',
        'TerminalId' => $MerchantID,
        'Amount' => $price,
        'ResNum' => $OrderID,
        'RedirectUrl' => $callbackUrl
    ];

    $url = 'https://sep.shaparak.ir/OnlinePG/OnlinePG';
    $response = CallCurlAPI($url, $post, $header);
    $error = $response["error"];
    $token = json_decode($response["response"]);
    $status = 0;
    if (is_object($token)) {
        if (isset($token->token)) {
            $status = 1;
            $data = [];
            $data["method"] = "POST";
            $data["actionURL"] = "https://sep.shaparak.ir/OnlinePG/OnlinePG";
            $data["Token"] = $token->token;
        } else {
            $data = 'خطا در دریافت توکن پرداخت سامان کیش : ' . $token->errorDesc;
        }
    } elseif ($error) {
        $data = 'خطای اتصال به درگاه سامان کیش : ' . $error;
    } else {
        $data = 'خطای غیر منتظره در اتصال به درگاه سامان کیش';
    }

    return [$status, $data];
}

function CallCurlAPI($url, $post, $header): array
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    return ["response" => $response, "error" => $error];
}