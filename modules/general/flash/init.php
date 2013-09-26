<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/flashblocks', new Route_Backend(
                'flashblocks(/<action>(/<id>))'
              . '(/opts1-<options_count1>)'
              . '(/opts2-<options_count2>)'
              . '(/opts3-<options_count3>)'            
              . '(/$<history>)',
            array(
            'action'    => '\w++',
            'id'        => '\d++',
            'options_count1' => '\d++',
            'options_count2' => '\d++',
            'options_count3' => '\d++',
                
            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'flashblocks',
            'action'     => 'index',
            'id'         => NULL,
            'options_count1' => NULL,
            'options_count2' => NULL,
            'options_count3' => NULL           
        ));
    
    // ----- Add backend menu items
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => 2,
        
        'caption' => 'Flash',
        'route' => 'backend/flashblocks',
        'icon' => 'flashblocks'
    ));
}