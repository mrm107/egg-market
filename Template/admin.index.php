<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?php echo $view_title ?></title>
    <link rel="stylesheet" type="text/css"
          href="<?php echo $conf_address . $conf_dir ?>Template/css/reset.v.1.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo $conf_address . $conf_dir ?>Template/css/semantic.v.1.0.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo $conf_address . $conf_dir ?>Template/css/general.v.1.0.css?v=2">
    <link rel="stylesheet" type="text/css"
          href="<?php echo $conf_address . $conf_dir ?>Template/plugin/jalalijscalender.v.1.4/css/calendar-brown.css">
    <link rel="icon" type="image/png"
          href="<?php echo $conf_address . $conf_dir ?>Template/images/achar.64x64.v1.png">
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/javascript/jquery-2.0.3.min.js"></script>
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/javascript/semantic.v.1.0.js"></script>
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/javascript/general.v.1.0.js?v=2"></script>
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/plugin/jalalijscalender.v.1.4/js/jalali.js"></script>
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/plugin/jalalijscalender.v.1.4/js/calendar.js"></script>
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/plugin/jalalijscalender.v.1.4/js/calendar-setup.js"></script>
    <script type="text/javascript"
            src="<?php echo $conf_address . $conf_dir ?>Template/plugin/jalalijscalender.v.1.4/js/calendar-fa.js"></script>
    <?php if (isset($view_header)) {
        echo $view_header;
    } ?>
</head>
<body>
<aside class="ui wide right sidebar sixteen wide column"><?php echo $view_sidebar ?></aside>
<section class="ui grid">
    <main class="sixteen wide column center-page">
        <nav id="shortcutbar">
            <a class="plusmenu" id="sidebar-togller" data-content="نوار ابزار" data-variation="inverted small"></a>
            <?php echo $view_nav ?>
        </nav>
        <header><?php echo $view_box_header ?></header>
        <?php if (isset($view_content_necc)) {
            echo $view_content_necc;
        } else {
            echo $view_content;
        } ?>
        <footer>
            <a href="http://moonlab.ir/" target="_blank"><img src="../../Template/images/designer.v1.png"></a>
            <div id="data-footer">Copyright <?php echo date('Y'); ?> Moonlab . All Rights Reserved .</div>
            <?php if (isset($view_footer)) {
                echo $view_footer;
            } ?>
            <footer>
    </main>
</section>
</body>
</html>