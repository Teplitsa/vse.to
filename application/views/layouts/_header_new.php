<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />
    <title><?php echo HTML::chars($view->placeholder('title')); ?></title>
    <meta http-equiv="keywords" content="<?php echo HTML::chars($view->placeholder('keywords')); ?>" />
    <meta http-equiv="description" content="<?php echo HTML::chars($view->placeholder('description')); ?>" />

    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/bootstrap.css'); ?>
    <?php echo HTML::style(Modules::uri('frontend') . '/public/css/frontend/style.css'); ?>
    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <?php echo $view->placeholder('styles'); ?>    
</head>

 