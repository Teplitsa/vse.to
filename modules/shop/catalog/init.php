<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    // ----- products
    Route::add('frontend/catalog/products/control', new Route_Frontend(
                'catalog/products(/<action>(/<id>)(/type-<type_id>)(/product-<product_id>)(/user-<user_id>)(/lecturer-<lecturer_id>)(/opts-<options_count>)(/ids-<ids>))'
              . '(/group-<cat_sectiongroup_id>)(/section-<cat_section_id>)(/sections-<cat_section_ids>)'
              . '(/porder-<cat_porder>)(/pdesc-<cat_pdesc>)'
              . '(/page-<page>)'

              // search params
              . '(/search-<search_text>)'
              . '(/active-<active>)'
            ,
            array(                
                'action'    => '\w++',
                'type_id'        => '\d++',                
                'id'        => '\d++',
                'product_id' => '\d++',                
                'user_id' => '\d++',                
                'lecturer_id' => '\d++',                
                
                'options_count' => '\d++',
                
                'ids'       => '[\d_]++',

                'cat_sectiongroup_id'  => '\d++',
                'cat_section_id'  => '\d++',
                'cat_section_ids' => '[\d_]++',

                'cat_porder' => '\w++',
                'cat_pdesc'  => '[01]',
                'page'       => '(\d++|all)',

                'search_text'   => '\w++',
                'active'        => '[01]'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'index',
            'type_id'    => NULL,
            'id'         => NULL,
            'product_id' => NULL,
            'user_id' => NULL,            
            'lecturer_id' => NULL,            
            'options_count' => NULL,
            
            'ids'        => '',

            'cat_sectiongroup_id' => NULL,
            'cat_section_id'  => '0',
            'cat_section_ids' =>  '',

            'cat_porder' => 'id',
            'cat_pdesc'  => '1',
            'page'       => '0',

            'search_text'   => '',
            'active'        => '-1'
        ));    

    // ----- List of products in section
    Route::add('frontend/catalog/products', new Route_Frontend(
                'events(/p-<page>)(/format-<format>)(/theme-<theme>)(/calendar-<calendar>)'
            ,
            array(
                'page' => '(\d++|all)',
                'format'   => '\d++',
                'theme'   => '\d++',
                'calendar'   => '[P0-9MWD]+',
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'index',
            'page' => '0',
            'format'   => NULL,
            'theme'   => NULL ,
            'calendar'   => NULL
        ));

//    // ----- productcomments
//    Route::add('frontend/products/telemosts', new Route_Frontend(
//                'products/telemosts(/<action>(/<id>)(/product-<product_id>))'
//              . '(/~<history>)'
//            ,
//            array(
//                'action'     => '\w++',
//                'id'         => '\d++',
//                'product_id'   => '\d++',
//
//                'history'   => '.++'
//            )
//        ))
//        ->defaults(array(
//            'controller' => 'telemosts',
//            'action'     => 'index',
//            'id'         => NULL,
//            'product_id'   => NULL
//        ));
//    
    // ----- Search
    Route::add('frontend/catalog/search', new Route_Frontend(
                'catalog/search(/catalog-<sectiongroup_name>)(/text-<search_text>)'
              . '(/p-<page>)'
              . '(/date-<date>)'
              . '(/tag-<tag>)'
            ,
            array(
                'sectiongroup_name' => '\w++',
                'path' => '[/a-z0-9_-]+?',
                'page' => '(\d++|all)',

                'date' => '[0-9\.]++',
                'tag' => '[a-z0-9_-]++',
                'search_text' => '[a-z0-9]++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'search',
            'search_text' => '',

            'page' => '1',
            'tag'  => NULL,
            'price_from' => '',
            'price_to'   => ''
        ));

    // ----- Product
    Route::add('frontend/catalog/product', new Route_Frontend(
                'catalog-<sectiongroup_name>(/stage-<stage>)/<path>/<alias>.html(/image-<image_id>)'
            ,
            array(
                'sectiongroup_name' => '\w++',
                'stage' => '\w++',                
                'path'  => '[/a-z0-9_-]+?',
                'alias' => '[a-z0-9_-]++',                
                'image_id' => '\d++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'product',
            'stage' => NULL,
            'image_id' => NULL
        ));

    // ----- fullscreen comdi 
    Route::add('frontend/catalog/product/fullscreen', new Route_Frontend(
                'catalog/product/fullscreen(/width-<width>)(/height-<height>)/<alias>.html'
            ,
            array(
                'sectiongroup_name' => '\w++', 
                'path'  => '[/a-z0-9_-]+?',
                'alias' => '[a-z0-9_-]++',
                'width' => '\d++',
                'height' => '\d++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'fullscreen',
            'width' => NULL,
            'height' => NULL
        ));    
    // ----- Ajax Product
    /*Route::add('frontend/catalog/product/choose', new Route_Frontend(
                'choose-<alias>.html'
            ,
            array(
                'alias' => '[a-z0-9_-]++',                
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'choose',
        ));
    */
    
    // ----- Product
        Route::add('frontend/catalog', new Route_Frontend('(p-<page>)'
                ,array(
                    'page'       => '(\d++|all)',
                )))
            ->defaults(array(
                'controller' => 'products',
                'action'     => 'index',
                'page'       => '1'
            ));  

    // ----- product_choose
    Route::add('frontend/catalog/product/choose', new Route_Frontend(
                'catalog/product/choose-<alias>.html'
            ,
            array(
                'alias' => '[a-z0-9_-]++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_product_choose'
        ));

    // ----- product_choose
    Route::add('frontend/catalog/telemost/select', new Route_Frontend(
                'catalog/telemost/select-<telemost_id>'
            ,
            array(
                'telemost_id' => '\d++'
            )
        ))
        ->defaults(array(
            'controller' => 'telemosts',
            'action'     => 'ajax_telemost_select',
            'telemost_id'=> NULL
        ));
    
    // ----- product_choose
    Route::add('frontend/catalog/smallproduct/choose', new Route_Frontend(
                'catalog/smallproduct/choose-<alias>.html'
            ,
            array(
                'alias' => '[a-z0-9_-]++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_smallproduct_choose'
        ));
    
    // ----- product_choose
    Route::add('frontend/catalog/product/unchoose', new Route_Frontend(
                'catalog/product/unchoose-<alias>.html'
            ,
            array(
                'alias' => '[a-z0-9_-]++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_product_unchoose'
        ));

    // ----- product_choose
    Route::add('frontend/catalog/smallproduct/unchoose', new Route_Frontend(
                'catalog/smallproduct/unchoose-<alias>.html'
            ,
            array(
                'alias' => '[a-z0-9_-]++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_smallproduct_unchoose'
        ));

    // ----- product_choose
    Route::add('frontend/catalog/ajax_products', new Route_Frontend(
                'ajax_events'
              . '(/ap-<apage>)'
            ,
            array(
                'apage'         => '\d++',  
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_products',
            'apage'         => '0',
        ));

    // ----- product_choose
    Route::add('frontend/catalog/ajax_telemosts', new Route_Frontend(
                'ajax_telemosts'
              . '(/tp-<tpage>)'
            ,
            array(
                'tpage'         => '\d++',  
            )
        ))
        ->defaults(array(
            'controller' => 'telemosts',
            'action'     => 'ajax_telemosts',
            'tpage'         => '0',
        ));

    // ----- product_choose
    Route::add('frontend/catalog/ajax_app_telemosts', new Route_Frontend(
                'ajax_app_telemosts'
              . '(/rp-<rpage>)'
            ,
            array(
                'rpage'         => '\d++',  
            )
        ))
        ->defaults(array(
            'controller' => 'telemosts',
            'action'     => 'ajax_app_telemosts',
            'rpage'         => '0',
        ));

    // ----- product_choose
    Route::add('frontend/catalog/ajax_goes', new Route_Frontend(
                'ajax_goes'
              . '(/mp-<mpage>)'
            ,
            array(
                'mpage'         => '\d++',  
            )
        ))
        ->defaults(array(
            'controller' => 'telemosts',
            'action'     => 'ajax_goes',
            'mpage'         => '0',
        ));    
    // ----- product_choose
    Route::add('frontend/catalog/smallproduct/unrequest', new Route_Frontend(
                'catalog/smallproduct/unrequest-<alias>.html'
            ,
            array(
                'alias' => '[a-z0-9_-]++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_smallproduct_unrequest'
        ));
    
    // ----- product_images
    Route::add('frontend/catalog/product/images', new Route_Frontend(
                'catalog/product/images'
            ,
            array(
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_product_images'
        ));
    
    // ----- product_images
    Route::add('frontend/catalog/small_product', new Route_Frontend(
                'catalog/product/small'
            ,
            array(
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'ajax_small_product'
        ));    
    // ----- Product
        Route::add('frontend/catalog', new Route_Frontend('(p-<page>)'
                ,array(
                    'page'       => '(\d++|all)',
                )))
            ->defaults(array(
                'controller' => 'products',
                'action'     => 'index',
                'page'       => '1'
            ));  
}
/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    // ----- products
    Route::add('backend/catalog/products', new Route_Backend(
                'catalog/products(/<action>(/<id>)(/type-<type_id>)(/product-<product_id>)(/user-<user_id>)(/lecturer-<lecturer_id>)(/place-<place_id>)(/opts-<options_count>)(/ids-<ids>))'
              . '(/group-<cat_sectiongroup_id>)(/section-<cat_section_id>)(/sections-<cat_section_ids>)(/towns-<access_town_ids>)(/organizers-<access_organizer_ids>)(/users-<access_user_ids>)'
              . '(/porder-<cat_porder>)(/pdesc-<cat_pdesc>)'
              . '(/page-<page>)'

              // search params
              . '(/search-<search_text>)'
              . '(/active-<active>)'

              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'type_id'   => '\d++',                
                'id'        => '\d++',
                'product_id' => '\d++',                
                'user_id'    => '\d++',
                'lecturer_id'    => '\d++',                
                'place_id'    => '\d++',                
                'options_count' => '\d++',
                
                'ids'       => '[\d_]++',

                'cat_sectiongroup_id'  => '\d++',
                'cat_section_id'  => '\d++',
                'cat_section_ids' => '[\d_]++',
                'access_user_ids' => '[\d_]++',                
                'access_organizer_ids' => '[\d_]++',                
                'access_town_ids' => '[\d_]++',               
                'cat_porder' => '\w++',
                'cat_pdesc'  => '[01]',
                'page'       => '(\d++|all)',

                'search_text'   => '\w++',
                'active'        => '[01]',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'products',
            'action'     => 'index',
            'type_id'    => NULL,            
            'id'         => NULL,
            'product_id' => NULL,
            
            'user_id'    => NULL,
            'lecturer_id'   => NULL,
            'place_id'    => NULL,
            
            'options_count' => NULL,
            
            'ids'        => '',

            'cat_sectiongroup_id' => NULL,
            'cat_section_id'  => '0',
            'cat_section_ids' =>  '',
            'access_user_ids' =>  '',                        
            'access_organizer_ids' =>  '',            
            'access_town_ids' =>  '',

            'cat_porder' => 'id',
            'cat_pdesc'  => '1',
            'page'       => '0',

            'search_text'   => '',
            'active'        => '-1'
        ));

    // ----- sectiongroups
    Route::add('backend/catalog/sectiongroups', new Route_Backend(
                'catalog/sectiongroups(/<action>(/<id>))'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'sectiongroups',
            'action'     => 'index',
            'id'         => NULL
        ));

    // ----- sections
    Route::add('backend/catalog/sections', new Route_Backend(
                'catalog/sections(/<action>(/<id>)(/ids-<ids>)(/<toggle>))'
              . '(/group-<cat_sectiongroup_id>)'
              . '(/section-<cat_section_id>)'
              . '(/sorder-<cat_sorder>)(/sdesc-<cat_sdesc>)'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'ids'       => '[\d_]++',

                'toggle'    => '(on|off)',
                'cat_sectiongroup_id'=> '\d++',
                'cat_section_id'  => '\d++',

                'cat_sorder' => '\w++',
                'cat_sdesc'  => '[01]',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'sections',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',

            'cat_sorder' => 'lft',
            'cat_sdesc'  => '0',

            'toggle' => '',
            'cat_section_id'  => '0',
        ));

    // ----- properties
    Route::add('backend/catalog/properties', new Route_Backend(
                'catalog/properties(/<action>(/<id>))'
              . '(/sectiongroups-<cat_sectiongroup_ids>)'
              . '(/opts-<options_count>)'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'cat_sectiongroup_ids' => '[\d_]++',
                'options_count' => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'properties',
            'action'     => 'index',
            'id'         => NULL,
            'cat_sectiongroup_ids' => '',

            'options_count' => NULL
        ));

    // ----- plists
    Route::add('backend/catalog/plists', new Route_Backend(
                'catalog/plists(/<action>(/<id>))'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'plists',
            'action'     => 'index',
            'id'         => NULL
        ));

    // ----- plistproducts
    Route::add('backend/catalog/plistproducts', new Route_Backend(
                'catalog/plistproducts(/<action>(/<id>)(/ids-<ids>)(/product-<product_id>)(/plist-<plist_id>))'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',
                'ids'       => '[\d_]++',

                'product_id' => '\d++',
                'plist_id'   => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'plistproducts',
            'action'     => 'index',
            'id'         => NULL,
            'ids'        => '',

            'product_id' => NULL,
            'plist_id'   => NULL
        ));

    // ----- import
    Route::add('backend/catalog/import', new Route_Backend('catalog/import(/<action>)')
            ,
            array(
                'action' => '\w++'
            )
        )
        ->defaults(array(
            'controller' => 'catimport',
            'action'     => 'index',
        ));

    // ----- catalog
    Route::add('backend/catalog', new Route_Backend(
                'catalog(/<action>(/<id>))'
              . '(/group-<cat_sectiongroup_id>)'
              . '(/section-<cat_section_id>)(/p-<page>)(/tab-<tab>)'
              . '(/product-<product_id>)'
              . '(/sorder-<cat_sorder>)(/sdesc-<cat_sdesc>)'


              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'id'        => '\d++',

                'cat_sectiongroup_id' => '\d++',
                'cat_section_id'  => '\d++',
                'page'      => '\d++',
                'tab'       => '\w++',

                'product_id' => '\d++',

                'cat_porder'    => '\w++',
                'cat_pdesc'     => '[01]',
                'cat_sorder'    => '\w++',
                'cat_sdesc'     => '[01]',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'catalog',
            'action'     => 'index',
            'id'         => NULL,

            'cat_sectiongroup_id' => '0',
            'cat_section_id'  => '0',
            'page'            => '0',
            'tab'             => 'catalog',

            'product_id' => NULL,

            'cat_sorder'    => 'lft',
            'cat_sdesc'     => '0',
            'cat_prorder'   => 'position',
            'cat_prdesc'    => '0',

        ));
    // ----- productcomments
    Route::add('backend/products/telemosts', new Route_Backend(
                'products/telemosts(/<action>(/<id>)(/product-<product_id>))'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'id'         => '\d++',
                'product_id'   => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'telemosts',
            'action'     => 'index',
            'id'         => NULL,
            'product_id'   => NULL
        ));    

    // ----- productcomments
    Route::add('backend/products/goes', new Route_Backend(
                'products/goes(/<action>(/<id>)(/telemost-<telemost_id>))'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'id'         => '\d++',
                'telemost_id'   => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'goes',
            'action'     => 'index',
            'id'         => NULL,
            'telemost_id'   => NULL
        ));    
    
    // ----- productcomments
    Route::add('backend/products/comments', new Route_Backend(
                'products/comments(/<action>(/<id>)(/product-<product_id>))'
              . '(/~<history>)'
            ,
            array(
                'action'     => '\w++',
                'id'         => '\d++',
                'product_id'   => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'productcomments',
            'action'     => 'index',
            'id'         => NULL,
            'product_id'   => NULL
        ));    

    // ----- Add backend menu items
    $parent_id = Model_Backend_Menu::add_item(array(
        'menu' => 'main',
        'site_required' => TRUE,
        'id' => 3,

        'caption' => 'Каталог',
        'route' => 'backend/catalog/products',
        'select_conds' => array(
            array('route' => 'backend/catalog/sectiongroups'),
            array('route' => 'backend/catalog/sections'),
            array('route' => 'backend/catalog/products'),
            array('route' => 'backend/catalog/properties'),
            array('route' => 'backend/catalog/plists'),
            array('route' => 'backend/catalog/plistproducts'),
            array('route' => 'backend/catalog/import'),
            array('route' => 'backend/catalog')
        ),

        'icon' => 'catalog'
    ));

        $parent_id2 = Model_Backend_Menu::add_item(array(
            'menu' => 'main',
            'parent_id' => $parent_id,
            'site_required' => TRUE,

            'caption' => 'События',
            'route' => 'backend/catalog/products',
        ));

            Model_Backend_Menu::add_item(array(
                'menu' => 'main',
                'parent_id' => $parent_id2,
                'site_required' => TRUE,

                'caption' => 'Выбор событий',
                'route' => 'backend/catalog/products',
                'select_conds' => array(
                    array('route' => 'backend/catalog/products', 'route_params' => array('action' => 'products_select')),
                )
            ));

        Model_Backend_Menu::add_item(array(
            'menu' => 'main',
            'parent_id' => $parent_id,
            'site_required' => TRUE,

            'caption' => 'Разделы',
            'route' => 'backend/catalog/sections'
        ));

        Model_Backend_Menu::add_item(array(
            'menu' => 'main',
            'parent_id' => $parent_id,
            'site_required' => TRUE,

            'caption' => 'Характеристики',
            'route' => 'backend/catalog/properties'
        ));


        Model_Backend_Menu::add_item(array(
            'menu' => 'main',
            'parent_id' => $parent_id,
            'site_required' => TRUE,

            'caption' => 'Группы категорий',
            'route' => 'backend/catalog/sectiongroups'
        ));
}

/******************************************************************************
 * Common
 ******************************************************************************/
Model_Privilege::add_privilege_type('products_control', array(
    'name'  => 'Управление анонсами событий',
    'readable' => TRUE,
    'controller' => 'products',
    'action'     => 'control',       
    'frontend_route' => 'frontend/catalog/products/control',
    'frontend_route_params' => array('action' => 'control'),  
));

// ----- Add privilege types
Model_Privilege::add_privilege_type('announce_create', array(
    'name' => 'Создание анонса',
    'readable' => FALSE,    
    'controller' => 'products',
    'action'     => 'create'      
));
Model_Privilege::add_privilege_type('announce_update', array(
    'name' => 'Редактирование анонса',
    'readable' => FALSE,
    'controller' => 'products',
    'action'     => 'update'    
));
Model_Privilege::add_privilege_type('announce_delete', array(
    'name' => 'Удаление анонса',
    'readable' => FALSE,
    'controller' => 'products',
    'action'     => 'delete'
));
Model_Privilege::add_privilege_type('announce_users', array(
    'name' => 'Организаторы События',
    'readable' => FALSE,    
    'system' => TRUE
));
Model_Privilege::add_privilege_type('start_event', array(
    'name' => 'Начать Мероприятие',
    'readable' => FALSE,    
    'controller' => 'products',
    'action'     => 'product',
    'route_params' => array('stage' => Model_Product::START_STAGE)    
));
Model_Privilege::add_privilege_type('stop_event', array(
    'name' => 'Завершить Мероприятие',
    'readable' => FALSE,    
    'controller' => 'products',
    'action'     => 'product',
    'route_params' => array('stage' => Model_Product::STOP_STAGE)    
));

//Model_Privilege::add_privilege_type('events_control', array(
//    'name'  => 'Управление Событиями',
//    'readable' => TRUE,    
//    'controller' => 'products',
//    'action'     => 'eventcontrol',
//    'frontend_route' => 'frontend/catalog/products/control',
//    'frontend_route_params' => array('action' => 'eventcontrol'),  
//));
//// ----- Add privilege types
//Model_Privilege::add_privilege_type('event_create', array(
//    'name' => 'Создание event-a',
//    'readable' => FALSE,    
//    'controller' => 'products',
//    'action'     => 'create',
//    'route_params' => array('type_id' => Model_SectionGroup::TYPE_EVENT)      
//));
//Model_Privilege::add_privilege_type('event_update', array(
//    'name' => 'Редактирование event-a',
//    'readable' => FALSE,
//    'controller' => 'products',
//    'action'     => 'update',
//    'route_params' => array('type_id' => Model_SectionGroup::TYPE_EVENT)    
//));
//Model_Privilege::add_privilege_type('event_delete', array(
//    'name' => 'Удаление event-a',
//    'readable' => FALSE,
//    'controller' => 'products',
//    'action'     => 'delete',
//    'route_params' => array('type_id' => Model_SectionGroup::TYPE_EVENT)
//));

// Add node types

// Use text page as index page for site    
Model_Node::add_node_type('indexpage', array(
    'name' => 'Главная страница',
    'backend_route'  => 'backend/catalog/products',
    'frontend_route' => 'frontend/catalog'
));


/******************************************************************************
 * Module installation
 ******************************************************************************/
if (Kohana::$environment !== Kohana::PRODUCTION)
{
    // Create main sectiongroup and  section
    $sectiongroup = new Model_SectionGroup();

    if ( ! $sectiongroup->count())
    {
        $sectiongroup->properties(array(
            'system'    => 1,
            'site_id'   => (int)Model_Site::current()->id,
            'name'      => 'event',
            'caption'   => 'События'));
        $sectiongroup->save();

        $section = new Model_Section();

        if ( $section->count()) $section->delete_all_by();
        
        $section = new Model_Section(array(
            'system' => 1, 
            'id' => Model_Section::EVENT_ID,
            'caption' => 'События',
            'sectiongroup_id' => $sectiongroup->id));

        $section->save(true);
    }
}