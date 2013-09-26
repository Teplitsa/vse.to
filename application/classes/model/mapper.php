<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model data mapper.
 * Maps model to a table in database
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Model_Mapper extends DbTable
{

    /**
     * Mapper instances
     * @var array
     */
    protected static $_mappers;

    /**
     * Return an instance of mapper
     *
     * @param  string $class
     * @return Model_Mapper
     */
    public static function factory($class)
    {
        if ( ! isset(self::$_mappers[$class]))
        {
            self::$_mappers[$class] = new $class();
        }

        return self::$_mappers[$class];
    }


    /**
     * Whether to cache or not to cache the result of find_all_by() function
     * 
     * @var boolean
     */
    public $cache_find_all = FALSE;

    /**
     * Limit maximum number of cached items
     * @var integer
     */
    public $cache_limit = 100;

    /**
     * Cache
     * @var array
     */
    protected $_cache;

    /**
     * Offset for batch find
     * @var integer
     */
    protected $_batch_offset = 0;

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
            // Construct table name from class name
            $table_name = strtolower(get_class($this));

            if (substr($table_name, -strlen('_mapper')) === '_mapper') {
                $table_name = substr($table_name, 0, -strlen('_mapper'));
            }
            elseif (substr($table_name, -strlen('_mapper_db')) === '_mapper_db') {
                $table_name = substr($table_name, 0, -strlen('_mapper_db'));
            }

            if (substr($table_name, 0, strlen('model_')) === 'model_') {
                $table_name = substr($table_name, strlen('model_'));
            }

            $this->_table_name = $table_name;
        }

        return $this->_table_name;
    }

    /**
     * Prepare model values for saving:
     * Leave values only for known columns.
     * Also setup values for special columns:
     *  -position
     *  -created_at
     *
     *
     * @param  Model $model
     * @param  boolean $force_create
     * @param  array $values that will be prepared for saving in the database
     * @return array
     */
    public function before_save(Model $model, $force_create = FALSE, array $values = array())
    {
        foreach ($this->_columns as $name => $column)
        {
            if ($name == 'position' && ( ! isset($model->id) || $force_create))
            {
                $values['position'] = (int) $this->max_position($model) + 1;
                $model->position = $values['position'];
            }
            elseif ($name == 'created_at' && ( ! isset($model->id) || $force_create) && $model->created_at === NULL)
            {
                $values['created_at'] = time();
                $model->created_at = $values['created_at'];
            }
            elseif (isset($model->$name))
            {                
                $values[$name] = $model->$name;
            }
        }

        return $values;
    }

    /**
     * Save model to database.
     * Updates existing row or inserts a new one
     *
     * @param  Model $model
     * @param  boolean $force_create If true, even models with existing pk's will be inserted
     * @return integer Id of inserted/updated row
     */
    public function save(Model $model, $force_create = FALSE)
    {
        $pk = $this->get_pk(FALSE);

        // Prepare values to be saved
        $values = $this->before_save($model, $force_create);

        if ($pk !== NULL)
        {
            $id = $model->$pk;
        }
        else
        {
            $id = NULL;
        }

        if ($id === NULL || $force_create)
        {
            // Insert new row
            $id = $this->insert($values);

            if ($pk !== NULL)
            {
                $model->$pk = $id;
            }
        }
        else
        {
            // Update existing row
            $this->update($values, DB::where($pk, '=', $id));
        }

        return $id;
    }

    /***************************************************************************
     * Find
     **************************************************************************/
    /**
     * Find model by condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model|array
     */
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {   
        $result = $this->select_row($condition, $params, $query);

        // Return the result of the desired type
        if (empty($params['as_array']))
        {
            if ( ! empty($result))
            {
                $model->properties($result);
            }
            else
            {
                $model->init();
            }
            return $model;
        }

        return $result;
    }

    /**
     * Find model by primary key
     *
     * @param  Model $model
     * @param  mixed $pk_value
     * @param  array $params
     * @return Model
     */
    public function find(Model $model, $pk_value, array $params = NULL)
    {
        return $this->find_by($model, array($this->get_pk() => $pk_value), $params);
    }
    
    public function find_another_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {    
        $pk = $this->get_pk();
        if ($model->$pk !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $condition->and_where($pk, '!=', $model->$pk);
        }
        return $this->find_by($model, $condition, $params, $query);
    }
    /**
     * Find all models by criteria and return them in {@link Models} container
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        if ($params === NULL)
        {
            $params = $model->params_for_find_all();
        }

        if ( ! empty($params['batch']))
        {
            // Batch find
            $params['offset'] = $this->_batch_offset;
            $params['limit']  = $params['batch'];
        }
        
        if ( ! isset($params['key']))
        {
            $params['key'] = $this->get_pk();
        }

        // ----- cache
        if ($this->cache_find_all)
        {
            $condition = $this->_prepare_condition($condition);
            $hash = $this->params_hash($params, $condition);

            if (isset($this->_cache[$hash]))
            {
                // Cache hit!
                return $this->_cache[$hash];
            }
        }
        
        $result = $this->select($condition, $params, $query);

        if ( ! empty($params['batch']))
        {
            // Batch find
            if ( ! empty($result))
            {
                $this->_batch_offset += count($result);
            }
            else
            {
                // Reset
                $this->_batch_offset = 0;
            }
        }
        
        // Return the result of the desired type
        if (empty($params['as_array']))
        {
            $key = isset($params['key']) ? $params['key'] : FALSE;

            $result = new Models(get_class($model), $result, $key);
        }

        // ----- cache
        if ($this->cache_find_all)
        {
            if (count($this->_cache) < $this->cache_limit)
            {
                $this->_cache[$hash] = $result;
            }
        }

        return $result;
    }

    /**
     * Find all models
     * 
     * @param  Model $model
     * @param  array $params
     * @return Models
     */
    public function find_all(Model $model, array $params = NULL)
    {
        return $this->find_all_by($model, NULL, $params);
    }
    
    public function find_another_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {    
        $pk = $this->get_pk();
        if ($model->$pk !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $condition->and_where($pk, '!=', $model->$pk);
        }
        
        return $this->find_all_by($model, $condition, $params, $query);
    }
    /***************************************************************************
     * Count & exists
     **************************************************************************/
    /**
     * Count all models by given condition
     * 
     * @param Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @return integer
     */
    public function count_by(Model $model, $condition = NULL)
    {
        return $this->count_rows($condition);
    }

    /**
     * Get the offset of model row in DB assuming the sorting, specified by $params
     *
     * @param Model $model
     * @param string|array|Database_Expression_Where $condition
     * @param array $params
     * @return integer
     */
    public function offset_by(Model $model, $condition = NULL, array $params = NULL)
    {
        $condition = $this->_prepare_condition($condition);

        if ( ! isset($params['order_by']))
            throw new Kohana_Exception ('Order by must be specified for offset_by method!');

        $order_by = $params['order_by'];
        if (empty($params['desc']))
        {
            $condition->and_where($order_by, '<', $this->_sqlize_value($model->$order_by, $order_by));
        }
        else
        {
            $condition->and_where($order_by, '>', $this->_sqlize_value($model->$order_by, $order_by));
        }

        return $this->count_rows($condition, $params);
    }

    /**
     * Count all models
     *
     * @param Model $model
     * @return integer
     */
    public function count(Model $model)
    {
        return $this->count_by($model, NULL);
    }

    /**
     * Check if there is a model with specified condition
     *
     * @param  Model $model
     * @param  sting|array|Database_Expression_Where $condition
     * @return boolean
     */
    public function exists_by(Model $model, $condition)
    {
        return $this->exists($condition);
    }

    /**
     * Check if there is another model with given condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @return boolean
     */
    public function exists_another_by(Model $model, $condition)
    {        
        $pk = $this->get_pk();
        if ($model->$pk !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $condition->and_where($pk, '!=', $model->$pk);
        }
        return $this->exists($condition);
    }


    /***************************************************************************
     * Delete
     **************************************************************************/
    /**
     * Delete all models by criteria
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @return Models
     */
    public function delete_all_by(Model $model, $condition = NULL)
    {
        $this->delete_rows($condition);
    }
    
    /**
     * Delete model by primary key
     *
     * @param  Model $model
     * @return Model
     */
    public function delete(Model $model)
    {
        $pk = $this->get_pk();
        $this->delete_all_by($model, DB::where($pk, '=', $model->$pk));
        $model->init();
    }

    /**
     * Magic method - automatically resolves the following methods:
     *  - find_by_*
     *  - find_all_by_*
     *  - exists_another_by_*
     *  - delete_all_by_*
     *  - count_by_*
     *
     * @param  string $name
     * @param  array $arguments
     * @return mixed
     */
    public function  __call($name, array $arguments)
    {
        // Model is always a first argument to mapper method
        $model = array_shift($arguments);

        // find_by_* methods
        if (strpos($name, 'find_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('find_by_')), $arguments, $name);
            $params = array_shift($arguments);

            return $this->find_by($model, $condition, $params);
        }

        // find_another_by_* methods
        if (strpos($name, 'find_another_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('find_another_by_')), $arguments, $name);
            $params = array_shift($arguments);

            return $this->find_another_by($model, $condition, $params);
        }
        
        // find_all_by_* methods
        if (strpos($name, 'find_all_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('find_all_by_')), $arguments, $name);
            $params = array_shift($arguments);

            return $this->find_all_by($model, $condition, $params);
        }
        
        // find_another_all_by_* methods
        if (strpos($name, 'find_another_all_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('find_another_all_by_')), $arguments, $name);
            $params = array_shift($arguments);

            return $this->find_another_all_by($model, $condition, $params);
        }        

        // exists_by_* methods
        if (strpos($name, 'exists_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('exists_by_')), $arguments, $name);

            return $this->exists_by($model, $condition);
        }

        // exists_another_by_* methods
        if (strpos($name, 'exists_another_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('exists_another_by_')), $arguments, $name);

            return $this->exists_another_by($model, $condition);
        }

        // delete_all_by_* methods
        if (strpos($name, 'delete_all_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('delete_all_by_')), $arguments, $name);

            return $this->delete_all_by($model, $condition);
        }

        // count_by_* methods
        if (strpos($name, 'count_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('count_by_')), $arguments, $name);

            return $this->count_by($model, $condition);
        }

        throw new Kohana_Exception('Unknown method :method', array(':method' => get_class($this) . '::' . $name));
    }

    /**
     * Convert column names string to array criteria, that can be used as a criteria
     * for find_all_by and find_by methods
     * [!] arguments array is modified: columns are shifted off
     *
     * @param  string $columns_string
     * @param  array $arguments       <-- Passed by REFERENCE and is modified
     * @param  string $method_name
     * @return array
     */ 
    protected function _cols_to_condition_array($columns_string, array & $arguments, $method_name)
    {
        $columns = explode('_and_', $columns_string);

        if (count($columns) > count($arguments))
        {
            throw new Kohana_Exception('Not enough arguments for :method. Number of columns = :cols, while number of arguments = :args',
                    array(':method' => get_class($this) . '::' . $method_name, ':cols' => count($columns), ':args' => count($arguments)));
        }

        $condition = array();
        foreach ($columns as $column)
        {
            $condition[$column] = array_shift($arguments);
        }

        return $condition;
    }

    /**
     * Move model one position up
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function up(Model $model, Database_Expression_Where $condition = NULL)
    {
        if ( ! isset($this->_columns['position']))
        {
            throw new Kohana_Exception('Unable to move :model up: model has no "position" column!',
                array(':model' => get_class($this))
            );
        }

        $pk = $this->get_pk();

        $db = $this->get_db();

        // Escape everything
        $id = $db->quote($model->$pk);
        $pk = $db->quote_identifier($pk);
        
        $pos = $db->quote_identifier('position');
        $table = $db->quote_table($this->table_name());

        if ($condition !== NULL) {
            $condition = " AND " . (string) $condition;
        } else {
            $condition = '';
        }

        $this->lock();
        $db->query(NULL, "SELECT @pos:=NULL, @next_pos:=null, @next_id:=null", FALSE);
        $db->query(NULL, "SELECT @pos:=$pos FROM $table WHERE ($pk=$id)", FALSE);
        $db->query(NULL, "SELECT @next_id:=$pk,@next_pos:=$pos FROM $table WHERE ($pos>@pos) $condition ORDER BY $pos ASC LIMIT 1", FALSE);
        $db->query(NULL, "UPDATE $table SET $pos=@next_pos WHERE (@next_pos IS NOT NULL) AND ($pk=$id)", FALSE);
        $db->query(NULL, "UPDATE $table SET $pos=@pos WHERE (@next_id IS NOT NULL) AND ($pk=@next_id)", FALSE);
        $this->unlock();
    }

    /**
     * Move model one position down
     *
     * @param Model $model
     * @param Database_Expression_Where $condition
     */
    public function down(Model $model, Database_Expression_Where $condition = NULL)
    {
        if ( ! isset($this->_columns['position']))
        {
            throw new Kohana_Exception('Unable to move :model up: model has no "position" column!',
                array(':model' => get_class($this))
            );
        }

        $pk = $this->get_pk();

        $db = $this->get_db();

        // Escape everything
        $id = $db->quote($model->$pk);
        $pk = $db->quote_identifier($pk);
        
        $pos = $db->quote_identifier('position');
        $table = $db->quote_table($this->table_name());

        if ($condition !== NULL) {
            $condition = " AND " . (string) $condition;
        } else {
            $condition = '';
        }

        $this->lock();
        $db->query(NULL, "SELECT @pos:=NULL, @next_pos:=null, @next_id:=null", FALSE);
        $db->query(NULL, "SELECT @pos:=$pos FROM $table WHERE ($pk=$id)", FALSE);
        $db->query(NULL, "SELECT @next_id:=$pk,@next_pos:=$pos FROM $table WHERE ($pos<@pos) $condition ORDER BY $pos DESC LIMIT 1", FALSE);
        $db->query(NULL, "UPDATE $table SET $pos=@next_pos WHERE (@next_pos IS NOT NULL) AND ($pk=$id)", FALSE);
        $db->query(NULL, "UPDATE $table SET $pos=@pos WHERE (@next_id IS NOT NULL) AND ($pk=@next_id)", FALSE);
        $this->unlock();
    }

    /**
     * Return maximum value of field $field
     * @param string $field
     */
    public function max($field)
    {
        if ( ! isset($this->_columns[$field]))
        {
            throw new Exception('Unable to obtain maximum value for column :field - column not found in :model!',
                array(':field' => $field, ':model' => get_class($this))
            );
        }

        return DB::select(array('MAX("' . $field . '")', 'maximum'))
            ->from($this->table_name())
            ->execute($this->get_db())
            ->get('maximum');
    }

    /**
     * Get maximum value of "position"
     *
     * @param Model $model  Model is passed to be able to select conditional maximum
     * @return integer
     */
    public function max_position(Model $model = NULL)
    {
        return (int) $this->max('position');
    }
}
