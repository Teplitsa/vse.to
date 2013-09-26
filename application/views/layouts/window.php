<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Parse window size from ?window=WWWxHHH $_GET paramater
$width = 400; $height = 500;
if ( ! empty($_GET['window']))
{
    $dim = explode('x', $_GET['window']); //WWWxHHH
    if (isset($dim[0]) && (int) $dim[0] > 0)
    {
        $width = (int) $dim[0];
    }
    if (isset($dim[1]) && (int) $dim[1] > 0)
    {
        $height = (int) $dim[1];
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo HTML::chars($view->placeholder('title')); ?></title>

    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />
    <meta http-equiv="Keywords" content="<?php echo HTML::chars($view->placeholder('keywords')); ?>" />
    <meta http-equiv="Description" content="<?php echo HTML::chars($view->placeholder('description')); ?>" />
    
    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/frontend.css'); ?>
    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/forms.css'); ?>
    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/tables.css'); ?>
    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/frontend_ie8.css', NULL, FALSE, 'if lte IE 8'); ?>
    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/frontend_ie6.css', NULL, FALSE, 'if lte IE 6'); ?>

    <?php echo HTML::style('public/css/stylesheet.css'); ?>
    <?php echo HTML::style('public/css/print_stylesheet.css',array('media'=>'print')); ?>
    <?php echo HTML::style('public/css/stylesheet_css_buttons.css'); ?>

    <?php echo $view->placeholder('styles'); ?>
    <?php echo $view->placeholder('scripts'); ?>
</head>
<body class="window_body">

<div class="window_caption">
    <a href="#" class="close_window"><?php echo View_Helper_Admin::image('controls/close.gif'); ?></a>
    <?php
    if (isset($caption))
    {
        echo $caption;
    }
    ?>
</div>
<div class="window_content" style="height: <?php echo $height; ?>px">
    <div class="window_workspace">
        <?php echo $content; ?>

        <?php
        if (Kohana::$profiling)
        {
            //echo View::factory('profiler/stats')->render();
        }
        ?>
    </div>
</div>
    
</body>
</html>
