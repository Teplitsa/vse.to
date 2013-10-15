<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo HTML::chars($view->placeholder('title')); ?></title>

    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />
    <meta http-equiv="Keywords" content="<?php echo HTML::chars($view->placeholder('keywords')); ?>" />
    <meta http-equiv="Description" content="<?php echo HTML::chars($view->placeholder('description')); ?>" />

    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/forms.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/tables.css'); ?>
    <?php echo HTML::style(Modules::uri('backend') . '/public/css/backend/backend_ie6.css', NULL, FALSE, 'if lte IE 6'); ?>

    <?php echo $view->placeholder('styles'); ?>
    <?php echo $view->placeholder('scripts'); ?>
</head>
<body>
    
    <table class="authorization"><tr><td class="super_centered">
        <?php echo $content; ?>
    </td></tr></table>

<?php
if (Kohana::$profiling)
{
    //echo View::factory('profiler/stats')->render();
}
?>
</body>
</html>