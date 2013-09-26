<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    Route::add('frontend/indexpage', new Route_Frontend(''))
        ->defaults(array(
            'controller' => 'indexpage',
            'action'     => 'view'
        ));
}

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/indexpage', new Route_Backend(
                'indexpage(/<action>(/<node_id>))'
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'node_id'   => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'indexpage',
            'action'     => 'update',
            'node_id'    => NULL
        ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
// ----- Add node types
Model_Node::add_node_type('indexpage', array(
    'name' => 'Главная страница',
    'backend_route'  => 'backend/indexpage',
    'frontend_route' => 'frontend/indexpage',
    'model' => 'page'
));