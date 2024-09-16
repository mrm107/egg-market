<?php
session_start();
ob_start();

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>سامانه مرغداران ایران | ورود</title>
    <link rel="stylesheet" type="text/css" href="../Template/css/reset.v.1.css">
    <link rel="stylesheet" type="text/css" href="../Template/css/semantic.v.1.0.css">
    <link rel="stylesheet" type="text/css" href="../Template/css/general.v.1.0.css">
    <link rel="icon" type="image/png" href="../Template/images/achar.64x64.v1.png"/>
    <script type="text/javascript" src="../Template/javascript/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="../Template/javascript/semantic.v.1.0.js"></script>
    <script type="text/javascript" src="../Template/javascript/general.v.1.0.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<?php
require('../Inc/config.inc.php');
require('../Controller/admin.controller.php');
require('../Model/model.php');
require('../View/view.php');
require('../Plugin/jdatetime.inc.php');
require('../Util/user-logger.php');
$controller = new controller;
$controller->checker_session_isset('valid_user');
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = openssl_digest($_POST['password'], 'sha512');

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    $local_access = false;
    if($controller->conf_database->conf_address == 'http://localhost') {
        $local_access = true;
    }

    if(!$local_access) {
        $post_data = http_build_query(
            array(
                'secret' => '6Lc0l20UAAAAAB9c1pACR9eMCJil41uGhR0eAxrV',
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);
    }

    if ($local_access || $result->success) {
        $_SESSION['security_code'] = "";
        $statement = $controller->model->db->prepare("SELECT * FROM users WHERE email = :username AND `status` <> 'deleted'");
        $statement->execute(array(':username' => $username));
        if ($statement->rowCount() == 0) {
            $login_status = false;
        } else {
            $row = $statement->fetch();
            if ($row['password'] == $password) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?*-_./\@#$%&';
                do {
                    $randomString = '';
                    for ($i = 0; $i < 30; $i++) {
                        $randomString .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    $string = microtime(true) . date('YmdHis') . $randomString;
                    $randomString = '';
                    for ($i = 0; $i < 40; $i++) {
                        $randomString .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    $string = $randomString . $string;
                    $check = $controller->model->db->prepare("SELECT * FROM users_sessions WHERE id = :id");
                    $check->execute(array(":id" => $string));
                } while ($check->rowCount() > 0);
                $update = $controller->model->db->prepare("UPDATE users SET `session` = :sid WHERE id = :id");
                $update->execute(array(":sid" => $string, ":id" => $row['id']));
                $update = $controller->model->db->prepare("INSERT INTO users_sessions (`id`) VALUES (:sid)");
                $update->execute(array(":sid" => $string));
                $_SESSION['valid_user'] = $string;

                $login_status = true;
            } else
                $login_status = false;
        }

        if ($login_status === true) {
            utils__user_logger(NULL, NULL, NULL, 'لاگین', 'موفق', $username);
            header('Location: ' . $controller->conf_database->conf_address . $controller->conf_database->conf_dir . 'App/administrator/');
        } else {
            utils__user_logger(NULL, NULL, NULL, 'لاگین', 'ناموفق - نام کاربری یا رمز عبور اشتباه', NULL);
            $view_alert = 'نام کاربری و یا رمز عبور اشتباه است ، لطفا دقت کنید !';
        }
    } else {
        utils__user_logger(NULL, NULL, NULL, 'لاگین', 'ناموفق - کلمه امنیتی اشتباه', NULL);
        $view_alert = 'کلمه امنیتی اشتباه است ، لطفا دقت کنید !';
    }
    echo "<script type='text/javascript'>alert('" . $view_alert . "');</script>";
}
?>
<section class="login-box">
    <div class="custome-box" id="logo-place">
        <img src="../Template/images/achar.64x64.v1.png" id="achar-logo"/>
        <h3>سامانه مرغداران ایران</h3>
        <h5>هسته خدمات</h5>
    </div>
    <div class="custome-box">
        <form method="post" action="#" id="content">
            <div class="form-elements" style="margin-top:30px;">
                <div style="width:100%;"><h6>ایمیل : </h6><input type='text' name='username' tabindex='1' required/></div>
                <div style="width:100%;"><h6>رمز عبور : </h6><input type='password' name='password' tabindex='2' required/></div>
                <div class="g-recaptcha" style="width: 100%; padding-right: 40px;" data-sitekey="6Lc0l20UAAAAAGGjhfyjjatyGEqm8dphjUCZYG4W"></div>
                <div style="width:100%;"><h6>&nbsp;</h6><input type='submit' class='submit' name='submit' value='ورود'/>
                </div>
            </div>
        </form>
    </div>
    <footer style="margin-top:10px !important;">
        <a href="http://www.moonlab.ir/achar" target="_blank"><img src="../Template/images/designer.v1.png"></a>
        <div id="data-footer">Copyright <?php echo date('Y'); ?> Moonlab . All Rights Reserved .</div>
        <footer>
</section>
</body>
</html>