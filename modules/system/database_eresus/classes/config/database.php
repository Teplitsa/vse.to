<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database-based configuration loader.
 *
 * Schema for configuration table:
 *
 *     group_name    varchar(128)
 *     config_key    varchar(128)
 *     config_value  text
 *     primary key   (group_name, config_key)
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Config_Database extends Kohana_Config_Database {

	public function __construct(array $config = NULL)
	{
        // In development environment use dbtable to automatically create/update needed table
        if (Kohana::$environment !== Kohana::PRODUCTION)
        {
            $dbtable = new DbTable_Config(NULL, $this->_database_table);
        }

		parent::__construct($config);
	}

}
