<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=<?php echo Kohana::$charset; ?>" />
    <title><?php echo HTML::chars($view->placeholder('title')); ?></title>

    <meta http-equiv="keywords" content="<?php echo HTML::chars($view->placeholder('keywords')); ?>" />
    <meta http-equiv="description" content="<?php echo HTML::chars($view->placeholder('description')); ?>" />

    <?php echo HTML::style('public/css/default.css'); ?>
    <?php echo HTML::style('public/css/default_ie6.css', NULL, FALSE, 'if lte IE 6'); ?>
    <?php echo HTML::style('public/css/int.css'); ?>

    <?php echo $view->placeholder('styles'); ?>
    <?php echo $view->placeholder('scripts'); ?>
</head>

<body>
<!-- popup -->
<?php echo Widget::render_widget('feedback', 'popup'); ?>

<!-- Шапка -->
<div id="head">
<div class="out">
    <a id="logo" href="<?php echo URL::site(''); ?>" title="Интернет-магазин игрушек Папа Саша">
        <?php echo HTML::image('public/css/img/logo.gif', array('alt' => 'Интернет-магазин игрушек Папа Саша', 'border' => '0')); ?>
    </a>
    <a id="topban" href="<?php echo URL::site('pages/payment_and_delivery'); ?>" title="Специальные условия доставки">
        <?php echo HTML::image('public/css/img/top-banner.gif', array('alt' => 'Специальные условия доставки в Мытищи, Королев, Щелково, Пушкино', 'border' => '0')); ?>
    </a>
    <div id="phone">
        <?php echo Widget::render_widget('blocks', 'block', 'phones'); ?>
        Не смогли дозвониться? <strong><a href="<?php echo URL::site('feedback'); ?>" class="orange popup">Оставьте свой телефон</a></strong>
    </div>
    
    <!-- Меню о магазине -->
    <div id="topmenu">
    <ul>
        <li class="first"><a href="<?php echo URL::site('pages/about'); ?>">О магазине</a></li>
        <li><a href="<?php echo URL::site('pages/payment_and_delivery'); ?>">Доставка и оплата</a></li>
        <li><a href="<?php echo URL::site('faq'); ?>">Вопрос-ответ</a></li>
        <li><a href="<?php echo URL::site('pages/contacts'); ?>">Контакты</a></li>
    </ul>
    </div>
</div>
</div>

<!-- Главное меню -->
<div id="mainmenu">
<div class="out">
<div class="tl">
<table width="966" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="205">
        <a href="<?php echo URL::site('catalog-brand'); ?>">
            <?php echo HTML::image('public/css/img/brands.gif', array('class' => 'mm', 'width' => 179, 'height' => 20, 'alt' => 'Игрушки по брендам')); ?>
        </a>
    </td>
    <td width="11"><?php echo HTML::image('public/css/img/mainmenu-sep.gif', array('width' => 11, 'height' => 45)); ?></td>
    
    <td width="237">
        <a href="<?php echo URL::site('catalog-cat'); ?>">
            <?php echo HTML::image('public/css/img/categories.gif', array('class' => 'mm', 'width' => 201, 'height' => 20, 'alt' => 'Игрушки по категориям')); ?>
        </a>
    </td>
    <td width="11"><?php echo HTML::image('public/css/img/mainmenu-sep.gif', array('width' => 11, 'height' => 45)); ?></td>
    
    <td width="189">
        <a href="#">
            <?php echo HTML::image('public/css/img/help.gif', array('class' => 'mm', 'width' => 153, 'height' => 20, 'alt' => 'Помощь в выборе')); ?>
        </a>
    </td>
    <td width="3" style="background-color:#fff;"><div style="width:3px;"></div></td>

    <td width="299" class="cart">
        <?php echo Widget::render_widget('cart', 'summary'); ?>
    </td>
    <td width="11" class="cart"><div class="tr"><div class="br"></div></div></td>
</tr>
</table>
</div>
</div>
</div>
<!-- Поиск -->
<div id="search">
<div class="out">

    <?php echo Widget::render_widget('products', 'search'); ?>

    <div class="blog">
        <a href="#">
            <?php echo HTML::image('public/css/img/dot.gif', array('width' => 260, 'height' => 55, 'alt' => 'Игрушечный блог')); ?>
        </a>
    </div>
</div>
</div>