<?php

class controller
{

    public $conf_database;
    public $model;
    public $view;
    public $datetime;
    public $jdate;
    public $db;
    public $view_title;
    public $view_content;
    public $view_content_beta;
    public $view_sidebar;
    public $view_name;
    public $side_view;
    public $fastaccess_view;
    public $access;
    public $access_comments;
    public $file_error;
    public $fileplace;

    public function __construct()
    {
        $this->conf_database = new base_config;
        $this->model = new model;
        $this->view = new view;
        $this->view->conf_address = $this->conf_database->conf_address;
        $this->view->conf_dir = $this->conf_database->conf_dir;
        $this->fileplace = $this->conf_database->conf_address . $this->conf_database->conf_dir;
        date_default_timezone_set($this->conf_database->conf_timezone);
        $this->jdate = new jDateTime(true, false, $this->conf_database->conf_timezone);
        $this->sdate = new jDateTime(true, true, $this->conf_database->conf_timezone);
        $this->datetime = $this->jdate->date('Y-m-d H:i:s');
    }

    public function checker_session_isset($session)
    {
        if (isset($_SESSION[$session])) {
            header('Location: ' . $this->conf_database->conf_address . $this->conf_database->conf_dir . 'App/administrator/');
            exit;
        }
    }

    public function checker_session_responsibility($session)
    {
        if (!isset($_SESSION[$session])) {
            header('Location: ' . $this->conf_database->conf_address . $this->conf_database->conf_dir . 'App/administrator/logout.php');
            exit;
        }
        if ($this->model->checker_session($_SESSION[$session]) === false) {
            header('Location: ' . $this->conf_database->conf_address . $this->conf_database->conf_dir . 'App/administrator/logout.php');
            exit;
        }
        $this->view_name = $this->model->view_name;
        $address = explode('/', $this->getaddress());
        $address = end($address);
        if ($this->model->user_status == 'deleted') {
            header('Location: logout.php');
            exit;
        } elseif ($this->model->user_status == 'deactive') {
            if ($address != 'block.view.php') {
                header('Location: block.view.php');
                exit;
            }
        }
    }

    public function getaddress()
    {
        $this->pagename = explode($this->conf_database->conf_dir, $_SERVER['REQUEST_URI']);
        return $this->pagename[1];
    }

    public function checker_page_access($pagecode)
    {
        $this->access = $this->model->fetch_one('responsibilities', "where `name` = '" . $this->model->user_respon . "'");
        $this->access = unserialize($this->access[2]);
        if (!in_array($pagecode, $this->access)) {
            header('Location: index.php');
        }
    }

    public function set_sidebar()
    {
        $message_count = $this->model->get_count_db('`messages`', "`date` < '" . date('Y-m-d H:i:s') . "' and `receiver` = '{$this->model->user_code}' and `status` = 'unread'");
        $all_count = $message_count;
        $user_name = $this->model->view_name;
        $avatar = "../../Template/images/no-user.v1.png";
        $sex = 'male';

        $side_view = "
			<script type='text/javascript'>
				var currentTime = new Date(" . date("Y,m,d,H,i,s") . ", 0);
				showtime();
			</script>
			<audio src='../../Template/audio/notice-alert.mp3' id='notice-alert'></audio>
		    <div class='custome-box' id='logo-place'>
                <img src='../../Template/images/achar.64x64.v1.png' id='achar-logo' />
                <h3>سامانه مرغداران ایران</h3>
                <h5>هسته خدمات</h5>
            </div>
            <div class='custome-box'>
                <div class='custome-label' id='sidebar-togller-sex'><i class='{$sex} icon'></i></div>
                <div id='avatar-place' style='height:auto;'><img class='circular ui image' src='{$avatar}' /></div>
                <h6 class='profile-greating'> سلام , وقت بخیر !</h6>
                <h4 class='profile-detail'>{$user_name}</h4>
                <div class='button-place' style='width:185px;'>
                    <a href='users.profile.php' class='ui mini attached button byekan-mini' style='width:80px; float:right; margin-right:5px;'>پروفایل</a>
                    <a href='logout.php' class='ui mini attached button byekan-mini' style='width:80px; float:right; margin-right:5px;'>خروج</a>
                </div>
            </div>
            <time class='custome-box'>
                <div id='livetime'><div id='clock-place'></div><div id='clock-helper'>ثانیه دقیقه سـاعت</div></div>
                <div id='livadate'>" . $this->sdate->date('l') . "&nbsp;&nbsp;&nbsp;" . $this->sdate->date('Y/m/d') . "</div>
            </time>
            <menu class='ui basic accordion'>
                <div class='title' id='count-first-sidebar'>
                    <i class='home icon'></i>
                    پیشخوان
                </div>
                <div class='content' id='content-first-sidebar'>
                    <a href='index.php' class='full-items'>خانه</a><a class='non-display'></a>
                    <a href='messages.view.php' class='half-items'>پیام ها <h3 class='number' id='messages-count'>{$message_count}</h3></a>
					<a href='users.profile.php' class='half-items'>حساب کاربری</a>
					<div id='all-count-sidebar' class='non-display'>{$all_count}</div>
                </div>";
        $side_array = $this->model->fetch_one('responsibilities', "where `name` = '" . $this->model->user_respon . "'");
        $side_user = unserialize($side_array[2]);
        $side_main = $this->model->fetch_all('responsibilities_work', "where `mother_array` = 'no' order by `sort` ASC");
        foreach ($side_main as $key) {
            if (in_array($key[0], $side_user)) {
                if ($key[8] == 'list') {
                    $side_view .= "<div class='title'><i class='{$key[3]} icon'></i>{$key[2]}</div>";
                    $side_sub = $this->model->fetch_all('responsibilities_work', "where `mother_array` = '{$key[0]}' order by `sort` ASC");
                    $side_view .= "<div class='content'>";
                    foreach ($side_sub as $subkey) {
                        if (in_array($subkey[0], $side_user)) {
                            if ($subkey[8] == 'slink')
                                $side_view .= "<a href='{$subkey[6]}' class='half-items'>{$subkey[2]}</a>";
                            else
                                $side_view .= "<a href='{$subkey[6]}' class='full-items'>{$subkey[2]}</a><a class='non-display'></a>";
                        }
                    }
                    $side_view .= "</div>";
                } else {
                    $side_view .= "<a href='{$key[6]}' class='link'><i class='{$key[3]} icon'></i>{$key[2]}</a>";
                }
            }
        }
        $this->view->view_sidebar = $side_view . "</menu>";
    }

    public function check_null_no($val)
    {
        if (trim($val) == ' ' or trim($val) == '' or trim($val) == NULL)
            $exval = NULL;
        else
            $exval = trim($val);
        return $exval;
    }

    public function set_fastaccess()
    {
        $this->fastaccess_view = '';
        if ($this->check_null_no($this->model->user_fastaccess) != NULL) {
            $fast_access = unserialize($this->model->user_fastaccess);
            $this->fastaccess_view = '';
            foreach ($fast_access as $fast_access_key) {
                if ($this->check_null_no($fast_access_key) != NULL) {
                    $fast_access_fetch = $this->model->fetch_one('responsibilities_work', "where `id` = '{$fast_access_key}'");
                    if ($fast_access_fetch[5] == 'no') {
                        $this->fastaccess_view .= "<a href='{$fast_access_fetch[6]}'>{$fast_access_fetch[2]}</a>";
                    } else {
                        $fast_access_fetch_parent = $this->model->fetch_one('responsibilities_work', "where `id` = '" . $fast_access_fetch[5] . "'");
                        $this->fastaccess_view .= "<a href='{$fast_access_fetch[6]}'>{$fast_access_fetch[2]}<span> [{$fast_access_fetch_parent[2]}]</span></a>";
                    }
                }
            }
        }
        return $this->fastaccess_view;
    }

    public function check_null($val)
    {
        if (intval($val) == ' ' or intval($val) == '' or intval($val) == NULL)
            $exval = NULL;
        else
            $exval = $val;
        return $exval;
    }

    public function upload_file($file, $output_dir, $category, $filesize, $allowed_ext, $error_ctf)
    {
        if (isset($_FILES[$file])) {
            $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            if (!in_array($extension, $allowed_ext)) {
                return "<span style=\"color:red; line-height:120px !important;\">خطا : فرمت فایل نا معتبر است .</span>";
                $this->file_error = true;
            } elseif (filesize($_FILES[$file]['tmp_name']) > ($filesize * 1024 * 1024)) {
                return "<span style=\"color:red; line-height:120px !important;\">خطا : حداکثر حجم مجاز برای بارگذاری {$filesize} مگابایت است .</span>";
                $this->file_error = true;
            } elseif ($_FILES[$file]["error"] > 0) {
                return "<span style=\"color:red;\">Error : " . $_FILES[$file]["error"] . "</span>";
                $this->file_error = true;
            } else {
                $possible = '123456789abcdefghijklmnopqrstuvwxyz';
                $code = '';
                for ($i = 0; $i < 12; $i++) {
                    $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
                }
                $name = $category . '-' . time() . $code . '.' . $extension;
                move_uploaded_file($_FILES[$file]["tmp_name"], $output_dir . $name);
                $this->file_error = false;
                return $name;
            }
        } else {
            return $error_ctf;
        }
    }
}

?>