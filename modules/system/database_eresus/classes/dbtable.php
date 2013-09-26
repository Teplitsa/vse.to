<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Represents a table in database
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class DbTable {

    /**
     * Cache for parsed column types
     * @var array
     */
    protected static $_types_info_cache = array();

    /**
     * Configuration
     * @var array
     */
    protected $_config;

	/**
     * Database instance
     * @var Database
     */
	protected $_db = 'default';

    /**
     * Name of database table
     * @var string
     */
    protected $_table_name;

    /**
     * Columns
     * @var array
     */
    protected $_columns = array();

    /**
     * Virtual columns (custom columns, that are not the columns of the table in db, but can appear when using joins or expression)
     * @var array
     */
    protected $_virtual_columns = array();

    /**
     * Manually specified indexes
     * @var array
     */
    protected $_indexes = array();

    /**
     * Table options
     * @var array
     */
    protected $_options = array();

    /**
     * Columns metadata (generated from _columns)
     * @var array
     */
    protected $_columns_metadata;

    /**
     * Indexes metadata (generated from _columns and _indexes)
     * @var array
     */
    protected $_indexes_metadata;

    /**
     * Flag is used to prevent duplicate table creation tries when
     * mapper is instantinated more than onse
     * @var array
     */
    protected static $_tables_created = array();

    /**
     * Flag is used to prevent duplicate table updating tries when
     * mapper is instantinated more than onse
     * @var array
     */
    protected static $_tables_updated = array();

    /**
     * Name of primary key
     * @var string
     */
    protected $_pk;

    /**
     * DbTable instances
     * @var array
     */
    protected static $_dbtables;

    /**
     * Return an instance of dbtable
     *
     * @param  string $class
     * @return DbTable
     */
    public static function instance($class)
    {
        $class = strtolower($class);
        
        if (strpos($class, 'dbtable_') !== 0)
        {
            $class = 'dbtable_' . $class;
        }
        
        if ( ! isset(self::$_dbtables[$class]))
        {
            self::$_dbtables[$class] = new $class();
        }

        return self::$_dbtables[$class];
    }


	/**
	 * Loads the database. Sets table name.
	 *
	 * @param   mixed  Database instance object or string
	 * @return  void
	 */
	public function __construct($db = NULL, $table_name = NULL, array $config = NULL)
	{
        // Load configuration
        if ($config === NULL)
        {
            $this->_config = Kohana::config('dbtable');
        }
        else
        {
            $this->_config = $config;
        }

		if ($db !== NULL)
		{
			// Set the database instance name
			$this->set_db($db);
		}

        if ($table_name !== NULL)
        {
            // Set up database table name
            $this->table_name($table_name);
        }

        // Initialize DbTable, add neccessary columns
        $this->init();

        // Auto create/update table columns
        $this->sync_with_db();
	}

    /**
     * Sets database to use by mapper
     *
     * @param Database | string $db Database object or database instance name
     * @return Model_Mapper
     */
    public function set_db($db)
    {
        $this->_db = $db;
        return $this;
    }

    /**
     * Gets database instance
     *
     * @return Database
     */
    public function get_db()
    {
        if (is_object($this->_db))
        {
            return $this->_db;
        }
		elseif (is_string($this->_db))
		{
			// Load the database by instance name
			$this->_db = Database::instance($this->_db);
            return $this->_db;
		}
        elseif ($this->_db === NULL)
        {
            throw new Exception('Database instance not specified!');
        }
        else
        {
            throw new Exception('Invalid database instance type ":type"', array(':type' => gettype($this->_db)));
        }
    }

    /**
     * Sets/gets db table name
     *
     * @param  string $table_name
     * @return string
     */
    public function table_name($table_name = NULL)
    {
        if ($table_name !== NULL)
        {
            $this->_table_name = $table_name;
        }

        if ($this->_table_name === NULL)
        {
            // Try to construct table name from class name
            $table_name = strtolower(get_class($this));

            if (substr($table_name, 0, strlen('dbtable_')) === 'dbtable_') {
                $table_name = substr($table_name, strlen('dbtable_'));
            }

            $this->_table_name = $table_name;
        }

        return $this->_table_name;
    }

    /**
     * Initialize dbtable
     */
    public function init()
    {}

    /**
     * Add column to DbTable
     *
     * @param string $name      Column name
     * @param array  $column    Column properties
     * @return DbTable
     */
    public function add_column($name, array $column)
    {
        $this->_columns[$name] = $column;
        return $this;
    }

    /**
     * Add virtual column to DbTable
     *
     * @param string $name      Column name
     * @param array  $column    Column properties
     * @return DbTable
     */
    public function add_virtual_column($name, array $column)
    {
        $this->_virtual_columns[$name] = $column;
        return $this;
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function has_column($name)
    {
        return isset($this->_columns[$name]);
    }

    /**
     * @param  string $name
     * @return boolean
     */
    public function has_virtual_column($name)
    {
        return isset($this->_virtual_columns[$name]);
    }

    /**
     * Get column info
     * 
     * @param  string $name
     * @return array
     */
    public function get_column($name)
    {
        if (isset($this->_columns[$name]))
        {
            return $this->_columns[$name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get virtual column info
     *
     * @param  string $name
     * @return array
     */
    public function get_virtual_column($name)
    {
        if (isset($this->_virtual_columns[$name]))
        {
            return $this->_virtual_columns[$name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Add table option (such as ENGINE, AUTO_INCREMENT, ...)
     * 
     * @param  string $name
     * @param  string $value
     * @return DbTable
     */
    public function add_option($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }

    /**
     * Automatically create table, add or remove columns
     */
    public function sync_with_db()
    {
        $table_name = $this->table_name();

        if (!empty($this->_config['auto_create']) && ! in_array($table_name, self::$_tables_created))
        {
            if ( ! in_array($this->get_db()->table_prefix() . $table_name, $this->get_db()->list_tables()))
            {
                // Create database table automatically, if not already exists
                $this->create_table();

                self::$_tables_created[] = $table_name;
            }
        }

        if (!empty($this->_config['auto_update']) && ! in_array($table_name, self::$_tables_updated))
        {
            // Automatically update table columns when mapper columns change
            $this->update_table();

            self::$_tables_updated[] = $table_name;
        }
    }

    /**
     * Create table schema in database
     */
    public function create_table()
    {
        list($columns, $indexes, $options) = $this->get_metadata();
        $this->get_db()->create_table($this->table_name(), $columns, $indexes, $options);
    }

    /**
     * Update table schema in database
     */
    public function update_table()
    {
        list($db_columns, $db_indexes) = $this->get_db()->describe_table($this->table_name());

        list($columns, $indexes, $options) = $this->get_metadata();

        $columns_to_drop = array_diff_key($db_columns, $columns);
        $columns_to_add = array_diff_key($columns, $db_columns);

        $indexes_to_drop = array_diff_key($db_indexes, $indexes);
        $indexes_to_add = array_diff_key($indexes, $db_indexes);

        $this->get_db()->alter_table(
            $this->table_name(),
            $columns_to_add, $columns_to_drop,
            $indexes_to_add, $indexes_to_drop
        );
    }

    /**
     * Truncate table
     */
    public function truncate_table()
    {
        $table = $this->get_db()->quote_table($this->table_name());
        $this->get_db()->query(Database::DELETE, "TRUNCATE $table", FALSE);
    }

    /**
     * Parses _columns and _indexes into mysql-compatible format
     *
     * @return array Columns metadata, Indexes metadata
     */
    public function get_metadata()
    {
        if ($this->_columns_metadata === NULL || $this->_indexes_metadata === NULL)
        {
            $this->_columns_metadata = array();
            $this->_indexes_metadata = array();

            // Parsing columns
            foreach ($this->_columns as $name => $column)
            {
                $key = isset($column['Key']) ? $column['Key'] : NULL;

                $column['Field'] = $name;
                unset($column['key']);

                $column['Type'] = $this->_sqlize_column_type($column['Type']);

                // Add column to columns metadata
                $this->_columns_metadata[$name] = $column;

                // Add corresponding index
                if (isset($key))
                {
                    switch (strtolower($key))
                    {
                        case 'pri':
                        case 'primary':
                            // Add primary key
                            $this->_indexes_metadata['PRIMARY'] = array(
                                'Key_name'     => 'PRIMARY',
                                'Column_names' => array($name),
                                'Non_unique'   => 0
                            );
                            $this->_pk = $name;
                            break;

                        case 'uni':
                        case 'unique':
                            // Add unique index
                            $this->_indexes_metadata[$name] = array(
                                'Key_name'     => $name,
                                'Column_names' => array($name),
                                'Non_unique'   => 0
                            );
                            break;

                        case 'key':
                        case 'ind':
                        case 'mul':
                        case 'index':
                            // Add index
                            $this->_indexes_metadata[$name] = array(
                                'Key_name'     => $name,
                                'Column_names' => array($name),
                                'Non_unique'   => 1
                            );
                            break;

                        default:
                            throw new Exception('Unknown index type ":key" for column ":name"',
                                array(':key' => $key, ':name' => $name)
                            );
                    }
                }
            } // end of parsing columns

            // Parsing indexes
            foreach ($this->_indexes as $name => $index)
            {
                $index['Key_name'] = $name;

                if (!isset($index['Non_unique']))
                {
                    // Make index non unique by default
                    $index['Non_unique'] = 1;
                }

                $this->_indexes_metadata[$name] = $index;
            }
        }

        return array($this->_columns_metadata, $this->_indexes_metadata, $this->_options);
    }

    /**
     * Gets name of primary key.
     * If $required and there is no pimary key then an exception is thrown.
     *
     * @param  boolean $required
     * @return string
     */
    public function get_pk($required = TRUE)
    {
        if ($this->_pk === NULL)
        {
            foreach ($this->_columns as $name => $column)
            {
                if (isset($column['Key']) && in_array(strtolower($column['Key']), array('pri', 'primary')))
                {
                    $this->_pk = $name;
                }
            }
        }

        if ($this->_pk === NULL && $required)
        {
            throw new Kohana_Exception('Unable to find primary key column for table :table',
                array(':table' => $this->table_name())
            );
        }

        return $this->_pk;
    }

    /**
     * Insert new row in database table
     *
     * @param  array   $values  Values to insert
     * @return integer          Id of inserted row
     */
    public function insert(array $values)
    {
        // Leave values only for known columns
        foreach ($values as $name => $value)
        {
            if ( ! isset($this->_columns[$name]))
            {
                unset($values[$name]);
            }
        }

        // Make values ready to be stored in database
        $values = $this->_sqlize($values);

        $query = DB::insert($this->table_name())
            ->columns(array_keys($values))
            ->values(array_values($values));

        list($id) = $query->execute($this->get_db());

        return $id;
    }

    /**
     * Updates existing row
     *
     * @param array $values
     * @param string|array|Database_Expression_Where $condition
     */
    public function update(array $values, $condition = NULL)
    {
        $condition = $this->_prepare_condition($condition);
        
        // Leave values only for known columns
        foreach ($values as $name => $value)
        {
            if ( ! isset($this->_columns[$name]))
            {
                unset($values[$name]);
            }
        }

        // Make values ready to be stored in database
        $values = $this->_sqlize($values);

        $query = DB::update($this->table_name())
            ->set($values);
        
        if ($condition !== NULL)
        {
            $query->where($condition, NULL, NULL);
        }

        $query
            ->execute($this->get_db());
    }

    /**
     * Fetchs multiple rows from db
     *
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params Additional params
     * @param  Database_Query_Builder_Select $query Custom query to execute
     * @return array
     */
    public function select($condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {

        if ($query === NULL)
        {
            $columns = $this->_prepare_columns($params);
            $query = new Database_Query_Builder_Select($columns);
            $query->from($this->table_name());
        }

        $condition = $this->_prepare_condition($condition);

        if ($condition !== NULL)
        {
            $query->where($condition, NULL, NULL);
        }

        // Add limit, offset and order by statements
        if (isset($params['known_columns']))
        {
            $known_columns = $params['known_columns'];
        }
        else
        {
            $known_columns = array();
        }
        
        $this->_std_select_params($query, $params, $known_columns);
        // Execute the query
        $result = $query->execute($this->get_db());

        $data = array();
        if (count($result))
        {
            $key = isset($params['key']) ? $params['key'] : FALSE;

            // Convert to correct types
            foreach ($result as $values)
            {
                $values = $this->_unsqlize($values);
                
                if ($key && isset($values[$key]))
                {
                    $data[$values[$key]] = $values;
                }
                else
                {
                    $data[] = $values;
                }
            }
        }

        return $data;
    }

    /**
     * Fetch only one row from database
     *
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params Additional params
     * @param  Database_Query_Builder_Select $query Custom query to execute
     * @return array
     */
    public function select_row($condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        // Limit to one row
        $params['limit'] = 1;

        $result = $this->select($condition, $params, $query);
        if (count($result))
        {
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Delete rows from table by condition
     *
     * @param string|array|Database_Expression_Where $condition
     */
    public function delete_rows($condition = NULL)
    {
        $query = DB::delete($this->table_name());

        if ($condition !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $query->where($condition, NULL, NULL);
        }

        $query->execute($this->get_db());
    }

    /**
     *  Counts rows in table that satisfy given condition
     *
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @return integer
     */
    public function count_rows($condition = NULL, array $params = NULL)
    {
        $query = DB::select(array('COUNT("*")', 'total_count'))
            ->from($this->table_name());

        if ($condition !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $query->where($condition, NULL, NULL);
        }

        $this->_std_select_params($query, $params);

        $count = $query->execute($this->get_db())
            ->get('total_count');

        return (int) $count;
    }

    /**
     * Check that rows with given conditions exists in table
     *
     * @param  string|array|Database_Expression_Where $condition
     * @return boolean
     */
    public function exists($condition = NULL)
    {
        $query = DB::select()
            ->from($this->table_name());

        if ($condition !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $query->where($condition, NULL, NULL);
        }

        $result = DB::select(array(DB::expr('EXISTS (' . $query->compile($this->get_db()) . ')'), 'row_exists'))
            ->execute($this->get_db())
            ->get('row_exists');

        return (bool) $result;
    }

    /**
     * Lock table for writing
     */
    public function lock()
    {
        $this->get_db()->query(NULL, "LOCK TABLE " . $this->get_db()->quote_table($this->table_name()) . " WRITE", FALSE);
    }

    /**
     * Unlock tables
     */
    public function unlock()
    {
        $this->get_db()->query(NULL, "UNLOCK TABLES", FALSE);
    }
    
    /**
     * Return database column type that will be used for specified mapper column type
     *
     * @param string $type  Mapper column type
     * @return string
     */
    protected function _sqlize_column_type($type)
    {
        $info = $this->_parse_column_type($type);
        switch ($info['type'])
        {
            case 'boolean':
                return 'tinyint(1)';

            case 'array':
                return 'text';

            case 'money':
                return 'bigint';

            case 'unix_timestamp':
                return 'int unsigned';

            default:
                return $type;
        }
    }

    /**
     * Parses given mapper column type and returns information in array
     *
     * @param string $type
     * @return array
     */
    protected function _parse_column_type($type)
    {
        if ( ! isset(self::$_types_info_cache[$type]))
        {
            if ( ! preg_match('/^(?P<type>\w+)(\((?P<size>\d+)\))?(?P<attributes>(\s+(\w+))*)$/', $type, $matches))
            {
                throw new Exception('Unable to parse column type ":type"',
                    array(':type' => $type)
                );
            }

            $info = array();

            foreach ($matches as $key => $value)
            {
                if (is_int($key)) {
                    // Skip all numeric keys
                    continue;
                }

                $info[$key] = $value;
            }

            $info['type'] = strtolower($info['type']);

            if (isset($info['attributes']))
            {
                $info['attributes'] = preg_split('/\s+/', $info['attributes'], NULL, PREG_SPLIT_NO_EMPTY);
            }

            self::$_types_info_cache[$type] = $info;
        }

        return self::$_types_info_cache[$type];
    }

    /**
     * Prepare values for storing in database
     *
     * @param array $values
     * @return array
     */
    protected function _sqlize($values)
    {
        foreach (array_keys($this->_columns) as $name)
        {
            if (isset($values[$name]))
            {
                $values[$name] = $this->_sqlize_value($values[$name], $name);
            }
        }

        return $values;
    }

    /**
     * Convert model values to the actual types, specified in _columns.
     *
     * @param array $values
     * @return array
     */
    protected function _unsqlize($values)
    {
        foreach ((array_merge(array_keys($this->_columns), array_keys($this->_virtual_columns))) as $name)
        {
            if (isset($values[$name]))
            {
                $values[$name] = $this->_unsqlize_value($values[$name], $name);
            }
        }

        return $values;
    }

    /**
     * Converts value to type of the specified column.
     *
     * @param mixed $value
     * @param string $column_name    Column name
     * @return mixed
     */
    protected function _unsqlize_value($value, $column_name)
    {
        if ( ! isset($this->_columns[$column_name]) && ! isset($this->_virtual_columns[$column_name]))
        {
            throw new Exception('Unknown column specified :column_name',
                array(':column_name' => $column_name)
            );
        }

        if (isset($this->_columns[$column_name]))
        {
            $column = $this->_columns[$column_name];
        }
        else
        {
            $column = $this->_virtual_columns[$column_name];
        }

        $type_info = $this->_parse_column_type($column['Type']);

        switch ($type_info['type'])
        {
            case 'boolean':
                return (bool) $value;

            case 'bigint': case 'int': case 'smallint': case 'tinyint':
                return (int) $value;

            case 'float':
                return (float) $value;

            case 'array':
                if ($value === NULL || $value === '') {
                    return array();
                } else {
                    return unserialize($value);
                }

           case 'money':
               $money = new Money();
               $money->set_raw_amount($value);
               return $money;

           case 'datetime': case 'date':
               // Datetime values are stored in UTC+0 timezone in database
               $value = new DateTime($value, new DateTimeZone('UTC'));
               // Convert to application timezone
               $value->setTimezone(new DateTimeZone(date_default_timezone_get()));
            default:
                return $value;
        }
    }

    /**
     * Prepare value of specific column to be stored in database
     *
     * @param mixed $value
     * @param string $column_name    Column name
     * @return string
     */
    protected function _sqlize_value($value, $column_name)
    {
        if ( ! isset($this->_columns[$column_name]) && ! isset($this->_virtual_columns[$column_name]))
        {
            throw new Kohana_Exception('Unknown column specified :column_name',
                array(':column_name' => $column_name)
            );
        }

        if ($value instanceof Database_Expression)
        {
            // Do not touch database expressions
            return $value;
        }

        if (isset($this->_columns[$column_name]))
        {
            $column = $this->_columns[$column_name];
        }
        else
        {
            $column = $this->_virtual_columns[$column_name];
        }

        $type_info = $this->_parse_column_type($column['Type']);

        switch ($type_info['type'])
        {
            case 'boolean':
            case 'bigint': case 'int': case 'smallint': case 'tinyint':
                $value = (int) $value;
                break;

            case 'float': case 'double':
                // Force '.' to be a decimal separator
                $value = str_replace(',', '.', (string) $value);
                break;

            case 'array':
                if (is_array($value)) {
                    foreach ($value as $value_key => $value_val) {
                        if ($value_val == '')
                            unset($value[$value_key]);
                    }
                }
                $value = serialize($value);
                break;

            case 'money':
                if ($value instanceof Money)
                {
                    $value = $value->raw_amount;
                }
                break;

            case 'unix_timestamp':
                if ($value === '')
                {
                    // Allow not-specified dates
                    $value = NULL;
                }
                else
                {
                    $value = (int) $value;
                }
                break;

            case 'datetime':
                if ($value instanceof DateTime || $value instanceof Date)
                {
                    // Convert to UTC+0 timezone
                    $value->setTimezone(new DateTimeZone('UTC'));
                    $value = $value->format(Kohana::config('datetime.db_datetime_format'));
                    
                }
                else
                {
                    $value = (string) $value;
                }
                break;
                
            case 'date':
                if ($value instanceof DateTime || $value instanceof Date)
                {
                    // Convert to UTC+0 timezone
                    $value->setTimezone(new DateTimeZone('UTC'));
                    $value = $value->format(Kohana::config('datetime.db_date_format'));
                }
                else
                {
                    $value = (string) $value;
                }
                break;

            default:
                $value = (string) $value;
        }

        return $value;
    }

    /**
     * Apply standart select parameters (such offset, limit, order_by, etc..) to query
     *
     * @param $query
     * @param array $params
     */
    protected function  _std_select_params(Database_Query_Builder_Select $query, array $params = NULL, $known_columns = array())
    {
        if (isset($params['order_by']))
        {
            if ( ! is_array($params['order_by']))
            {
                $order_by = array($params['order_by'] => ! empty($params['desc']));
            }
            else
            {
                // Several order by expressions
                $order_by = $params['order_by'];
            }
            //@TODO: allow order by on unknown columns

            foreach ($order_by as $column => $desc)
            {
                if (isset($this->_columns[$column]) || isset($this->_virtual_columns[$column]) || in_array($column, $known_columns))
                {
                    $query->order_by($column, $desc ? 'DESC' : 'ASC');
                }
            }
        }

        if ( ! empty($params['offset']))
        {
            $query->offset((int) $params['offset']);
            if (empty($params['limit']))
            {
                $query->limit(65535);
            }
        }

        if ( ! empty($params['limit']))
        {
            $query->limit((int) $params['limit']);
        }

        return $query;
    }

    /**
     * Prepare columns for SELECT query from params
     * 
     * @param  array $params
     * @return array
     */
    protected function _prepare_columns(array $params = NULL)
    {
        $table = $this->table_name();
        if (isset($params['columns']))
        {
            // Explicitly specify table name for columns
            // to avoid "column is ambiguous" issue
            $columns = array();
            foreach ($params['columns'] as $column)
            {
                if (strpos($column, '.') === FALSE)
                {
                    $columns[] = "$table.$column";
                }
                else
                {
                    $columns[] = $column;
                }
            }
        }
        else
        {
            $columns = array("$table.*");
        }
        return $columns;
    }

    /**
     * Convert specified condition (string, array, Database_Expression_Where)
     * to the Database_Expression_Where object
     *
     * @param  string|array|Database_Expression_Where $condition
     * @return Database_Expression_Where
     */
    protected function _prepare_condition($condition)
    {
        if (is_string($condition))
        {
            throw new Kohana_Exception('String conditions are not yet implemented');
        }
        elseif (is_array($condition))
        {
            $condition = $this->_array_to_condition($condition);
        }
        return $condition;
    }

    /**
     * Convert an array of column names and values (array(column=>value)) to criteria
     * using AND logic
     *
     * @param  array $condition
     * @return Database_Expression_Where
     */
    protected function _array_to_condition(array $condition)
    {
        if (empty($condition))
            return NULL;
        
        $table = $this->table_name();
        $where = DB::where();
        foreach ($condition as $column => $value)
        {
            if (is_array($value))
            {
                $op    = $value[0];
                $value = $value[1];
            }
            else
            {
                $op = '=';
            }

            if (strpos($column, '.') === FALSE && $this->has_column($column))
            {
                $column = "$table.$column";
            }
            $where->and_where($column, $op, $value);
        }

        return $where;
    }

    /**
     * Returns hash for select parameters (useful when caching results of a select)
     *
     * @param array $params
     * @param Database_Expression_Where $condition
     * @return string
     */
    public function params_hash(array $params = NULL, Database_Expression_Where $condition = NULL)
    {
        if (empty($params['desc']))
        {
            $params['desc'] = FALSE;
        }

        if (empty($params['offset']))
        {
            $params['offset'] = 0;
        }

        if (empty($params['limit']))
        {
            $params['limit'] = 0;
        }

        if ($condition !== NULL)
        {
            $params['condition'] = (string) $condition;
        }

        $str = '';
        foreach ($params as $k => $v)
        {
            
            if (is_array($v)) {
                foreach ($v as $vs) {
                    $str .= $k . $vs;    
                }
            } else {
                $str .= $k . $v;
            }
        }

        return substr(md5($str), 0, 16);
    }

}