<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/sites', new Route_Backend(
                'sites(/<action>(/<id>))'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'sites',
            'action'     => 'index',
            'id'         => NULL
        ));

    // ----- Add backend menu items
    if (Kohana::config('sites.multi'))
    {
        Model_Backend_Menu::add_item(array(
            'id'   => 1,
            'menu' => 'main',
            
            'caption' => 'Магазины',
            'route' => 'backend/sites',
            'icon' => 'sites'
        ));
    }
    else
    {
        Model_Backend_Menu::add_item(array(
            'id'   => 1,
            'menu' => 'main',
            
            'caption' => 'Настройки',
            'route' => 'backend/sites',
            'route_params' => array('action' => 'update'),
            'icon' => 'settings'
        ));
    }
}

/******************************************************************************
 * Module installation
 ******************************************************************************/
if (Kohana::$environment !== Kohana::PRODUCTION)
{
    if ( ! Kohana::config('sites.multi'))
    {
        // Create the default site for single-site environment
        $site = Model_Site::current();

        if ( ! isset($site->id))
        {
            $site->caption = 'Новый сайт';
            $site->domain  = 'example.com';
            $site->save();
        }
    }
}
