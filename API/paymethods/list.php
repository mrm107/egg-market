<?php
$status = 0;
$data = ["موردی یافت نشد"];
if (!isset($controller)) {
    $controller = null;
}
$paymethod = $controller->model->db->prepare("SELECT * FROM paymethods WHERE published=1 ORDER BY `ordering` ASC");
$paymethod->execute();
$paymethods = $paymethod->fetchAll();
$query_num = count($paymethods);
if ($query_num) {
    $link = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"]."/".$_SERVER['REQUEST_URI'];
    $link = explode("/paymethods",$link);
    $link = $link[0];
    $data = [];
    $status = 200;
    foreach ($paymethods as $row) {
        $row = (object)$row;
        $out = [];
        $out["name"] = $row->fldname;
        $out["title"] = $row->fldcode1;
        $out["image"] = "Paymethods/images/$row->fldname.png";
    }
    $data["paymethods"] = $out;
    $data["payaction"] = [
        "payurl"=>"$link/paymethods/pay",
        "confirmurl"=>"$link/paymethods/confirm",
        "parameters"=>[
            "pay"=>[
                "paymethod"=>"STRING",
                "amount"=>"INT",
                "orderid"=>"INT",
                "callbackurl"=>"STRING"
            ],
            "confirm"=>[
                "paymethod"=>"STRING",
                "All Data Post Merge Array"
            ]
        ]
    ];
}

$export['status'] = $status;
$export['data'] = $data;

return $export;
