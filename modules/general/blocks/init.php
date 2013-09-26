<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/blocks', new Route_Backend(
                'blocks(/<action>(/<id>))'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'blocks',
            'action'     => 'index',
            'id'         => NULL
        ));

    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => 2,
        
        'caption' => 'Блоки',
        'route' => 'backend/blocks',
        'icon' => 'blocks'
    ));
}