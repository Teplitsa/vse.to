<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/courierzones', new Route_Backend(
                'courierzones(/<action>(/<id>)(/ids-<ids>)(/delivery-<delivery_id>))'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[\d_]++',
            'delivery_id' => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'courierzones',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',
            'delivery_id' => NULL
        ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
// Register this delivery module
Model_Delivery::register(array(
    'module'  => 'courier',
    'caption' => 'Доставка курьером'
));