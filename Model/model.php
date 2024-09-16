<?php

class model
{

    public $conf_database;
    public $db;
    public $user_respon;
    public $user_code;
    public $view_name;
    public $user_fastaccess;
    public $avatar_user;
    public $user_sex;
    public $user_status;

    public function __construct()
    {
        $this->conf_database = new base_config;
        $this->db = new PDO('mysql:dbname=' . $this->conf_database->conf_database . ';host=' . $this->conf_database->conf_host, $this->conf_database->conf_username, $this->conf_database->conf_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function __destruct()
    {
        $this->db = null;
    }

    public function checker_session($session)
    {
        if (trim($session) == ' ' or trim($session) == '' or trim($session) == NULL)
            return false;
        $statement = $this->db->prepare("SELECT * FROM users WHERE `session` = :session");
        $statement->execute(array(':session' => $session));
        if ($statement->rowCount() == 0)
            return false;
        else {
            $row = $statement->fetch();
            $this->user_code = $row['id'];
            $this->user_respon = $row['responsibility'];
            $this->view_name = $row['firstname'] . ' ' . $row['lastname'];
            $this->user_fastaccess = $row['fast_access'];
            $this->user_status = $row['status'];
            return true;
        }
    }

    public function get_count_db($table, $where)
    {
        $statement = $this->db->prepare("select * from $table where $where");
        $statement->execute();
        return $statement->rowCount();
    }

    public function fetch_one($table, $where)
    {
        $statement = $this->db->prepare("select * from $table $where");
        $statement->execute();
        $result = $statement->fetch();
        return $result;
    }

    public function fetch_all($table, $where)
    {
        $statement = $this->db->prepare("select * from $table $where");
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }
}

?>