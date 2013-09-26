<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Extends Kohana_Database_Mysql functionality:
 *  - create_table, describe_table, update_table, drop_table support
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Database_MySQL extends Kohana_Database_MySQL {

	// Force using SET NAMES
	protected static $_set_names = TRUE;

    /**
     * List of tables in the database
     *
     * @var array
     */
    protected $_tables_cache;

    /**
     * Table columns info cache
     *
     * @var array
     */
    protected $_table_columns_cache;

    /**
     * Table indexes cache
     *
     * @var array
     */
    protected $_table_indexes_cache;


    /**
     * Returns a list of tables in database via 'SHOW TABLES FROM' query.
     *
     * @param string $use_cache     Allow to use cached results
     * @return array                List of tables
     */
	public function list_tables($use_cache = TRUE)
    {

        if ($this->_tables_cache === NULL || !$use_cache)
        {
            $this->_tables_cache = parent::list_tables();
        }

        return $this->_tables_cache;
    }

    /**
     * Returns information about database table,
     * including information about columns and indexes
     *
     * Results are obtained via 'SHOW COLUMNS FROM' and 'SHOW INDEXES FROM' sql commands.
     *
     * @param string Table name
     * @return array    Array of columns (including indexes)
     */
    public function describe_table($table_name)
    {
        // Table columns
        if (!isset($this->_table_columns_cache[$table_name]))
        {
            $result = $this->query(Database::SELECT, 'SHOW COLUMNS FROM ' . $this->quote_table($table_name), FALSE);
            if (is_object($result))
            {
                if ($result instanceof Database_Result)
                {
                    $result = $result->as_array();
                }
                else
                {
                    throw new Exception('Database query returned result of unkonwn type ":type"',
                        array(':type' => get_class($result)));
                }
            }

            $this->_table_columns_cache[$table_name] = array();

            foreach ($result as $column) {
                $this->_table_columns_cache[$table_name][$column['Field']] = $column;
            }
        }

        // Table indexes
        if (!isset($this->_table_indexes_cache[$table_name]))
        {
            $result = $this->query(Database::SELECT, 'SHOW INDEXES FROM ' . $this->quote_table($table_name), FALSE);
            if (is_object($result))
            {
                if ($result instanceof Database_Result)
                {
                    $result = $result->as_array();
                }
                else
                {
                    throw new Exception('Database query returned result of unkonwn type ":type"',
                        array(':type' => get_class($result)));
                }
            }

            $this->_table_indexes_cache[$table_name] = array();

            foreach ($result as $index) {
                // Support multi-column index: mysql returns a row for every column in index with same Key_name
                if (!isset($this->_table_indexes_cache[$table_name][$index['Key_name']]))
                {
                    $this->_table_indexes_cache[$table_name][$index['Key_name']] = array(
                        'Key_name'      => $index['Key_name'],
                        'Column_names'  => array($index['Column_name']),
                        'Non_unique'    => $index['Non_unique']
                    );
                }
                else
                {
                    // Append column to index
                    $this->_table_indexes_cache[$table_name][$index['Key_name']]['Column_names'][] = $index['Column_name'];
                }
            }
        }

		return array($this->_table_columns_cache[$table_name], $this->_table_indexes_cache[$table_name]);
    }

    /**
     * Creates table in database (if not already exists) with supplied columns and indexes.
     *
     * Columns is an array of column_name => column_properties
     * For avaliable column_properties keys @see _column_sql
     *
     * Indexes is an array of index_name => index_properties
     * For avaliable index_properties keys @see _index_sql
     *
     * @param string $table_name    Name of the table
     * @param array $columns        Columns for the table
     * @param array $indexes        Indexes for the table
     * @param array $options        Table options
     */
    public function create_table($table_name, array $columns, array $indexes, array $options, $if_not_exists = TRUE)
    {
        if (empty($columns))
        {
            throw new Exception('No columns specified for create_table()!');
        }

        $sql = '';

        // Add columns
        foreach ($columns as $column) {
            $sql .= ',' . $this->_column_sql($column);
        }

        // Add indexes
        foreach ($indexes as $index) {
            $sql .= ',' . $this->_index_sql($index);
        }

        $sql = trim($sql, ', ');

        if ( ! isset($options['ENGINE']))
        {
            // Default engine is MYISAM
            $options['ENGINE'] = 'MYISAM';
        }

        $opts = '';
        foreach ($options as $k => $v)
        {
            $opts .= " $k=$v";
        }

        $sql =  'CREATE TABLE ' . ($if_not_exists ? 'IF NOT EXISTS ' : '') . $this->quote_table((string) $table_name) . ' (' .
                    $sql .
                ')' . $opts;

        $this->query(NULL, $sql, FALSE);
    }


    /**
     * Alter table in database:
     * adds columns $columns_to_add,
     * drops columns $columns_to_drop,
     * adds indexes $indexes_to_add,
     * drops indexes $indexes_to_drop
     *
     * Format of columns and indexes array is the same as in @link create_table
     *
     * @param string $table_name
     * @param array $columns_to_add     Columns to add
     * @param array $columns_to_drop    Columns to drop
     * @param array $indexes_to_add     Indexes to add
     * @param array $indexes_to_drop    Indexes to drop
     */
    public function alter_table($table_name, array $columns_to_add, array $columns_to_drop, array $indexes_to_add, array $indexes_to_drop)
    {
        if (
            empty($columns_to_add) &&
            empty($columns_to_drop) &&
            empty($indexes_to_add) &&
            empty($indexes_to_drop)
        ) {
            // Nothing to do
            return;
        }

        $sql = '';

        // Add columns and indexes
        $add_sql = '';
        foreach ($columns_to_add as $column)
        {
            $add_sql .= ',' . $this->_column_sql($column);
        }
        foreach ($indexes_to_add as $index)
        {
            $add_sql .= ',' . $this->_index_sql($index);
        }
        $add_sql = trim($add_sql, ', ');

        if ($add_sql !== '')
        {
            $sql .= 'ADD COLUMN (' . $add_sql . ')';
        }


        // Drop columns and indexes
        $drop_sql = '';
        foreach ($columns_to_drop as $column)
        {
            if (!isset($column['Field']))
            {
                // Column name is required
                throw new Exception('"Field" not specified for column :column!', array(':column' => print_r($column, TRUE)));
            }
            $drop_sql .= ', DROP COLUMN ' . $this->quote_identifier($column['Field']);
        }

        foreach ($indexes_to_drop as $index)
        {
            if (!isset($index['Key_name']))
            {
                // Column name is required
                throw new Exception('"Key_name" not specified for index :index!', array(':index' => print_r($index, TRUE)));
            }
            $drop_sql .= ', DROP INDEX ' . $this->quote_identifier($index['Key_name']);
        }
        $drop_sql = trim($drop_sql, ', ');

        if ($drop_sql !== '')
        {
            if ($sql !== '') {
                $sql .= ', ';
            }

            $sql .= $drop_sql;
        }

        $sql = 'ALTER TABLE ' . $this->quote_table((string) $table_name) . ' ' . $sql;

        $this->query(NULL, $sql, FALSE);
    }


    /**
     * Builds an sql expression to create a column.
     *
     * column must have folowing keys:
     *  - 'Field':  name of the column
     *  - 'Type':   type of the column (any sql type like 'int(11)', 'varchar(255)')
     *
     * Also you can specify this keys:
     *  - 'Null':   Column can have NULL values ('YES' or 'NO')
     *  - 'Default': Default value for the column
     *  - 'Extra':  Additional attributes (like 'auto_increment')
     *
     *
     * @param array $column Column properties
     * @return string SQL expression for column. (Like: '`login` CHAR(15) NOT NULL DEFAULT 'nobody')
     */
    protected function _column_sql(array $column)
    {
        if (!isset($column['Field']))
        {
            // Column name is required
            throw new Exception('"Field" not specified for column :column!', array(':column' => print_r($column, TRUE)));
        }

        $name = $this->quote_identifier($column['Field']);

        if (!isset($column['Type']))
        {
            // Column type is required
            throw new Exception('Type not specified for column :name!',
                array(':name' => $name));
        }

        $type = strtoupper($column['Type']);

        if (isset($column['Null']) && strtoupper($column['Null']) === 'NO' ) {
            $null = 'NOT NULL';
        } else {
            $null = 'NULL';
        }

        if (isset($column['Default'])) {
            $default = 'DEFAULT ' . $this->quote($column['Default']);
        } else {
            $default = '';
        }

        if (isset($column['Extra'])) {
            $extra = $column['Extra'];
        } else {
            $extra = '';
        }

        $sql = "$name $type $null $default $extra";

        return $sql;
    }

    /**
     * Builds an sql expression to create an index.
     *
     * index must have folowing keys:
     *  - 'Key_name':   Name of the key (if 'PRIMARY' - it's a PRIMARY KEY)
     *  - 'Column_names':   Array of column names that index is created for
     *
     * Also you can specify this keys:
     *  - 'Non_unique': 0 for UNIQUE index, 1 for NON UNIQUE index
     *
     * @param array $index Index properties
     * @return string SQL expression for index. (Like: UNIQUE `some_index` (`col1`, `col2`))
     */
    protected function _index_sql(array $index)
    {
        if (!isset($index['Key_name']))
        {
            // Index name is required
            throw new Exception('"Key_name" not specified for index :index!', array(':index' => print_r($index, TRUE)));
        }

        $name = $this->quote_identifier($index['Key_name']);

        if (empty($index['Column_names']))
        {
            // Column_names are required
            throw new Exception('Column names not specified for index :name', array(':name' => $index['Key_name']));
        }

        $index_columns = '';
        foreach ($index['Column_names'] as $column_name)
        {
            $index_columns .= ',' . $this->quote_identifier($column_name);
        }
        $index_columns = trim($index_columns, ', ');

        if ($index['Key_name'] == 'PRIMARY')
        {
            // A primary key
            $sql = 'PRIMARY KEY (' . $index_columns . ')';
        }
        elseif (empty($index['Non_unique']))
        {
            // A unique index
            $sql = 'UNIQUE ' . $name . ' (' . $index_columns . ')';
        }
        else {
            // Index
            $sql = 'INDEX ' . $name . ' (' . $index_columns . ')';
        }

        return $sql;
    }

}
