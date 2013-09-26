<?php defined('SYSPATH') or die('No direct script access.');

    echo Widget::render_widget('products', 'product', $product);

    echo Widget::render_widget('telemosts', 'request', $product);    

    ?>

