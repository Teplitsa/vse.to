<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Database connection wrapper.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Database extends Kohana_Database {

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
        //abstract
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
        //abstract
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
        //abstract
    }
} // End Database
