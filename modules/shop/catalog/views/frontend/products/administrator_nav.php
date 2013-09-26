<?php defined('SYSPATH') or die('No direct script access.');

    $choose_url = URL::to('frontend/catalog/product/fullscreen', array('alias' => $product->alias)); ?>

<?php     switch ($stage) {
        case Model_Product::ACTIVE_STAGE:
            /*if (Auth::granted('start_event')) { */?>
                    <a href="<?php echo $choose_url; ?>" class="go-link button" target="_blank">Подключиться</a>
                
                <?php
            /*}*/ break;                        
        case Model_Product::START_STAGE:
            /*if (Auth::granted('stop_event')) { */?>
                    <a href="<?php echo $choose_url; ?>" class="go-link button" target="_blank">Подключиться</a>
                    <a href="<?php echo URL::site($product->uri_frontend(NULL,Model_Product::STOP_STAGE)); ?>" class="request-link button">Закончить</a>
                <?php
            /*}*/
            break;
    } ?>


