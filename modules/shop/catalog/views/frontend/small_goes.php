<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php 
if (!count($products)) return;
$today_datetime = new DateTime("now");
$tomorrow_datetime = new DateTime("tomorrow");
$today_flag= 0;
$tomorrow_flag= 0;
$nearest_flag = 0;
?>
    <div class="wrapper main-list">
        <div class="ub-title">
            <p>Я пойду</p>             
        </div>
    <hr>
<?php
$view = new View('frontend/products/list');
$view->order_by = $order_by;
$view->desc = $desc;
$view->products = $products;
$view->pagination = '';
$view->without_dateheaders = true;
echo $view->render();
?>
    <?php
    if ($pagination)
    {   
        echo $pagination;
    }
    ?>    
    </div>
