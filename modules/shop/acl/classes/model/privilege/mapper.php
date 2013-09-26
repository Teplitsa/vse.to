<?php defined('SYSPATH') or die('No direct script access.');

class Model_Privilege_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('position', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('caption', array('Type' => 'varchar(31)'));
        $this->add_column('name',    array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        
        $this->add_column('options', array('Type' => 'array'));

        $this->add_column('system', array('Type' => 'boolean'));
    }


    /**
     * Find all privileges by given condition
     *
     * @param  Model $model
     * @param  string|array|Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Models|array
     */
    public function find_all_by(Model $model, $condition = NULL, array $params = NULL, Database_Query_Builder_Select $query = NULL)
    {
        if (is_array($condition) && ! empty($condition['group_id']))
        {            
            // Find properties that apply to selected section
            $columns = $this->_prepare_columns($params);

            $table = $this->table_name();
            $privgr_table = Model_Mapper::factory('Model_PrivilegeGroup_Mapper')->table_name();
            
            $query = DB::select_array($columns)
                ->from($table)
                ->join($privgr_table, 'INNER')
                    ->on("$privgr_table.privilege_id", '=', "$table.id")
                    ->on("$privgr_table.group_id", '=', DB::expr((int) ($condition['group_id'])));

            if (isset($condition['active']))
            {
                $query->and_where("$privgr_table.active", '=', $condition['active']);
                unset($condition['active']);
            }
            
            unset($condition['group']);
        }
        return parent::find_all_by($model, $condition, $params, $query);
    }

    /**
     * Move privilege up
     *
     * @param Model $privilege
     * @param Database_Expression_Where $condition
     */
    public function up(Model $privilege, Database_Expression_Where $condition = NULL)
    {
        parent::up($privilege, DB::where('site_id', '=', $privilege->site_id));
    }

    /**
     * Move property down
     *
     * @param Model $property
     * @param Database_Expression_Where $condition
     */
    public function down(Model $privilege, Database_Expression_Where $condition = NULL)
    {
        parent::down($privilege, DB::where('site_id', '=', $privilege->site_id));
    }
}