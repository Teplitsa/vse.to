<?php defined('SYSPATH') or die('No direct script access.');

$provider = $product->get_telemost_provider();

if ($provider == Model_Product::COMDI) {
    $connectUrl = URL::to('frontend/catalog/product/fullscreen', array('alias' => $product->alias));
    $endUrl = URL::site($product->uri_frontend(NULL,Model_Product::STOP_STAGE));
    
} else if ($provider == Model_Product::HANGOTS) {
    
    if (!empty($product->hangouts_url)) {
        $connectUrl = $product->hangouts_url;
    } else {
         $connectUrl = "https://plus.google.com/hangouts/_?gid=1085528649580&gd=".$product->hangouts_secret_key;
    }
    
    $endUrl = URL::site($product->uri_frontend(NULL,Model_Product::STOP_STAGE));
}
?>

    

<?php     
    switch ($stage) {
        case Model_Product::ACTIVE_STAGE:
?>
                    <a href="<?php echo $connectUrl; ?>" class="go-link button" target="_blank">Подключиться</a>
<?php
            break;                        
        case Model_Product::START_STAGE:
?>
                    <a href="<?php echo $connectUrl; ?>" class="go-link button" target="_blank">Подключиться</a>
                    <a href="<?php echo $endUrl ?>" class="request-link button">Закончить</a>
<?php
            break;
    }
?>


