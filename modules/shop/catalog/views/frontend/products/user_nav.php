<?php defined('SYSPATH') or die('No direct script access.');

    $provider = $product->get_telemost_provider();
    if ($provider == Model_Product::COMDI)
        $choose_url = URL::to('frontend/catalog/product/fullscreen', array('alias' => $product->alias));
    else if ($provider == Model_Product::HANGOTS)
        $choose_url = base64_decode($product->hangouts_url);

    switch ($stage) {
        case Model_Product::ACTIVE_STAGE:
            break;                        
        case Model_Product::START_STAGE:
            if (!empty($choose_url)):
?>
                <a href="<?php echo $choose_url; ?>" class="request-link button" target="_blank">Присоединиться</a>
<?php
                endif;
            break;
    } 
?>