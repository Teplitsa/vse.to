<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />
    <title><?php echo HTML::chars($message); ?></title>

    <?php echo HTML::style('public/css/stylesheet.css'); ?>
    <?php echo HTML::style('public/css/print_stylesheet.css',array('media'=>'print')); ?>
    <?php echo HTML::style('public/css/stylesheet_css_buttons.css'); ?>

</head>
<body>
    <br>
    <br>    
    <h1><?php echo HTML::chars($message); ?></h1>

<?php
if (Kohana::$profiling)
{
    echo View::factory('profiler/stats')->render();
}
?>
</body>
</html>