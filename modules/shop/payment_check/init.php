<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Common
 ******************************************************************************/
// Register this payment module
Model_Payment::register(array(
    'module'  => 'check',
    'caption' => 'Оплата по квитанции'
));