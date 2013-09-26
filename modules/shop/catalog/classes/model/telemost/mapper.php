<?php defined('SYSPATH') or die('No direct script access.');

class Model_Telemost_Mapper extends Model_Mapper_Resource
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('product_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('place_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        
        $this->add_column('event_uri',    array('Type' => 'varchar(127)'));        

        $this->add_column('info', array('Type' => 'text'));

        $this->add_column('active', array('Type' => 'boolean', 'Key' => 'INDEX'));

        $this->add_column('visible', array('Type' => 'boolean', 'Key' => 'INDEX'));
        
        $this->add_column('created_at', array('Type' => 'int unsigned'));
        
//        $this->cache_find_all = TRUE;        
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
        
        if (is_array($condition) && isset($condition['town']))
        {           
            $place_table = Model_Mapper::factory('Model_Place_Mapper')->table_name();
            
            $query
                ->where("$place_table.town_id", '=', (int) $condition['town']->id);

                $params['join_place']   = TRUE;

            unset($condition['town']);
        }
        
        // ----- joins
        $this->_apply_joins($query, $params);
        
        return parent::find_all_by($model, $condition, $params, $query);
    }
    
    /**
     * Apply joins to the query
     *
     * @param Database_Query_Builder_Select $query
     * @param array $params
     */
    protected function _apply_joins(Database_Query_Builder_Select $query, array $params = NULL)
    {
        $table = $this->table_name();

        if ( ! empty($params['join_place'])) 
        {        
            $place_table = Model_Mapper::factory('Model_Place_Mapper')->table_name();

            $query
                ->join($place_table, 'LEFT')
                    ->on("$place_table.id", '=', "$table.place_id");
        }        
    }
    
    
}