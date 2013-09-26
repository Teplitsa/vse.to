<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database object creation helper methods.
 *
 * Added WHERE expression helper
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class DB extends Kohana_DB {

	/**
	 * Create a new WHERE expression
	 *
	 * @return  Database_Expression_Where
	 */
	public static function where($operand1 = NULL, $operator = NULL, $operand2 = NULL)
	{
		$where = new Database_Expression_Where();
        if ($operand1 !== NULL || $operator !== NULL || $operand2 !== NULL)
        {
            $where->and_where($operand1, $operator, $operand2);
        }

        return $where;
	}
} // End DB