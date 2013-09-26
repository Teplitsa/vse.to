<?php defined('SYSPATH') or die('No direct script access.');

class Model_Message_Mapper extends Model_Mapper
{
    public function init()
    {
        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('dialog_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('created_at', array('Type' => 'datetime'));
        $this->add_column('message',   array('Type' => 'text'));
        $this->add_column('user_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));        
        $this->add_column('active',     array('Type' => 'boolean'));        
    }

    /*public function find_all_dialogs(Model $model, array $params = NULL)
    {
        return $this->find_all_dialogs_by($model, NULL, $params);
    }
    
    public function find_all_dialogs_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL) {
        $table = $this->table_name();

        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->distinct('dialog_id')
                ->from($table);
        }

        if (!isset($params['order_by'])) {
            $params['order_by'] = 'created_at';
            $params['desc']     = FALSE;
        }        

        $params['for'] = Auth::instance()->get_user();
                
        return parent::find_all_by($model, $condition, $params, $query);
    }*/
    
    /**
     * Count all models
     *
     * @param Model_Message $model
     * @return integer
     */
    /*public function count_dialogs(Model_Message $model)
    {
        return $this->count_dialogs_by($model, NULL);
    } */   

    /**
     * Count all dialogs by given condition
     * 
     * @param Model_Message $model
     * @param  string|array|Database_Expression_Where $condition
     * @return integer
     */
    /*public function count_dialogs_by(Model_Message $model, $condition = NULL, array $params = NULL)
    {
        $table = $this->table_name();
        
        $query = DB::select(array('COUNT(DISTINCT "' . $table . '.dialog_id")','total_count'))
            ->from($table);

              
        $user = Auth::instance()->get_user();

        $resource_table = Model_Mapper::factory('Model_Resource_Mapper')->table_name();

        $pk = $this->get_pk();

        $query
            ->join($resource_table, 'LEFT')
                ->on("$resource_table.resource_id", '=', "$table.$pk")                            
           ->where("$resource_table.role_id", '=', $user->id)
           ->and_where("$resource_table.resource_type", '=', get_class($model))
           ->and_where("$resource_table.role_type", '=', get_class($user));      
        
        
        if ($condition !== NULL)
        {
            $condition = $this->_prepare_condition($condition);
            $query->where($condition, NULL, NULL);
        }
        
        $count = $query->execute($this->get_db())
            ->get('total_count');

        return (int) $count;
     }*/    
    /**
     * Magic method - automatically resolves the following methods:
     *  - find_all_dialogs_by_*
     *  - count_dialogs_by_*
     *
     * @param  string $name
     * @param  array $arguments
     * @return mixed
     */
    /*public function  __call($name, array $arguments)
    {
        // Model is always a first argument to mapper method
        $model = array_shift($arguments);

        // find_all_dialogs_by_* methods
        if (strpos($name, 'find_all_dialogs_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('find_all_dialogs_by_')), $arguments, $name);
            $params = array_shift($arguments);

            return $this->find_all_dialogs_by($model, $condition, $params);
        }
        // count_dialogs_by_* methods
        if (strpos($name, 'count_dialogs_by_') === 0)
        {
            $condition = $this->_cols_to_condition_array(substr($name, strlen('count_dialogs_by_')), $arguments, $name);
            $params = array_shift($arguments);

            return $this->count_dialogs_by($model, $condition, $params);
        }
        
        return parent::__call($name, $arguments);
    }*/
}