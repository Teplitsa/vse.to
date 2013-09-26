<?php defined('SYSPATH') or die('No direct script access.');
/**
 * "Where" database expression
 * Can be used as standalone expression (DB::where)
 * To expressions can be combined together:
 *  $where1 = DB::where('foo', '=', 'bar');
 *  $where2 = DB::where('la', '=', 'me')->and_where($where1);
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Database_Expression_Where {

	/**
     * @var array
     */
	protected $_where = array();

    /**
     * Cache compiled statement
     * @var string
     */
    protected $_compiled_sql;

	/**
	 * Creates a new "AND WHERE" condition for the query.
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   string  logic operator
	 * @param   mixed   column value or object
	 * @return  $this
	 */
	public function and_where($operand1 = NULL, $operator = NULL, $operand2 = NULL)
	{
        if ($operand1 === NULL && $operator === NULL && $operand2 === NULL)
        {
            throw new Exception('Empty operands and operators supplied to :function',
                array(':function' => __FUNCTION__)
            );
        }

		$this->_where[] = array('AND' => array($operand1, $operator, $operand2));

		return $this;
	}

	/**
	 * Creates a new "OR WHERE" condition for the query.
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   string  logic operator
	 * @param   mixed   column value or object
	 * @return  $this
	 */
	public function or_where($operand1 = NULL, $operator = NULL, $operand2 = NULL)
	{
        if ($operand1 === NULL && $operator === NULL && $operand2 === NULL)
        {
            throw new Exception('Empty operands and operators supplied to :function',
                array(':functioin' => __FUNCTION__)
            );
        }

		$this->_where[] = array('OR' => array($operand1, $operator, $operand2));

		return $this;
	}

	/**
	 * Alias of and_where_open()
	 *
	 * @return  $this
	 */
	public function where_open()
	{
		return $this->and_where_open();
	}

	/**
	 * Opens a new "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_where_open()
	{
		$this->_where[] = array('AND' => '(');

		return $this;
	}

	/**
	 * Opens a new "OR WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_where_open()
	{
		$this->_where[] = array('OR' => '(');

		return $this;
	}

	/**
	 * Closes an open "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function where_close()
	{
		return $this->and_where_close();
	}

	/**
	 * Closes an open "AND WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function and_where_close()
	{
		$this->_where[] = array('AND' => ')');

		return $this;
	}

	/**
	 * Closes an open "OR WHERE (...)" grouping.
	 *
	 * @return  $this
	 */
	public function or_where_close()
	{
		$this->_where[] = array('OR' => ')');

		return $this;
	}

	/**
	 * Compiles an array of conditions into an SQL partial.
     * Used for WHERE and HAVING.
     *
	 * @param   object  Database instance
	 * @param   array   condition statements
	 * @return  string
	 */
	public function compile(Database $db)
	{
        if (isset($this->_compiled_sql))
            return $this->_compiled_sql;

		$last_condition = NULL;

		$sql = '';
		foreach ($this->_where as $group)
		{
			// Process groups of conditions
			foreach ($group as $logic => $condition)
			{
				if ($condition === '(')
				{
					if ( ! empty($sql) AND $last_condition !== '(')
					{
						// Include logic operator
						$sql .= ' '.$logic.' ';
					}

					$sql .= '(';
				}
				elseif ($condition === ')')
				{
					$sql .= ')';
				}
				else
				{
					if ( ! empty($sql) AND $last_condition !== '(')
					{
						// Add the logic operator
						$sql .= ' '.$logic.' ';
					}

					// Split the condition
					list($operand1, $operator, $operand2) = $condition;

                    if ($operand1 !== NULL)
                    {
                        if ($operand1 instanceof Database_Expression_Where)
                        {
                            $sql .= '(' . $operand1->compile($db) . ')';
                        }
                        else
                        {
                            // operand1 is a column name or Database_Query object or Database_Expression object
                            $sql .= $db->quote_identifier($operand1);
                        }
                    }

                    if ($operator !== NULL)
                    {
                        $sql .= ' ' . strtoupper($operator);
                    }

                    if ($operand2 !== NULL)
                    {
                        if ($operand2 instanceof Database_Expression_Where)
                        {
                            $sql .= ' (' . (string) $operand2 . ')';
                        }
                        else
                        {
                            // operand2 is a value or Database_Query object or Database_Expression object
                            $sql .= ' ' . $db->quote($operand2);
                        }
                    }
				}

				$last_condition = $condition;
			}
		}

        $this->_compiled_sql = $sql;
		return $sql;
	}

    /**
     * Does this expression actually have any conditions?
     * 
     * @return boolean
     */
    public function is_empty()
    {
        return ! count($this->_where);
    }

    public function  __toString()
    {
        return $this->compile(Database::instance());
    }

}