<?php

class sepehrClass
{
    static function test_input($data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }

    public static function setMabna($Amount, $terminal, $redirectAddress, $invoiceNumber): array
    {
        $amount = trim($Amount);
        $terminal = trim($terminal);

        $redirectAddress = self::test_input($redirectAddress);
        $dataQuery = "Amount=$amount&callbackURL=$redirectAddress&InvoiceID=$invoiceNumber&TerminalID=$terminal&Payload=";
        $AddressServiceToken = "https://sepehr.shaparak.ir:8081/V1/PeymentApi/GetToken";
        $TokenArray = self::makeHttpChargeRequest($dataQuery, $AddressServiceToken);
        $decode_TokenArray = json_decode($TokenArray);

        $Status = @$decode_TokenArray->Status;
        $AccessToken = @$decode_TokenArray->Accesstoken;
        $url = 'sepehr.shaparak.ir';
        $ip = gethostbyname($url);
        if ($url != $ip && !empty($AccessToken) && $Status == 0) {
            $AddressIpgPay = "https://sepehr.shaparak.ir:8080/pay";
            $setPayment = [];
            $setPayment["method"] = "POST";
            $setPayment["actionURL"] = $AddressIpgPay;
            $setPayment["TerminalID"] = $terminal;
            $setPayment["token"] = $AccessToken;
            $setPayment["paymethod"] = "sepehr";
            return [1, $setPayment];
        } else {
            if ($Status) {
                $Status = 'خطا در ساخت توکن <br/> کد خطا :' . $Status;
            } else {
                $Status = 'پورت 8081 در هاست شما بسته است !';
            }
            $message = "ارتباط بانک برقرار نیست : " . $Status;

            return [0, $message];
        }
    }

    static function makeHttpChargeRequest($_Data, $_Address): bool|string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $_Address);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_Data);
        $result = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($error) {
            $result = $error;
        }

        return $result;

    }

    public static function getMabna($POST, $terminal, $counter): array
    {
        $terminal = trim($terminal);
        $out = [0, ""];
        if (isset($POST['respcode']) && $POST['respcode'] == '0') {
            $params = 'digitalreceipt=' . $POST['digitalreceipt'] . '&Tid=' . $terminal;
            $URL = 'https://sepehr.shaparak.ir:8081/V1/PeymentApi/Advice';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($ch);

            curl_close($ch);
            $result = json_decode($res, true);
            $err = null;
            if (strtoupper($result['Status']) == 'OK') {
                $amount = intval(@$_POST["amount"]);
                if ($result['ReturnId'] == $amount) {
                    $referenceId = $POST['digitalreceipt'];
                    $out = [1, "با تشکر پرداخت شما با موفقت انجام شد. شماره تراکنش پرداخت: " . $referenceId, $referenceId];
                } else {
                    $err = [0, 'مبلغ واریزی با قیمت محصول برابر نیست'];
                }
            } else {
                $result["counter"] = $counter;
                $err = match ($result['ReturnId']) {
                    '-1' => 'تراکنش پیدا نشد',
                    '-2' => 'تراکنش قبلا Reverse شده است',
                    '-3' => 'خطا عمومی',
                    '-4' => 'امکان انجام درخواست برای این تراکنش وجود ندارد',
                    '-5' => 'آدرس IP پذیرنده نامعتبر است',
                    default => 'خطای ناشناس : ' . $result['ReturnId'],
                };

            }
            if ($err) {
                $out = [0, $err];
            }

        } else {
            $out = [0, $POST['respmsg']];
        }
        return $out;
    }

    static function checkTransaction($MerchantID): array
    {

        $respcode = $_POST['respcode'];
        $respmsg = $_POST['respmsg'];
        $cardnumber = $_POST['cardnumber'];
        $tracenumber = $_POST['tracenumber'];

        if ($respcode == '0') {
            $counter = 0;
            $payResult = self::getMabna($_POST, $MerchantID, $counter);
        } else {
            $payResult[0] = 0;
            $payResult[1] = $respmsg;
        }


        if ($payResult[0]) {
            $data = [1, $cardnumber, $tracenumber, $respmsg];
        } else {
            $message = $payResult[1] ?: $respmsg;
            $data = [0, "", "", $message];
        }

        return $data;
    }
}