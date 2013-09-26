<?php defined('SYSPATH') or die('No direct script access.');
    switch ($stage) {
        case Model_Product::ACTIVE_STAGE:
            break;                        
        case Model_Product::START_STAGE:
            $choose_url = URL::to('frontend/catalog/product/fullscreen', array('alias' => $product->alias));
            
            /*if (Auth::granted('stop_event')) { */?>
                        <a href="<?php echo $choose_url; ?>" class="request-link button" target="_blank">Присоединиться</a>
                        <!--<a href="<?php //echo URL::site($product->uri_frontend(NULL,Model_Product::START_STAGE)); ?>" class="request-link button">Присоединиться</a>-->                       
                <?php
            /*}*/
            break;
    } ?>