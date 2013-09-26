<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CMS modules manager
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Modules {

    /**
     * Modules uri cache
     * @var array
     */
    protected static $_uris;

    /**
     * Return path to module or FALSE if module was not registered
     *
     * @param  string $module Module name
     * @return string
     */
    public static function path($module)
    {
        $modules = Kohana::modules();
        if ( ! isset($modules[$module]))
            return FALSE;

        return $modules[$module];
    }

    /**
     * Return URI to module
     *
     * @param  string $module
     * @return string|boolean
     */
    public static function uri($module)
    {
        if ( ! isset(self::$_uris[$module]))
        {
            $path = Modules::path($module);
            if ($path === FALSE)
                return FALSE;

            self::$_uris[$module] = File::uri($path);
        }

        return self::$_uris[$module];
    }

    /**
     * Has the specified module been registrered?
     *
     * @param  string $module
     * @return boolean
     */
    public static function registered($module)
    {
        $modules = Kohana::modules();
        return (isset($modules[$module]));
    }

    /**
     * Load module config
     * Try to load from database config and fall back to file config on failure
     * 
     * @param string $db_config_group
     * @param string $file_config_group
     * @return array | FALSE
     */
    public static function load_config($db_config_group, $file_config_group = NULL)
    {
        // First try to read site-specific config from database
        $reader = new Config_Database();
        $config = $reader->load($db_config_group);
        if ($config !== FALSE)
        {
            $config = (array) $config;
        }
        elseif ($file_config_group !== NULL)
        {
            // Try to read default config from file
            $reader = new Kohana_Config_File();            
            $config = $reader->load($file_config_group);
            if ($config !== FALSE)
            {
                $config = (array) $config->as_array();
            }
            else
            {
                $config = FALSE;
            }
        }
        else
        {
            $config = FALSE;
        }

        return $config;
    }

    /**
     * Save module config to database
     *
     * @param string $db_config_group
     * @param array $values
     */
    public static function save_config($db_config_group, $values)
    {
        if (empty($values))
        {
            return;
        }
        
        $reader = new Config_Database();
        $config = $reader->load($db_config_group);
        if ($config === FALSE)
        {
            $config = $reader->load($db_config_group, array());
        }

        foreach ($values as $k => $v)
        {
            // offsetSet is overloaded in Config_Dataabase so that the following
            // assignment acuatally writes values to database
            $config[$k] = $v;
        }
    }

	final private function __construct()
	{
		// This is a static class
	}
}