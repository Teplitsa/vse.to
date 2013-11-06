<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    Route::add('frontend/news', new Route_Frontend(
                'news'
              . '(/<year>)(/<month>)'
              . '(/<action>(/<id>))'
              . '(/p-<page>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'year'  => '\d++',
            'month' => '\d++',

            'page'       => '\d++',
            'news_order' => '\w++',
            'news_desc'  => '[01]',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'news',
            'action'     => 'index',
            'id'         => NULL,

            'year'  => 0,
            'month' => 0,

            'page'       => 0,
            'news_order' => 'date',
            'news_desc'  => '1'
        ));
}   

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/news', new Route_Backend(
                'news(/<action>(/<id>))'
              . '(/p-<page>)(/order-<news_order>)(/desc-<news_desc>)'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'page'       => '\d++',
            'news_order' => '\w++',
            'news_desc'  => '[01]',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'news',
            'action'     => 'index',
            'id'         => NULL,

            'page'       => 0,
            'news_order' => 'date',
            'news_desc'  => '1'
        ));

    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',

        'caption' => 'Новости',
        'route' => 'backend/news',
        'icon' => 'news'
    ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
// ----- Add node types
Model_Node::add_node_type('news', array(
    'name' => 'Новости',
    'backend_route'  => 'backend/news',
    'frontend_route' => 'frontend/news',
));