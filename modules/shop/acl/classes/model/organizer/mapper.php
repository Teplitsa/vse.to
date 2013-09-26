    <?php defined('SYSPATH') or die('No direct script access.');

class Model_Organizer_Mapper extends Model_Mapper {    
    public function init()
    {
        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('name',  array('Type' => 'varchar(63)'));

        $this->add_column('town_id',  array('Type' => 'int unsigned','Key' => 'INDEX'));
        
        $this->add_column('type', array('Type' => 'varchar(63)',  'Key' => 'INDEX'));

        $this->add_column('address', array('Type' => 'text'));
        
        $this->add_column('links', array('Type' => 'array'));
        
        $this->add_column('info', array('Type' => 'text'));  
    }

    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $table = $this->table_name();
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
        if ( ! isset($params['with_town']) || ! empty($params['with_town']))
        {
            $table = $this->table_name();
            $town_table = Model_Mapper::factory('Model_Town_Mapper')->table_name();
            
            // Add column to query
            $query->select(array(DB::expr("town.name"), 'town_name'));

            $query->join(array($town_table, "town"), 'LEFT')
                ->on(DB::expr("town.id"), '=', "$table.town_id");

        }
        return parent::find_by($model, $condition, $params, $query);
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
        
//        if ( ! isset($params['with_town']) || ! empty($params['with_town']))
//        {
//            $table = $this->table_name();
//            $town_table = Model_Mapper::factory('Model_Town_Mapper')->table_name();
//            
//            // Add column to query
//            $query->select(array(DB::expr("town.name"), 'town_name'));
//
//            $query->join(array($town_table, "town"), 'LEFT')
//                ->on(DB::expr("town.id"), '=', "$table.town_id");
//
//        }
        
        return parent::find_all_by($model, $condition, $params, $query);
    }
    
    /**
     * Find all organizations by part of the name
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
                ->where('name', 'LIKE', "$name%");
        
        return $this->find_all_by($model, NULL, $params, $query);
    }    
}