<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/images', new Route_Backend(
                'images(/<action>(/<id>))(/ot-<owner_type>)(/oid-<owner_id>)(/c-<config>)'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'owner_type' => '\w++',
            'owner_id'   => '\d++',
            'config'     => '\w++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'images',
            'action'     => 'index',
            'id'         => NULL,
            
            'owner_type' => '',
            'owner_id'   => NULL
        ));
}
if (APP === 'FRONTEND')
{
    Route::add('frontend/images', new Route_Frontend(
                'images(/<action>(/<id>))(/ot-<owner_type>)(/oid-<owner_id>)(/c-<config>)'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'owner_type' => '\w++',
            'owner_id'   => '\d++',
            'config'     => '\w++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'images',
            'action'     => 'index',
            'id'         => NULL,
            
            'owner_type' => '',
            'owner_id'   => NULL
        ));
}
