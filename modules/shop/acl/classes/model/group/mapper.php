<?php defined('SYSPATH') or die('No direct script access.');

class Model_Group_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('system', array('Type' => 'boolean'));

        $this->add_column('name', array('Type' => 'varchar(31)'));
    }
    /**
     * Find group by condition
     * Load additional properties
     *
     * @param Model $model
     * @param string|array|Database_Condition_Where $condition
     * @param array $params
     * @param Database_Query_Builder_Select $query
     */
    
    public function find_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        $table = $this->table_name();
        if ($query === NULL)
        {
            $query = DB::select_array($this->_prepare_columns($params))
                ->from($table);
        }
        if ( ! isset($params['with_privileges']) || ! empty($params['with_privileges']))
        {   
            $priv_condition = array(
                'site_id' => Model_Site::current()->id,
                'system' => 0,
                'active' => '1'                
            );
            if (isset($condition['id'])) {
                $priv_condition['group_id'] = $condition['id']; 
            }
            // Select groups with additional (non-system) privilege values
            $privileges = Model::fly('Model_Privilege')->find_all_by(
                $priv_condition,
                array(
                    'columns'  => array('id', 'name'),
                    'order_by' => 'position',
                    'desc'     => FALSE,
                    'as_array' => TRUE
            ));

            $table = $this->table_name();
            $privilegevalue_table = Model_Mapper::factory('Model_PrivilegeValue_Mapper')->table_name();
            foreach ($privileges as $privilege)
            {
                $id = (int) $privilege['id'];

                // Add column to query
                $query->select(array(DB::expr("privval$id.value"), $privilege['name']));

                $query->join(array($privilegevalue_table, "privval$id"), 'LEFT')
                    ->on(DB::expr("privval$id.privilege_id"), '=', DB::expr($id))
                    ->on(DB::expr("privval$id.group_id"), '=', "$table.id");
            }
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

        if ( ! empty($params['with_privileges']))
        {
            // Select product with additional (non-system) property values
            // @TODO: use only properties for current section
            $privileges = Model::fly('Model_Privilege')->find_all_by_site_id_and_system(Model_Site::current()->id, 0, array(
                'columns'  => array('id', 'name'),
                'order_by' => 'position',
                'desc'     => FALSE,
                'as_array' => TRUE
            ));

            $table = $this->table_name();
            $privilegevalue_table = Model_Mapper::factory('Model_PrivilegeValue_Mapper')->table_name();

            foreach ($privileges as $privilege)
            {
                $id = (int) $privilege['id'];

                // Add column to query
                $query->select(array(DB::expr("privval$id.value"), $privilege['name']));
                
                $query->join(array($privilegevalue_table, "privval$id"), 'LEFT')
                    ->on(DB::expr("privval$id.privilege_id"), '=', DB::expr($id))
                    ->on(DB::expr("privval$id.group_id"), '=', "$table.id");
            }
        }
        return parent::find_all_by($model, $condition, $params, $query);
    }
    
    
}