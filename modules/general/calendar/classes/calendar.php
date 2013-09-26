<?php defined('SYSPATH') or die('No direct script access.');

class Calendar {
    /**
     * @var boolean
     */
    protected static $_scripts_added = FALSE;

    /**
     * Calendar instance
     * @var Layout
     */
    protected static $_instance;
    
    /**
     * Add jQuery scripts to the layout
     */
    public static function add_scripts()
    {
        if (self::$_scripts_added)
            return;

        $layout = Layout::instance();
        $layout->add_style(Modules::uri('calendar') . '/public/css/calendar.css');
        self::$_scripts_added = TRUE;
    }
    
    public static function instance() {
        if (self::$_instance === NULL)
        {
            self::add_scripts();
            self::$_instance = new Base_Calendar('ru');
        }
        return self::$_instance;    
    }
 
    protected function  __construct()
    {
        // This is a static class
    }    
}