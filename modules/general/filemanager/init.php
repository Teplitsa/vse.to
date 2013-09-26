<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/filemanager', new Route_Backend(
                'filemanager(/<action>)(/path-<fm_path>)'
              . '(/root-<fm_root>)(/style-<fm_style>)(/tinymce-<fm_tinymce>)'
              . '(/order-<fm_order>)(/desc-<fm_desc>)'
              . '(/~<history>)',
            array(
            'action'    => '\w++',
            'fm_path'   => '[0-9a-fA-F]++', //path is encoded by URL::encode()

            'fm_root'    => '\w++',
            'fm_style'   => '\w++',
            'fm_tinymce' => '[01]',

            'fm_order'  => '\w++',
            'fm_desc'   => '\w++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'filemanager',
            'action'     => 'index',
            'fm_path'    => '',

            'fm_root'    => 'files',
            'fm_style'   => 'list',
            'fm_tinymce' => '0',

            'fm_order'  => 'name',
            'fm_desc'   => '1'
        ));

    // ----- Add backend menu items
    $parent_id = Model_Backend_Menu::add_item(array(
        'menu' => 'sidebar',
        
        'caption' => 'Файловый менеджер',
        'route' => 'backend/filemanager',
        'icon' => 'filemanager'
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'sidebar',
        'parent_id' => $parent_id,

        'caption' => 'Файлы пользователя',
        'route' => 'backend/filemanager',
        'route_params' => array('fm_root' => 'files')
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'sidebar',
        'parent_id' => $parent_id,

        'caption' => 'Оформление',
        'route' => 'backend/filemanager',
        'route_params' => array('fm_root' => 'css')
    ));

    Model_Backend_Menu::add_item(array(
        'menu' => 'sidebar',
        'parent_id' => $parent_id,

        'caption' => 'Яваскрипты',
        'route' => 'backend/filemanager',
        'route_params' => array('fm_root' => 'js')
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'sidebar',
        'parent_id' => $parent_id,

        'caption' => 'Шаблоны',
        'route' => 'backend/filemanager',
        'route_params' => array('fm_root' => 'templates')
    ));    
}