<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/menus', new Route_Backend(
                'menus(/<action>(/<id>))'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'menus',
            'action'     => 'index',
            'id'         => NULL
        ));

    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => 2,
        
        'caption' => 'Меню',
        'route' => 'backend/menus',        
        'icon'  => 'menus'
    ));
}