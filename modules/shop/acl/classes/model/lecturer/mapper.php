<?php defined('SYSPATH') or die('No direct script access.');

class Model_Lecturer_Mapper extends Model_Mapper {    
    public function init()
    {
        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('first_name',  array('Type' => 'varchar(63)'));
        $this->add_column('last_name',   array('Type' => 'varchar(63)'));
        $this->add_column('middle_name', array('Type' => 'varchar(63)'));

        $this->add_column('links', array('Type' => 'array'));
        
        $this->add_column('info', array('Type' => 'text'));  
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
    public function find_all_by(
        Model                         $model,
                                      $condition = NULL,
        array                         $params = NULL,
        Database_Query_Builder_Select $query = NULL
    )
    {
        $table = $this->table_name();

        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->distinct('whatever')
                ->from($table);
        }
        
        // ----- process contition
        if (is_array($condition) &&  ! empty($condition['ids']))
        {
            // find product by several ids
            $query->where("$table.id", 'IN', DB::expr('(' . implode(',', $condition['ids']) . ')'));

            unset($condition['ids']);
        }
        
        return parent::find_all_by($model, $condition, $params, $query);
    }
    
    /**
     * Find all lecturers by part of the name
     *
     * @param  Model $model
     * @param  string $name
     * @param  array $params
     * @return Models
     */
    public function find_all_like_name(Model $model, $name, array $params = NULL)
    {
        $table = $this->table_name();
        
        $query = DB::select_array($this->_prepare_columns($params))
                ->distinct('whatever')
                ->from($table)
                ->where('first_name', 'LIKE', "$name%")
                ->or_where('last_name', 'LIKE', "$name%")
                ->or_where('middle_name', 'LIKE', "$name%");
        
       
        return $this->find_all_by($model, NULL, $params, $query);
    }    
}