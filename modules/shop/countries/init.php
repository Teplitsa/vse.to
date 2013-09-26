<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/countries', new Route_Backend(
                'countries(/<action>(/<id>))'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'countries',
            'action'     => 'index',
            'id'         => NULL
        ));

    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        
        'caption' => 'Страны и регионы',
        'route' => 'backend/countries'
    ));
}