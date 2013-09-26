<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    Route::add('frontend/faq', new Route_Frontend(
                'faq(/<action>(/<id>))(/p-<page>)',
        array(
            'action' => '\w++',
            'id'     => '\d++',
            'page'   => '(\d++|all)'
        )))
        ->defaults(array(
            'controller' => 'faq',
            'action'     => 'index',
            'id'         => NULL,
            'page'       => '0'
        ));
}

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/faq', new Route_Backend(
                'faq(/<action>(/<id>)(/ids-<ids>))'
              . '(/p-<page>)'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[0-9_]++',

            'page' => '(\d++|all)',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'faq',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',
            
            'page' => '0',
        ));

    // ----- Add backend menu items
    $id = Model_Backend_Menu::add_item(array(
        'id'   => 6,
        'menu' => 'main',
        
        'caption' => 'Вопрос-ответ',
        'route' => 'backend/faq',
        'icon' => 'faq'
    ));
    
        Model_Backend_Menu::add_item(array(
            'parent_id' => $id,
            'menu'      => 'main',

            'caption' => 'Список вопросов',
            'route' => 'backend/faq',
            'select_conds' => array(
                array('route' => 'backend/faq', 'route_params' => array('action' => 'index')),
                array('route' => 'backend/faq', 'route_params' => array('action' => 'create')),
                array('route' => 'backend/faq', 'route_params' => array('action' => 'update')),
                array('route' => 'backend/faq', 'route_params' => array('action' => 'delete'))
            )            
        ));

        Model_Backend_Menu::add_item(array(
            'parent_id' => $id,
            'menu'      => 'main',

            'caption' => 'Настройки',
            'route' => 'backend/faq',
            'route_params' => array('action' => 'config')
        ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
// ----- Add node types
Model_Node::add_node_type('faq', array(
    'name' => 'Вопрос-ответ',
    'backend_route'  => 'backend/faq',
    'frontend_route' => 'frontend/faq'
));
