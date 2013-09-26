<?php defined('SYSPATH') or die('No direct script access.'); ?>

<?php 
if (!count($telemosts)) return;
$today_datetime = new DateTime("now");
$tomorrow_datetime = new DateTime("tomorrow");
$today_flag= 0;
$tomorrow_flag= 0;
$nearest_flag = 0;
?>
    <div class="wrapper main-list">
        <div class="ub-title">
            <p>Мои заявки</p>            
        </div>
        <div class="ub-title">        
 
        </div>            
    <hr>
<?php
$i=0;
foreach ($telemosts as $telemost):
    $i++;
    $product = $telemost->product;
    $url = URL::site($product->uri_frontend());

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
            <div class="span5">
                <p><span class="date"><a class="day" href=""><?php echo $day?></a>, <?php echo $product->get_datetime_front()?> </span></p>
                <p class="title"><a href="<?php echo $url ?>"><?php echo $product->caption?></a></p>
                <p class="place">Плащадка: <?php echo $product->place->town_name?>: <?php echo $product->place->name ?></p>
                <p class="organizer">Организатор: <?php echo $product->organizer_name?><span></span></p>
            </div>
            <div class="span7 desc">
                <?php echo $telemost->info; ?>
            </div>
        </div>
        <a href="#" class="link-edit"><i class="icon-pencil icon-white"></i></a>
    </section>
    <?php if ($i == count($telemosts)) {?>
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


