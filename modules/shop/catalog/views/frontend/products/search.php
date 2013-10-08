<?php defined('SYSPATH') or die('No direct script access.'); ?>

    <div class="b-search-head">
        <p>Результаты поиска:</p>
        <ul class="b-results">
            <li><a href="">события и телемосты <span class="count"><?php echo '('.count($products).')' ?></span></a></li>
        </ul>
    </div>


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
$today_datetime = new DateTime("now");
$tomorrow_datetime = new DateTime("tomorrow");
$today_flag= 0;
$tomorrow_flag= 0;
$nearest_flag = 0;
if (!isset($without_dateheaders)) $without_dateheaders=FALSE; 
while ($i < $count)
{               
        $product = $products->current();

        $products->next();
        
        if (($today_flag == 0) && $product->datetime->format('d.m.Y') == $today_datetime->format('d.m.Y')) {
            if (!$without_dateheaders)
            echo '<h1 class="main-title"><span>Сегодня, '.$product->datetime->format('d.m.Y').'</span></h1>';
            $today_flag++;
        } elseif (($tomorrow_flag == 0) && $product->datetime->format('d.m.Y') == $tomorrow_datetime->format('d.m.Y')){
            if (!$without_dateheaders)            
            echo '<h1 class="main-title"><span>Завтра, '.$product->datetime->format('d.m.Y').'</span></h1>';
            $tomorrow_flag++;            
        } elseif ($nearest_flag == 0) {
            if (!$without_dateheaders)            
            echo '<h1 class="main-title"><span>В ближайшее время </span></h1>';
            $nearest_flag++;
        }
        
        $i++;      
        
        echo Widget::render_widget('products', 'small_product', $product);

        echo Widget::render_widget('telemosts', 'request', $product);    
        
} ?>

<?php
if ($pagination)
{
    echo $pagination;
} }?>


