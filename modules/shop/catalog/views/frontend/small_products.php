<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div id='products' class='widget'>
<?php 
$today_datetime = new DateTime("now");
$tomorrow_datetime = new DateTime("tomorrow");
$today_flag= 0;
$tomorrow_flag= 0;
$nearest_flag = 0;

// ----- Set up urls
$create_url = URL::to('frontend/catalog/products/control', array('action'=>'create'));
$update_url = URL::to('frontend/catalog/products/control', array('action'=>'update', 'id' => '${id}'), TRUE);

?>
    <div class="wrapper main-list">
        <div class="ub-title">
            <p>Мои оффлайн-события</p>             
            <a href="<?php echo $create_url?>" class="link-add">+ добавить</a>        
        </div><div class="ub-title"></div>
    <hr>
<?php
$i=0;
foreach ($products as $product):
    $i++;
    $url = URL::site($product->uri_frontend());

    $telemosts = $product->get_telemosts();
    $numTelemosts = count($telemosts);    
    
    $image='';
    if (isset($product->image))
    {
        $image = 
                '<a href="' . $url . '">'
      .             HTML::image('public/data/' . $product->image, array('alt' => $product->caption))
      .         '</a>';
    }

    if (($today_flag == 0) && $product->datetime->format('d.m.Y') == $today_datetime->format('d.m.Y')) {
        $today_flag++;
    } elseif (($tomorrow_flag == 0) && $product->datetime->format('d.m.Y') == $tomorrow_datetime->format('d.m.Y')){
        $tomorrow_flag++;            
    } elseif ($nearest_flag == 0) {
        $nearest_flag++;
    }    
    $day = $nearest_flag?$product->weekday:($tomorrow_flag?'Завтра':'Сегодня');
    
    ?>
    <section class="mini">
        <div class="row-fluid">
            <div class="span2"><?php echo $image ?></div>
            <div class="span6">
                <p><span class="date"><a class="day" href=""><?php echo $day?></a>, <?php echo $product->get_datetime_front()?> </span></p>
                <p class="title"><a href="<?php echo $url ?>"><?php echo $product->caption?></a></p>
                <p class="place"><?php echo $product->place->town_name?>: <?php echo $product->place->name ?></p>
            </div>
         
        </div>
<?php
        if ($product->user_id == Model_User::current()->id) {
            $_update_url = str_replace('${id}', $product->id, $update_url);
?>        
        <a href="<?php echo $_update_url;?>" class="link-edit"><i class="icon-pencil icon-white"></i></a>
        <span class="link-edit-user-page-req">Заявки: <?php echo $numTelemosts ?></span>
<?php } ?>
    </section>
    <?php if ($i == count($products)) {?>   
    <hr class='last_hr'>
    <?php } else { ?>
    <hr>
    <?php } ?>
<?php endforeach; //foreach ($products as $product)
?>
        <?php
        if ($pagination)
        {   
            echo $pagination;
        }
        ?>     
    </div>
</div>

