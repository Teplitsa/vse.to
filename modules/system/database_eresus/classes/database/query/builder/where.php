<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database query builder for WHERE statements.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Database_Query_Builder_Where extends Kohana_Database_Query_Builder_Where {

    /**
     * Maintained as array to preserve compatibility with _compile_conditions(),
     * which requires the second parameter, conditions, to be an array
     * @var Database_Expression_Where
     */
	protected $_where;

    /**
     * @return Database_Expression_Where
     */
    public function get_where()
    {
        if ($this->_where === NULL)
        {
            $this->_where = array(new Database_Expression_Where());
        }

        return $this->_where[0];
    }

	/**
	 * Creates a new "AND WHERE" condition for the query.
	 *
	 * @param   mixed   column name or array($column, $alias) or Database_Query_Builder_Where object
	 * @param   string  logic operator
	 * @param   mixed   column value or object
	 * @return  $this
	 */
	public function and_where($column, $op, $value)
	{
        $this->get_where()->and_where($column, $op, $value);
		return $this;
	}

	/**
	 * Creates a new "OR WHERE" condition for the query.
	 *
	 * @param   mixed   column name or array($column, $alias) or Database_Query_Builder_Where object
	 * @param   string  logic operator
	 * @param   mixed   column value
	 * @return  $this
	 */
	public function or_where($column, $op, $value)
	{
        $this->get_where()->or_where($column, $op, $value);
		return $this;
	}

	/**
	 * Opens a new "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_where_open()
	{
        $this->get_where()->and_where_open();
		return $this;
	}

	/**
	 * Opens a new "OR WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_where_open()
	{
        $this->get_where()->or_where_open();
		return $this;
	}

	/**
	 * Closes an open "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_where_close()
	{
        $this->get_where()->and_where_close();
		return $this;
	}

	/**
	 * Closes an open "OR WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_where_close()
	{
        $this->get_where()->or_where_close();
		return $this;
	}

} // End Database_Query_Builder_Where