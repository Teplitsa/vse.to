<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend', new Route_Backend('(/<controller>(/<action>(/<id>)))', array(
            'controller' => '\w++',
            'action'     => '\w++',
            'id'         => '\d++',
        )))
        ->defaults(array(
            'controller' => 'index',
            'action'     => 'index',
            'id'         => NULL,
        ));

    // ----- Configure menu
    Model_Backend_Menu::configure('sidebar', array(
        'caption' => 'Модули'
    ));

    
    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'id' => 2,
        'site_required' => TRUE,

        'caption' => 'Контент',
        'route' => 'backend/nodes',
        'icon' => 'pages',
        'select_conds' => array(
            array('route' => 'backend/nodes'),
            array('route' => 'backend/pages'),
            array('route' => 'backend/blocks'),
            array('route' => 'backend/menus'),            
        )
   ));
}