<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database query builder.
 * Added support to specify pure
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Database_Query_Builder extends Kohana_Database_Query_Builder {

	/**
	 * Compiles an array of conditions into an SQL partial. Used for WHERE
	 * and HAVING.
     *
     * Allow to specify WHERE conditions as Database_Query objects
	 *
	 * @param   object  Database instance
	 * @param   array   condition statements
	 * @return  string
	 */
	protected function _compile_conditions(Database $db, array $conditions)
	{
        if ( ! count($conditions))
        {
            return '';
        }

        if ($conditions[0] instanceof Database_Expression_Where)
        {
            return $conditions[0]->compile($db);
        }
        else
        {
            return parent::compile_conditions($db, $conditions);
        }
	}

}