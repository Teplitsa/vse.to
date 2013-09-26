<?php defined('SYSPATH') or die('No direct script access.'); ?>

    <div class="b-search-head">
        <p>Результаты поиска:</p>
        <ul class="b-results">
            <li><a href="">события и телемосты <span class="count"><?php echo '('.count($products).')' ?></span></a></li>
        </ul>
    </div>
    
<?php if (count($products)) { ?>   
<div class="search"?>
<h1 class="main-title"><span>События <span class="count"><?php echo count($products) ?></span></span></h1>
</div>
<?php foreach ($products as $product) { 

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


?>   

<section>
    <header>
        <div class="row-fluid">
            <div class="span6" style="white-space: nowrap;">
                <span class="date"><?php echo $product->get_datetime_front()?></span>
                <span class="type"><?php echo Model_Product::$_interact_options[$product->interact] ?></span>
            </div>
            <div class="span6 b-link">
            <a data-toggle="modal" href="#requestModal" class="request-link button">Подать заявку</a>
            <a href="#" class="go-link button">Я пойду</a>                
            </div>
        </div>
        
    </header>
    <div class="body-section">
        <div class="row-fluid">
            <div class="span6 face">
                <?php echo $image ?>
                <p class="counter"><span title="хочу телемост" id-"" class="hand">999</span></p>
            </div>
            <div class="span6">
                <a class="dir" href="#"><?php echo Model_Product::$_theme_options[$product->theme]?></a>
                <h2><a href="event"><?php echo $product->caption?></a></h2>
                <p class="lecturer">Лектор: <a href="#"><?php echo $product->lecturer_name ?></a></p>
                <div class="desc"><p><?php echo $product->short_desc ?></p></div>
                <p class="link-more"><a href="<?php echo $url?>">Подробнее</a></p>
            </div>
        </div>
    </div>
</section>
<hr>

<?php }} ?>

<?php
if ($pagination)
{
    echo $pagination;
}
?>
