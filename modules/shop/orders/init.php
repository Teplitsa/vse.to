<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    // ----- cart
    Route::add('frontend/cart', new Route_Frontend(
                'cart(/<action>(/product-<product_id>)(/style-<widget_style>))'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'product_id' => '\d++',
                'widget_style' => '(small|big)',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'cart',
            'action'     => 'index',
            'product_id' => NULL,
            'widget_style' => 'small',

            'history' => ''
        ));

    // ----- order checkout
    Route::add('frontend/orders', new Route_Frontend(
                'orders(/<action>(/cart-<cart>))'
            ,
            array(
                'action' => '\w++',
                'cart'   => '[0-9_-]++',
            )
        ))
        ->defaults(array(
            'controller' => 'orders',
            'action'     => 'checkout',
            'cart'       => ''
        ));
}
/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    // ----- cartproducts
    Route::add('backend/orders/products', new Route_Backend(
                'orders/products(/<action>(/<id>)(/order-<order_id>)(/product-<product_id>))'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'id'         => '\d++',
                'order_id'   => '\d++',
                'product_id' => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'cartproducts',
            'action'     => 'index',
            'id'         => NULL,
            'order_id'   => NULL
        ));

    // ----- orderstatuses
    Route::add('backend/orders/statuses', new Route_Backend(
                'orders/statuses(/<action>(/<id>))'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'orderstatuses',
            'action'     => 'index',
            'id'         => NULL,
        ));

    // ----- ordercomments
    Route::add('backend/orders/comments', new Route_Backend(
                'orders/comments(/<action>(/<id>)(/order-<order_id>))'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'id'         => '\d++',
                'order_id'   => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'ordercomments',
            'action'     => 'index',
            'id'         => NULL,
            'order_id'   => NULL
        ));

    // ----- orders
    Route::add('backend/orders', new Route_Backend(
                'orders(/<action>(/<id>)(/ids-<ids>)(/user-<user_id>))'
              . '(/order-<orders_order>)(/desc-<orders_desc>)(/p-<page>)'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'ids'       => '[\d_]++',

                'user_id' => '\d++',

                'orders_order' => '\w++',
                'orders_desc'  => '[01]',
                'page'         => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'orders',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',

            'user_id' => NULL,

            'orders_order' => 'id',
            'orders_desc'  => '1',
            'page'         => 0
        ));

    // ----- Add backend menu items
    $parent_id = Model_Backend_Menu::add_item(array(
        'id'   => 5,
        'menu' => 'main',

        'caption' => 'Заказы',
        'route' => 'backend/orders',
        'select_conds' => array(
            array('route' => 'backend/orders'),
            array('route' => 'backend/orders/products'),
            array('route' => 'backend/orders/statuses'),
            array('route' => 'backend/orders/comments'),
            array('route' => 'backend/deliveries'),
            array('route' => 'backend/payments'),
            array('route' => 'backend/history', 'route_params' => array('item_type' => 'order'))
        ),
        'icon' => 'orders'
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,

        'caption' => 'Список заказов',
        'route' => 'backend/orders',
        'select_conds' => array(
            array('route' => 'backend/orders'),
            array('route' => 'backend/orders/products'),
            array('route' => 'backend/orders/comments')
        )
    ));

    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,

        'caption' => 'Статусы',
        'route' => 'backend/orders/statuses',
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,

        'caption' => 'Журнал работы',
        'route' => 'backend/history',
        'route_params' => array('item_type' => 'order')
    ));
}

/******************************************************************************
 * Module installation
 ******************************************************************************/
if (Kohana::$environment !== Kohana::PRODUCTION)
{
    // Register system statuses
    $status = new Model_OrderStatus();

    if ( ! $status->exists_by_id(Model_OrderStatus::STATUS_NEW))
    {
        $status->init(array(
            'id'      => Model_OrderStatus::STATUS_NEW,
            'caption' => 'Новый заказ',
            'system'  => TRUE
        ));
        $status->create();
    }

    if ( ! $status->exists_by_id(Model_OrderStatus::STATUS_COMPLETE))
    {
        $status->init(array(
            'id'      => Model_OrderStatus::STATUS_COMPLETE,
            'caption' => 'Выполнен',
            'system'  => TRUE
        ));
        $status->create();
    }

    if ( ! $status->exists_by_id(Model_OrderStatus::STATUS_CANCELED))
    {
        $status->init(array(
            'id'      => Model_OrderStatus::STATUS_CANCELED,
            'caption' => 'Отменён',
            'system'  => TRUE
        ));
        $status->create();
    }
}