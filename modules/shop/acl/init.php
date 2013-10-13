<?php defined('SYSPATH') or die('No direct script access.');

if (APP === 'FRONTEND') {


    // ----- registration
    Route::add('registration', new Route_Frontend(
                'registration'
            ,
            array(
            )
        ))
        ->defaults(array(
            'controller' => 'users',
            'action'     => 'create'
        ));

    // ----- users
    Route::add('frontend/acl/users/control', new Route_Frontend(
                'acl/users(/<action>)(/user-<user_id>)(/image-<image_id>)'
              . '(/ap-<apage>)(/tp-<tpage>)(/rp-<rpage>)(/mp-<mpage>)'
              . '(/opts-<options_count>)'            
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'user_id' => '\d++',
                'apage'         => '\d++',
                'tpage'         => '\d++',
                'rpage'         => '\d++',
                'mpage'         => '\d++',                
                'history'   => '.++',
                'image_id' => '\d++',
                'options_count' => '\d++',                
                
            )
        ))
        ->defaults(array(
            'controller' => 'users',
            'action'     => 'index',
            'user_id' => NULL,
            'image_id' => NULL,
            'options_count' => NULL,
            'apage'         => '0',
            'tpage'         => '0',
            'rpage'         => '0',
            'mpage'         => '0',            
        ));

    // ----- user_images
    Route::add('frontend/acl/user/images', new Route_Frontend(
                'acl/user/images'
            ,
            array(
            )
        ))
        ->defaults(array(
            'controller' => 'users',
            'action'     => 'ajax_user_images'
        ));
    
    // ----- lecturers
    Route::add('frontend/acl/lecturers', new Route_Frontend(
                '(/<town>)acl/lecturers(/<action>)(/lecturer-<lecturer_id>)(/image-<image_id>)'
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'lecturer_id'        => '\d++',
            'image_id' => '\d++',
            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'lecturers',
            'action'     => 'index',
            'image_id' => NULL,
            'lecturer_id'         => '0',
        ));
    
    // ----- organizers
    Route::add('frontend/acl/organizers', new Route_Frontend(
                'acl/organizers(/<action>)(/organizer-<organizer_id>)(/image-<image_id>)'
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'organizer_id'        => '\d++',
            'image_id' => '\d++',
            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'organizers',
            'action'     => 'index',
            'image_id' => NULL,
            'organizer_id'         => '0',
        ));
    
    // ----- lecturer_images
    Route::add('frontend/acl/lecturer/images', new Route_Frontend(
                'acl/lecturer/images'
            ,
            array(
            )
        ))
        ->defaults(array(
            'controller' => 'lecturers',
            'action'     => 'ajax_lecturer_images'
        ));
    
    // ----- acl
    Route::add('frontend/acl', new Route_Frontend(
            'acl(/<action>)(/user-<user_id>)(/group-<group_id>)(/lecturer-<lecturer_id>)(/stat-<stat>)(/code-<hash>)',            
    array(
        'action'    => '\w++',
        'user_id'   => '\d++',
        'group_id'  => '\d++',        
        'lecturer_id'  => '\d++',
        'stat'  => '\w++',
        'hash'   => '.++'
        
    )))
    ->defaults(array(
        'controller' => 'acl',
        'action'     => 'index',
        'user_id'   => NULL,
        'group_id'  => NULL,
        'lecturer_id'  => '0',
        'stat' => NULL,
    ));    
}
if (APP === 'BACKEND')
{
    // ----- users
    Route::add('backend/acl/users', new Route_Backend(
                'acl/users(/<action>(/<id>)(/ids-<ids>)(/v-<v_action>))'
              . '(/group-<group_id>)'
              . '(/opts-<options_count>)'             
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[\d_]++',
            'v_action'  => '\w++',

            'group_id'  => '\d++',
            'options_count' => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'users',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',
            'v_action'   => NULL,
            'options_count' => NULL,
            'group_id'  => NULL,
        ));

    // ----- groups
    Route::add('backend/acl/groups', new Route_Backend(
                'acl/groups(/<action>(/<id>))'
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'id'        => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'groups',
            'action'     => 'index',
            'id'         => NULL
        ));
        // ----- privileges
        Route::add('backend/acl/privileges', new Route_Backend(
                'acl/privileges(/<action>(/<id>))'
              . '(/opts-<options_count>)'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',

                'options_count' => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'privileges',
            'action'     => 'index',
            'id'         => NULL,

            'options_count' => NULL
        ));

    // ----- lecturers
    Route::add('backend/acl/lecturers', new Route_Backend(
                'acl/lecturers(/<action>(/<id>)(/ids-<ids>))'
              . '(/opts-<options_count>)' 
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[\d_]++',
            'options_count' => '\d++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'lecturers',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',
            
            'options_count' => NULL            
        ));

    // ----- lecturers
    Route::add('backend/acl/organizers', new Route_Backend(
                'acl/organizers(/<action>(/<id>)(/ids-<ids>))'
              . '(/opts-<options_count>)' 
              . '(/oorder-<acl_oorder>)(/odesc-<acl_odesc>)'            
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'id'        => '\d++',
            'ids'       => '[\d_]++',
            'options_count' => '\d++',
            'acl_oorder' => '\w++',
            'acl_odesc'  => '[01]',
            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'organizers',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',
            'acl_oorder' => 'name',
            'acl_odesc'  => '0',             
            'options_count' => NULL            
        ));    
        
        // ----- userprops
        Route::add('backend/acl/userprops', new Route_Backend(
                'acl/userprops(/<action>(/<id>))'
              . '(/opts-<options_count>)'
              . '(/uprorder-<acl_uprorder>)(/uprdesc-<acl_uprdesc>)'                
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'options_count' => '\d++',
                'acl_uprorder'    => '\w++',
                'acl_uprdesc'     => '\w++',
                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'userprops',
            'action'     => 'index',
            'id'         => NULL,
            'acl_uprorder'    => 'position',
            'acl_uprdesc'     => '0',
            'options_count' => NULL
        ));

        // ----- links
        Route::add('backend/acl/links', new Route_Backend(
                'acl/links(/<action>(/<id>))'
              . '(/opts-<options_count>)'
              . '(/uliorder-<acl_uliorder>)(/ulidesc-<acl_ulidesc>)' 
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'options_count' => '\d++',
                'acl_uliorder'    => '\w++',
                'acl_ulidesc'     => '\w++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'links',
            'action'     => 'index',
            'id'         => NULL,
            'acl_uliorder'    => 'position',
            'acl_ulidesc'     => '0',
            'options_count' => NULL
        ));
        
        // ----- acl
        Route::add('backend/acl', new Route_Backend(
                'acl(/<action>(/<id>)(/v-<v_action>))'
              . '(/user-<user_id>)(/group-<group_id>)(/lecturer-<lecturer_id>)(/p-<page>)(/tab-<tab>)'
              . '(/uorder-<acl_uorder>)(/udesc-<acl_udesc>)(/gorder-<acl_gorder>)(/gdesc-<acl_gdesc>)'
              . '(/~<history>)',
        array(
            'action'    => '\w++',
            'id'        => '\d++',
            'v_action'  => '\w++',

            'user_id'   => '\d++',
            'group_id'  => '\d++',
            'lecturer_id'    => '\d++',                
            
            'page'         => '\d++',
            'tab'          => '\w++',

            'acl_uorder'    => '\w++',
            'acl_udesc'     => '\w++',
            'acl_gorder'    => '\w++',
            'acl_gdesc'     => '\w++',

            'history'   => '.++'
        )))
        ->defaults(array(
            'controller' => 'acl',
            'action'     => 'index',
            'id'         => NULL,
            'v_action'   => NULL,

            'user_id'      => '0',
            'group_id'     => '0',
            'lecturer_id'     => '0',            
            'page'         => '0',
            'tab'          => 'users',

            'acl_uorder'    => 'id',
            'acl_udesc'     => '0',
            'acl_gorder'    => 'id',
            'acl_gdesc'     => '0'
        ));


    // ----- Add backend menu items
    $parent_id = Model_Backend_Menu::add_item(array(
        'id'  => 4,
        'menu' => 'main',

        'caption' => 'Пользователи',
        'route' => 'backend/acl',
        'select_conds' => array(
            array('route' => 'backend/acl'),
            array('route' => 'backend/acl/users'),
            array('route' => 'backend/acl/groups'),
            array('route' => 'backend/acl/privileges'),
            array('route' => 'backend/acl/lecturers'),
            array('route' => 'backend/acl/organizers'),            
            array('route' => 'backend/acl/userprops'),
            array('route' => 'backend/acl/links')            
        ),
        'icon' => 'acl'
    ));
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Пользователи',
        'route' => 'backend/acl'
    ));
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Привилегии',
        'route' => 'backend/acl/privileges'
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Лекторы',
        'route' => 'backend/acl/lecturers'
    ));
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Организации',
        'route' => 'backend/acl/organizers'
    ));    
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Доп.характеристики',
        'route' => 'backend/acl/userprops'
    ));    
    
    Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'parent_id' => $parent_id,
        'site_required' => TRUE,

        'caption' => 'Внешние ссылки',
        'route' => 'backend/acl/links'
    ));    
}

/******************************************************************************
 * Module installation
 ******************************************************************************/
if (Kohana::$environment !== Kohana::PRODUCTION)
{
    // Create superusers group and superuser
    $group = new Model_Group();

    if ( ! $group->count())
    {
        $group->properties(array('system' => 1));
        $group->id = Model_Group::ADMIN_GROUP_ID;
        $group->name = 'Суперпользователи';
        $group->save(TRUE);

        $organizer = new Model_Organizer();
        $organizer->id = Model_Organizer::DEFAULT_ORGANIZER_ID;
        $organizer->name = Model_Organizer::DEFAULT_ORGANIZER_NAME;
        $organizer->type = Model_Organizer::TYPE_TVGROUP;        
        $organizer->town_id = Model_Town::DEFAULT_TOWN_ID;
        $organizer->address = "Москва, Нахимовский пр. 47";
        $organizer->save(TRUE);
        
        $user = new Model_User(array('system' => 1, 'group_id' => $group->id));
        $user->email = 'ivanovser@list.ru';
        $user->password = 'root';
        $user->first_name = 'Администратор';
        $user->organizer_id = Model_Organizer::DEFAULT_ORGANIZER_ID;
        $user->organizer_name = Model_Organizer::DEFAULT_ORGANIZER_NAME;
        $user->town_id = Model_Town::DEFAULT_TOWN_ID;
        $user->save();
        
        $group->properties(array('system' => 1));
        $group->id = Model_Group::EDITOR_GROUP_ID;        
        $group->name = 'Редакторы';
        $group->save(TRUE);

        $group->properties(array('system' => 1));
        $group->id = Model_Group::USER_GROUP_ID;        
        $group->name = 'Пользователи';
        $group->save(TRUE);
                
    // Create system privileges
    $privilege = new Model_Privilege();

        if ( ! $privilege->count())
        {
            $privilege->properties(array(
                'site_id' => Model_Site::current()->id,
                'system' => 1));
            $privilege->name = 'backend_access';
            $privilege->caption = 'Доступ в панель управления';

            $privilege->save();

            $privilege_group = new Model_PrivilegeGroup();
            $privilege_group->privilege_id = $privilege->id;
            $privilege_group->group_id = $group->id;
            $privilege_group->save();
        }    
    }
}

// ----- Add privilege types
Model_Privilege::add_privilege_type('backend_access', array(
    'name' => 'Панель управления',
    'readable' => FALSE,    
    'system' => TRUE
));

Model_Privilege::add_privilege_type('users_control', array(
    'name'  => 'Просмотр профайла',
    'readable' => FALSE,
    'controller' => 'users',
    'action'     => 'control',       
    'frontend_route' => 'frontend/acl/users/control',
    'frontend_route_params' => array('action' => 'control'),  
));
Model_Privilege::add_privilege_type('user_update', array(
    'name' => 'Редактировать Профайл',
    'readable' => FALSE,    
    'controller' => 'users',
    'action'     => 'update'   
));
/*
// ----- Add privilege types
Model_Privilege::add_privilege_type('product_create', array(
    'name' => 'Создание мероприятия',
    'readable' => FALSE,    
    'controller' => 'products',
    'action'     => 'create'   
));
Model_Privilege::add_privilege_type('product_update', array(
    'name' => 'Редактирование мероприятия',
    'readable' => FALSE,
    'controller' => 'products',
    'action'     => 'update'
));
Model_Privilege::add_privilege_type('product_delete', array(
    'name' => 'Удаление мероприятия',
    'readable' => FALSE,
    'controller' => 'products',
    'action'     => 'delete'
));
*/
