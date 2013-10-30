<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frotnend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    // ----- sections
    Route::add('frontend/area/towns', new Route_Frontend(
                'area/towns(/<action>(/<are_town_alias>))'
            ,
            array(
                'action'    => '\w++',
                'are_town_alias'  => '[^/]++',    
            )
        ))
        ->defaults(array(
            'controller' => 'towns',
            'action'     => 'index',
            'are_town_alias'  => '',            
        ));
    
    Route::add('frontend/area/place/select', new Route_Frontend(
                'area/place/select',
            array(
        )))
        ->defaults(array(
            'controller' => 'place',
            'action'     => 'select'             
        ));       
    
    
}
/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    if (Cookie::get(Model_Town::TOWN_TOKEN,FALSE))
        Cookie::delete(Model_Town::TOWN_TOKEN);
    
    Route::add('backend/area/places', new Route_Backend(
                'area/places(/<action>(/<id>))(/ids-<ids>)'
              . '(/town-<are_town_alias>)(/p-<page>)'             
              . '(/torder-<are_torder>)(/tdesc-<are_tdesc>)'
              . '(/porder-<are_porder>)(/pdesc-<are_pdesc>)'
              . '(/opts-<options_count>)'             
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[\d_]++',                
            'are_torder' => '\w++',
            'are_tdesc'  => '[01]',
            'are_porder' => '\w++',
            'are_pdesc'  => '[01]',
            'are_town_alias'  => '[^/]++',                 
            'options_count' => '\d++',                
            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'places',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',            
            'are_torder' => 'name',
            'are_tdesc'  => '0',
            'are_porder' => 'name',
            'are_pdesc'  => '0',
            'are_town_alias'  => '',            
            'options_count' => NULL,            
        ));
    
    // ----- sections
    Route::add('backend/area/towns', new Route_Backend(
                'area/towns(/<action>(/<id>)(/ids-<ids>))'
              . '(/town-<are_town_alias>)'
              . '(/torder-<are_torder>)(/tdesc-<are_tdesc>)'
              . '(/opts-<options_count>)'             
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'ids'       => '[\d_]++',
                'are_town_alias'  => '[^/]++', 
                'are_torder' => '\w++',
                'are_tdesc'  => '[01]',
                'options_count' => '\d++',
                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'towns',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',
            'are_torder' => 'lft',
            'are_tdesc'  => '0',
            'are_town_alias'  => '',            
            'options_count' => NULL,            
        ));
    
    Route::add('backend/area', new Route_Backend(
                'area(/<action>(/<id>))(/ids-<ids>)'
              . '(/town-<are_town_alias>)(/place-<place_id>)(/p-<page>)'            
              . '(/torder-<are_torder>)(/tdesc-<are_tdesc>)'
              . '(/porder-<are_porder>)(/pdesc-<are_pdesc>)'
              . '(/opts-<options_count>)'              
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[\d_]++',                
            'are_town_alias'  => '[^/]++',
            'place_id'   => '\d++',                
            'are_torder' => '\w++',
            'are_tdesc'  => '[01]',
            'are_porder' => '\w++',
            'are_pdesc'  => '[01]',
            'options_count' => '\d++',                
            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'area',
            'action'     => 'index',            
            'id'         => NULL,
            'ids'        => '',            
            'are_town_alias'  => '',            
            'place_id'      => '0',            
            'are_torder' => 'name',
            'are_tdesc'  => '0',
            'are_porder' => 'name',
            'are_pdesc'  => '0',
            'options_count' => NULL,             
        ));       

    // ----- Add backend menu items
    $parent_id = Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'site_required' => TRUE,        
        'caption' => 'Места',
        'route' => 'backend/area/places',
        'controller' => 'area',
        'select_conds' => array(
            array('route' => 'backend/area'),            
            array('route' => 'backend/area/towns'),
            array('route' => 'backend/area/places')            
        ),
        'icon' => 'sites'
    ));
        
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Площадки',
        'route' => 'backend/area/places',
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Города',
        'route' => 'backend/area/towns',
    ));    
}

/******************************************************************************
 * Module installation
 ******************************************************************************/
if (Kohana::$environment !== Kohana::PRODUCTION)
{
    // Create superusers group and superuser
    $town = new Model_Town();

    if ( ! $town->count())
    {
        $town->id = Model_Town::DEFAULT_TOWN_ID;
        $town->name = 'Москва';
        $town->phonecode = '495';        
        $town->timezone = Model_Town::$_timezone_options['Europe/Moscow'];        
        $town->save(TRUE);
    }
}

// ----- Add node types
Model_Node::add_node_type('map', array(
    'name' => 'Карта',
    'frontend_route' => 'frontend/area/towns',
));