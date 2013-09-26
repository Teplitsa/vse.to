<?php defined('SYSPATH') or die('No direct script access.');

class Twig {

    /**
     * @var array Twig_Environment
     */
    protected static $_twigs = array();

    protected static $_initialized = FALSE;


    /**
     * Setup twig template engine
     *
     * @return Twig_Environment
     */
    public static function instance($loader_class = 'kohana')
    {
        if ( ! isset(self::$_twigs[$loader_class]))
        {
            self::init();

            $loader = 'Twig_Loader_' . ucfirst($loader_class);
            $loader = new $loader();

            self::$_twigs[$loader_class] = new Twig_Environment($loader, array(
                'debug' => (Kohana::$environment !== Kohana::PRODUCTION),
                'cache' => APPPATH.'cache/twig'
            ));
        }

        return self::$_twigs[$loader_class];
    }

    /**
     * Setup twig autoloaders
     */
    public static function init()
    {
        if ( ! self::$_initialized)
        {
            require_once Modules::path('twig') . '/lib/Twig/Autoloader.php';
            Twig_Autoloader::register();

            self::$_initialized = TRUE;
        }
    }

    protected function  __construct()
    {
        // This is a static class
    }
}