<?php
require('user-ip.php');

function utils__user_logger($table, $record, $action, $description, $new_value, $previous_value)
{
    global $controller;
    $ip = utils__get_user_ip();
    $update_activity_user = $controller->model->db->prepare("INSERT INTO `users_activities` (`user`, `date`, `ip`, `agent`, `table`, `record`, `action`, `description`, `new_value`, `previous_value`) 
                                                                                                VALUES (:user, :date, :ip, :agent, :table, :record, :action, :description, :new_value, :previous_value);");
    $update_activity_user->execute(array(
        ':user' => $controller->model->user_code,
        ':date' => $controller->datetime,
        ':ip' => $ip,
        ':agent' => $_SERVER['HTTP_USER_AGENT'],
        ':table' => $table,
        ':record' => $record,
        ':action' => $action,
        ':description' => $description,
        ':new_value' => $new_value,
        ':previous_value' => $previous_value
    ));

}
