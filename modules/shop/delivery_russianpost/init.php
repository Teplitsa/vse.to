<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Common
 ******************************************************************************/
// Register this delivery module
Model_Delivery::register(array(
    'module'  => 'russianpost',
    'caption' => 'Доставка почтой России'
));