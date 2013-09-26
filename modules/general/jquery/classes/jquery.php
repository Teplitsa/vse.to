<?php defined('SYSPATH') or die('No direct script access.');

class jQuery
{
    /**
     * @var boolean
     */
    protected static $_scripts_added = FALSE;

    /**
     * Add jQuery scripts to the layout
     */
        
    public static function add_scripts()
    {
        if (self::$_scripts_added)
            return;

        $layout = Layout::instance();
        $layout->add_script(Modules::uri('jquery') . '/public/js/jquery-1.7.2.min.js');
        $layout->add_script(Modules::uri('jquery') . '/public/js/jquery-ui.min.js');        
        $layout->add_script(Modules::uri('jquery') . '/public/js/jquery-ui-sliderAccess.js');
        $layout->add_script(Modules::uri('jquery') . '/public/js/jquery-ui-timepicker-addon.js');
        
        $layout->add_style(Modules::uri('jquery') . '/public/css/jquery-ui-timepicker-addon.css');        
        $layout->add_style(Modules::uri('jquery') . '/public/css/jquery-ui-1.8.22.custom.css');        
        
        self::$_scripts_added = TRUE;
    }        
}