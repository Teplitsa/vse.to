<?php defined('SYSPATH') or die('No direct script access.');

class Gmap {
    
    protected static $_initialized = FALSE;
    
    protected static $_gmap = FALSE;
    
    public static function instance()
    {
        if (!self::$_initialized) {
            require_once Modules::path('gmap') . '/lib/GoogleMap.php';
            require_once Modules::path('gmap') . '/lib/JSMin.php';
            
            self::$_gmap =  new GoogleMapAPI();
        }
        return self::$_gmap;
    }

    protected function  __construct()
    {
        // This is a static class
    }
}