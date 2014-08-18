<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php require_once(Kohana::find_file('views', 'frontend/products/_map')); ?>

<?php
if ( ! count($products))
{
    echo '<i>Анонсов нет</i>';
}
else {
$count = count($products);
$i = 0;
$products->rewind();

if (!isset($without_dateheaders)) $without_dateheaders=FALSE;

$today_datetime = new DateTime("now");
$tomorrow_datetime = new DateTime("tomorrow");
$today_time = $today_datetime->format('d.m.Y');
$tomorrow_time = $tomorrow_datetime->format('d.m.Y');

$today_events = array();
$tomorrow_events = array();
$near_events = array();

$today_str = "";
$tomorrow_str = "";
$near_str = "";

while ($i < $count)
{
    $product = $products->current();
    $products->next();

    $prod_time = $product->datetime->format('d.m.Y');

    if($prod_time == $today_time) {
        array_push($today_events, $product);
        $today_str .= Widget::render_widget('products', 'small_product', $product);
        $today_str .= Widget::render_widget('telemosts', 'request', $product);
    } else if($prod_time == $tomorrow_time) {
        array_push($tomorrow_events, $product);
        $tomorrow_str .= Widget::render_widget('products', 'small_product', $product);
        $tomorrow_str .= Widget::render_widget('telemosts', 'request', $product);
    } else {
        array_push($near_events, $product);
        $near_str .= Widget::render_widget('products', 'small_product', $product);
        $near_str .= Widget::render_widget('telemosts', 'request', $product);
    }
    $i++;
}

if(count($today_events)) {
    echo '<h1 class="main-title"><span>Сегодня, '.$today_time.'</span></h1>';
//    foreach ($today_events as $product) {
//        echo Widget::render_widget('products', 'small_product', $product);
//        Widget::render_widget('telemosts', 'request', $product);
//    }
    echo $today_str;
}
if(count($tomorrow_events)) {
    echo '<h1 class="main-title"><span>Завтра, '.$tomorrow_time.'</span></h1>';
//    foreach ($tomorrow_events as $product) {
//        echo Widget::render_widget('products', 'small_product', $product);
//        Widget::render_widget('telemosts', 'request', $product);
//    }
    echo $tomorrow_str;
}
if (count($near_events)) {
    if (isset($is_archive) && $is_archive) {
      $title = 'Архивные события';
    } else {
      $title = 'В ближайшее время';
    }

    echo "<h1 class='main-title'><span>$title</span></h1>";
//    foreach ($near_events as $product) {
//        echo Widget::render_widget('products', 'small_product', $product);
//        Widget::render_widget('telemosts', 'request', $product);
//    }
    echo $near_str;
}
?>

<?php
if ($pagination)
{
    echo $pagination;
} }?>
