<?php
function utils__entofa_numbers($text)
{
    $persian_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $english_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $text = str_replace($english_digits, $persian_digits, $text);
    return $text;
}

function utils__fatoen_numbers($text)
{
    $english_digits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $persian_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $text = str_replace($english_digits, $persian_digits, $text);
    return $text;
}

function utils__arabic_character_to_persian($string)
{
    $characters = [
        'ك' => 'ک',
        'دِ' => 'د',
        'بِ' => 'ب',
        'زِ' => 'ز',
        'ذِ' => 'ذ',
        'شِ' => 'ش',
        'سِ' => 'س',
        'ى' => 'ی',
        'ي' => 'ی',
        '١' => '۱',
        '٢' => '۲',
        '٣' => '۳',
        '٤' => '۴',
        '٥' => '۵',
        '٦' => '۶',
        '٧' => '۷',
        '٨' => '۸',
        '٩' => '۹',
        '٠' => '۰',
    ];
    return str_replace(array_keys($characters), array_values($characters),$string);
}