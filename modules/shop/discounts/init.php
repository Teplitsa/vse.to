<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
}
/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    // ----- coupons
    Route::add('backend/coupons', new Route_Backend(
                'coupons(/<action>(/<id>)(/user-<user_id>))'
              . '(/order-<coupons_order>)(/desc-<coupons_desc>)(/p-<page>)'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'id'         => '\d++',
                'user_id'    => '\d++',

                'coupons_order' => '\w++',
                'coupons_desc'  => '[01]',
                'page'          => '\d++',
                
                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'coupons',
            'action'     => 'index',
            'id'         => NULL,
            'user_id'    => NULL,

            'coupons_order' => 'id',
            'coupons_desc'  => '1',
            'page'          => '0',
        ));

    // ----- Add backend menu items
    $parent_id = Model_Backend_Menu::add_item(array(
        'menu' => 'main',

        'caption' => 'Акции и скидки',
        'route' => 'backend/coupons',
        'select_conds' => array(
            array('route' => 'backend/coupons'),
            array('route' => 'backend/specialoffers'),
        ),
        'icon' => 'discounts'
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,

        'caption' => 'Купоны на скидку',
        'route' => 'backend/coupons'
    ));
}