<?php
function check_token($token = NULL) {
    global $controller;

    if($controller->check_null_no($token) == NULL) {
        return false;
    } else {
        $check = $controller->model->db->prepare("SELECT customer FROM customers_tokens inner join customers on customers_tokens.customer = customers.id WHERE customers_tokens.token = :token and customers_tokens.active = 1 and customers.status = 'accepted' LIMIT 1");
        $check->execute(array(":token" => $token));
        if($check->rowCount() > 0) {
            $customer = $check->fetch();
            return $customer['customer'];
        } else {
            return false;
        }
    }
}