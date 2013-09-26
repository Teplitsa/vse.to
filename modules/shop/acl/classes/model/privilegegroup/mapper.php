<?php defined('SYSPATH') or die('No direct script access.');

class Model_PrivilegeGroup_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('privilege_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('group_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('active', array('Type' => 'boolean', 'Key' => 'INDEX'));
    }

    /**
     * Link privilege to given groups
     * 
     * @param Model_Privilege $privilege
     * @param array $privgroups
     */
    public function link_privilege_to_groups(Model_Privilege $privilege, array $privgroups)
    {                
        $this->delete_rows(DB::where('privilege_id', '=', (int) $privilege->id));
        foreach ($privgroups as $privgroup)
        {
            $privgroup['privilege_id'] = $privilege->id;
            $this->insert($privgroup);
        }    }

    /**
     * Link group to given privileges
     *
     * @param Model_Group $group
     * @param array $privgroups
     */
    public function link_group_to_privileges(Model_Group $group, array $privgroups)
    {
        $this->delete_rows(DB::where('group_id', '=', (int) $group->id));
        foreach ($privgroups as $privgroup)
        {
            $privgroup['group_id'] = $group->id;

            $this->insert($privgroup);
        }
    }

    /**
     * Find all privilege-group infos by given group
     *
     * @param  Model_PrivilegeGroup $privgr
     * @param  Model_Group $group
     * @param  array $params
     * @return Models
     */
    public function find_all_by_group(Model_PrivilegeGroup $privgr, Model_Group $group, array $params = NULL)
    {
        $privgr_table  = $this->table_name();
        $privilege_table = Model_Mapper::factory('Model_Privilege_Mapper')->table_name();
        $group_table  = Model_Mapper::factory('Model_Group_Mapper')->table_name();

        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            array("$privilege_table.id", 'privilege_id'),
                            "$privilege_table.caption",
                            "$privilege_table.system",

                            "$privgr_table.active",
                        );

        $query = DB::select_array($columns)
            ->from($privilege_table)
            ->join($privgr_table, 'LEFT')
                ->on("$privgr_table.privilege_id", '=', "$privilege_table.id")
                ->on("$privgr_table.group_id", '=', DB::expr((int) $group->id));

        $data = parent::select(NULL, $params, $query);

        // Add group properties
        foreach ($data as & $privileges)
        {
            $privileges['group_id']      = $group->id;
            $privileges['group_name'] = $group->name;
        }

        return new Models(get_class($privgr), $data);
    }

    /**
     * Find all privilege-group infos by given privilege
     *
     * @param  Model_PrivilegeGroup $privgr
     * @param  Model_Privilege $privilege
     * @param  array $params
     * @return Models
     */
    public function find_all_by_privilege(Model_PrivilegeGroup $privgr, Model_Privilege $privilege, array $params = NULL)
    {
        $privgr_table  = $this->table_name();
        $privilege_table = Model_Mapper::factory('Model_Privilege_Mapper')->table_name();
        $group_table  = Model_Mapper::factory('Model_Group_Mapper')->table_name();

        $columns = isset($params['columns'])
                        ? $params['columns']
                        : array(
                            array("$group_table.id", 'group_id'),
                            array("$group_table.name", 'group_name'),

                            "$privgr_table.active"
                        );

        $query = DB::select_array($columns)
            ->from($group_table)
            ->join($privgr_table, 'LEFT')
                ->on("$privgr_table.group_id", '=', "$group_table.id")
                ->on("$privgr_table.privilege_id", '=', DB::expr((int) $privilege->id));

        $data = parent::select(array(), $params, $query);

        // Add property properties
        foreach ($data as & $privileges)
        {
            $privileges['privilege_id'] = $privilege->id;
            $privileges['caption']     = $privilege->caption;
            $privileges['system']      = $privilege->system;
        }
        
        return new Models(get_class($privgr), $data);
    }    
}