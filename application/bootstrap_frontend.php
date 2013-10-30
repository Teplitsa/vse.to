<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Moscow');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'ru_RU.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

// Current application
define('APP', 'FRONTEND');

// Environment
//Kohana::$environment = Kohana::PRODUCTION;

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => BASE_URL,
	'index_file' => FALSE,
    'caching'    => (Kohana::$environment == Kohana::PRODUCTION),
    'cache_dir'  => APPPATH.'cache/core/frontend',
    'profile'    => (Kohana::$environment != Kohana::PRODUCTION)
));

/**
 * Set up language
 */
I18n::lang('ru-ru');

/**
 * Attach the file write to logging. Multiple writers are supported.
 */

Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */

Kohana::modules(array(
        'database_eresus' => MODPATH.'system/database_eresus',
        'database'        => MODPATH.'system/database',
        'image_eresus'    => MODPATH.'system/image_eresus',
        'image'           => MODPATH.'system/image',
        'forms'           => MODPATH.'system/forms',
        'history'         => MODPATH.'system/history',
        'widgets'         => MODPATH.'system/widgets',
        'tasks'           => MODPATH.'system/tasks',
        'tinymce'         => MODPATH.'general/tinymce',

        'jquery'  => MODPATH.'general/jquery',
        'menus'   => MODPATH.'general/menus',
        'menus'   => MODPATH.'general/menus',
        'news'   => MODPATH.'general/news',    
        'nodes'   => MODPATH.'general/nodes',
        'pages'   => MODPATH.'general/pages',
        'feedback' => MODPATH.'general/feedback',
        'flash'   => MODPATH. 'general/flash',
        'calendar' => MODPATH. 'general/calendar',    
        'chat'   => MODPATH. 'general/chat', 
        'images'  => MODPATH.'general/images',
        'tags'    => MODPATH.'general/tags',
        'breadcrumbs' => MODPATH.'general/breadcrumbs',
        'xls_reader' => MODPATH.'general/xls_reader',
        'blocks'  => MODPATH.'general/blocks',
        'twig'    => MODPATH.'general/twig',
        'swift_mailer' => MODPATH.'general/swift_mailer',

        'sites'     => MODPATH.'shop/sites',
        'acl'       => MODPATH.'shop/acl',
        'area' => MODPATH.'shop/area',    
        'towns' => MODPATH.'shop/towns',
        'catalog'   => MODPATH.'shop/catalog',

        'frontend'  => MODPATH.'frontend',
    
        'ulogin'  => MODPATH.'general/ulogin',
	));

/**
 * Attach a database reader to config, lowest priority
 */
//Kohana::$config->attach(new Config_Database, FALSE);

/**
 * Default route. Used when all other routes fail matching. Matches every URL and executes 404 action
 */
Route::add('404', new Route_Frontend('<uri>', array('uri' => '.*')))
	->defaults(array(
		'controller' => 'controller_errors',
        'action'     => 'error',

        'uri'        => '',
		'status'     => 404,
	));

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */

echo Request::instance()
	->execute()
	->send_headers()
	->response;