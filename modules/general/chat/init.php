<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    // ----- dialogs
    Route::add('frontend/dialogs', new Route_Frontend(
                'dialogs(/<action>(/<id>)(/ids-<ids>))'
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
            'controller' => 'dialogs',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',            
            'page' => '0',            
        ));
    
    Route::add('frontend/messages', new Route_Frontend(
                'messages(/dialog-<dialog_id>)(/<action>(/<id>)(/ids-<ids>))'
              . '(/user-<user_id>)'            
              . '(/p-<page>)'
              . '(/~<history>)',
            array(
            'dialog_id' => '\d++',                
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[0-9_]++',
            'user_id'   => '\d++',
            'page' => '(\d++|all)',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'messages',
            'action'     => 'index',
            'id'         => NULL,
            'dialog_id'  => NULL,            
            'ids'        => '',
            'user_id' => '0',            
            'page' => '0',
        ));    
}

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    // ----- dialogs
    Route::add('backend/dialogs', new Route_Backend(
                'dialogs(/<action>(/<id>)(/ids-<ids>))'
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
            'controller' => 'dialogs',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',            
            'page' => '0'       
        ));
    
    Route::add('backend/messages', new Route_Backend(
                'messages(/dialog-<dialog_id>)(/<action>(/<id>)(/ids-<ids>))'
              . '(/user-<user_id>)'            
              . '(/p-<page>)'
              . '(/~<history>)',
            array(
            'dialog_id' => '\d++',                
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[0-9_]++',
            'user_id'   => '\d++',
            'page' => '(\d++|all)',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'messages',
            'action'     => 'index',
            'id'         => NULL,
            'dialog_id'  => NULL,            
            'ids'        => '',
            'user_id' => '0',            
            'page' => '0',
        ));

    // ----- Add backend menu items
    $id = Model_Backend_Menu::add_item(array(
        'id'   => 6,
        'menu' => 'main',
        
        'caption' => 'Сообщения',
        'route' => 'backend/dialogs',
        'select_conds' => array(
            array('route' => 'backend/dialogs'),            
            array('route' => 'backend/messages'),
        ),        
        'icon' => 'dialogs'
    ));
    
        Model_Backend_Menu::add_item(array(
            'parent_id' => $id,
            'menu'      => 'main',

            'caption' => 'Диалоги',
            'route' => 'backend/dialogs'            
        ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
Model_Privilege::add_privilege_type('dialogs_index', array(
    'name'  => 'Сообщения',
    'readable' => TRUE,
    'controller' => 'dialogs',
    'action'     => 'index',       
    'frontend_route' => 'frontend/dialogs',
    'frontend_route_params' => array('action' => 'index'),  
));