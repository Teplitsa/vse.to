<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/nodes', new Route_Backend(
                'nodes(/<action>(/<id>))'
              . '(/parent-<node_id>)'
              . '(/~<history>)'
            ,
            array(
                'action' => '\w++',
                'id'     => '\d++',

                'node_id' => '\d++',

                'history' => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'nodes',
            'action'     => 'index',
            'id'         => NULL,

            'node_id'  => NULL,
        ));
    
    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'id' => 10,
        'parent_id' => 2,
        'site_required' => TRUE,

        'caption' => 'Страницы',
        'route' => 'backend/nodes',
        'select_conds' => array(
            array('route' => 'backend/nodes'),
            array('route' => 'backend/pages'),
        )
    ));
}