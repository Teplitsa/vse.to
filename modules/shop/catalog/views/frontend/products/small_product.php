<?php defined('SYSPATH') or die('No direct script access.');

$today_datetime = new DateTime("now");
$tomorrow_datetime = new DateTime("tomorrow");

$url = URL::site($product->uri_frontend());
       
$image = '';
if (isset($product->image))
{
    $image = 
            '<a href="' . $url . '">'
  .             HTML::image('public/data/' . $product->image, array('alt' => $product->caption))
  .         '</a>';
}
$lecturer_url = URL::to('frontend/acl/lecturers', array('action' => 'show','lecturer_id' => $product->lecturer_id));

$day =NULL;
if ($product->datetime->format('d.m.Y') == $today_datetime->format('d.m.Y'))
    $day = 'Сегодня';
if ($product->datetime->format('d.m.Y') == $tomorrow_datetime->format('d.m.Y'))
    $day = 'Завтра';
if (!$day) $day = $product->weekday;

$telemost_flag= FALSE;
if (Model_Town::current()->alias != Model_Town::ALL_TOWN)
    $telemost_flag = ($product->place->town_name != Model_Town::current()->name);

$user_id = Model_User::current()->id;
$group_id = Model_User::current()->group_id;

$telemosts = $product->telemosts;
$available_num = (int)$product->numviews;
if (count($telemosts)) { 
    $available_num = ((int)$product->numviews > count($telemosts))?true:false;
}
?>
<section><header>
<div class="row-fluid">
<div class="span6" style="white-space: nowrap;">
<span class="date"><a class="day" href=""><?php echo $day ?></a><?php echo " ".$product->get_datetime_front()." "?></span>
<?php if (Model_Town::current()->alias == Model_Town::ALL_TOWN) { ?><span class="town"><?php echo $product->place->town_name;?></span><?php } ?>
<?php if ($telemost_flag) { ?><span class="type"><?php echo Model_Product::$_interact_options[$product->interact];?></span><?php } ?>
</div>
<div class="span6 b-link">
<?php $datenow = new DateTime("now");
if($product->datetime > $datenow): ?>
<?php if (!$telemost_flag && $group_id != Model_Group::USER_GROUP_ID && $group_id && $product->user_id !=  $user_id) {    
    if ($available_num && !$user_id) { ?>
    <a data-toggle="modal" href="#notifyModal" class="request-link button">Провести телемост</a>    
<?php  } elseif ($available_num && !$already_req) { ?>
<a data-toggle="modal" href="<?php echo "#requestModal_".$product->alias?>" class="request-link button">Провести телемост</a>
<?php } elseif($already_req) {
    $unrequest_url = URL::to('frontend/catalog/smallproduct/unrequest', array('alias' => $product->alias));?>
<a href="<?php echo $unrequest_url?>" class="ajax request-link button">Отменить заявку</a>         
<?php }} ?>
<? endif ?>
   
</div></div></header>
<div class="body-section">
<div class="row-fluid">
<div class="span6 face">
<?php echo $image ?>

    
<?php
    //$html .= '<p class="counter"><span title="хочу телемост" id-"" class="hand">999</span></p>';
 ?>
</div>
<div class="span6">
<a class="dir" href="#">Категория: <?php echo Model_Product::$_theme_options[$product->theme] ?></a>
<h2><a href="<?php echo $url ?>"><?php echo $product->caption ?></a></h2>
<p class="lecturer">Лектор: <a href="<?php echo $lecturer_url ?>"><?php echo $product->lecturer_name?></a></p>
<div class="desc"><p><?php echo $product->short_desc ?></p></div>
<p class="link-more"><a href="<?php echo $url ?>">Подробнее</a></p>
</div></div></div></section><hr>



