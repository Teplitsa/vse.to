<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    Route::add('frontend/pages', new Route_Frontend(
                'pages(/<node_id>)',
        array(
            'action'     => '\w++',
            'node_id'    => '\w++',
        )))
        ->defaults(array(
            'controller' => 'pages',
            'action'     => 'view',
            'node_id'    => NULL
        ));

    if ( ! Modules::registered('indexpage'))
    {
        Route::add('frontend/indexpage', new Route_Frontend(''))
            ->defaults(array(
                'controller' => 'pages',
                'action'     => 'view'
            ));
    }

    Route::add('frontend/userpage', new Route_Frontend(
                'userpage(/<user_id>)',
        array(
            'action'     => '\w++',
            'user_id'    => '\d++',
        )))
        ->defaults(array(
            'controller' => 'pages',
            'action'     => 'update',
            'user_id'    => NULL
        ));
    
}

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/pages', new Route_Backend(
                'pages(/<action>(/<node_id>))'
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'node_id'   => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'pages',
            'action'     => 'update',
            'node_id'    => NULL
        ));

    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => 10,
        'site_required' => TRUE,

        'caption' => 'Текстовые страницы',
        'route' => 'backend/pages'
    ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
// ----- Add node types
Model_Node::add_node_type('page', array(
    'name' => 'Текстовая страница',
    'backend_route'  => 'backend/pages',
    'frontend_route' => 'frontend/pages',
    'model' => 'page'
));

//if ( ! Modules::registered('indexpage'))
//{
//    // Use text page as index page for site    
//    Model_Node::add_node_type('indexpage', array(
//        'name' => 'Главная страница',
//        'backend_route'  => 'backend/pages',
//        'frontend_route' => 'frontend/indexpage',
//        'model' => 'page'
//    ));
//}
