<?php
function utils__generate_confirm_code()
{
    $code = mt_rand(1000, 9999);
    return $code;
}

function utils__generate_token()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#@';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 200; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}