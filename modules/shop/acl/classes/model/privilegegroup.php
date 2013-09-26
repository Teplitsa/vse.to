<?php defined('SYSPATH') or die('No direct script access.');

class Model_PrivilegeGroup extends Model
{
    /**
     * Get "active" privilege
     * 
     * @return boolean
     */
    public function get_active()
    {        
        if ($this->system)
        {
            // System privileges are always active
            return 1;
        }
        elseif (isset($this->_properties['active']))
        {
            return $this->_properties['active'];
        }
        else
        {
            return 0; // default value
        }
    }

    /**
     * Set "active" privilege
     *
     * @param <type> $value
     */
    public function set_active($value)
    {
        // System privileges are always active
        if ($this->system)
        {
            $value = 1;
        }

        $this->_properties['active'] = $value;
    }

    /**
     * Link given privilege to group
     * 
     * @param Model_Privilege $privilege
     * @param array $privgroups
     */
    public function link_privilege_to_groups(Model_Privilege $privilege, array $privgroups)
    {
        // Delete all info
        $this->delete_all_by_privilege_id($privilege->id);
        
        $privgroup = Model::fly('Model_PrivilegeGroup');
        foreach ($privgroups as $values)
        {
            $privgroup->values($values);
            $privgroup->privilege_id = $privilege->id;
            $privgroup->save();
        }
    }

    /**
     * Link given group to privileges
     *
     * @param Model_Group $group
     * @param array $privgroups
     */
    public function link_group_to_privileges(Model_Group $group, array $privgroups)
    {
        // Delete all info
        $this->delete_all_by_group_id($group->id);

        $privgroup = Model::fly('Model_PrivilgeGroup');
        foreach ($privgroups as $values)
        {
            $privgroup->values($values);
            $privgroup->group_id = $group->id;
            $privgroup->save();
        }
    }
}