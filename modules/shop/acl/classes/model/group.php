<?php defined('SYSPATH') or die('No direct script access.');

class Model_Group extends Model
{
    const ADMIN_GROUP_ID = 1;
    const EDITOR_GROUP_ID = 2;
    const USER_GROUP_ID = 3;
    /**
     * Does this group have the requested privilege?
     *
     * @param  string $privilege
     * @return boolean
     */
    public function granted($privilege)
    {
        if ($this->id === NULL) {
            // deny access for all unauthorized users
            return false;
        }
        if (isset($this->privileges_hash[$privilege])) {
            
            $privilege_desc = $this->privileges_hash[$privilege];
            
            return ($privilege_desc['value'] == 1);
        }
        return false;
    }

   /**
     * Group is not system by default
     *
     * @return boolean
     */
    public function default_system()
    {
        return FALSE;
    }


    /**
     * Get privileges for this group
     * 
     * @return array
     */
    /*public function get_privileges()
    {
        if ($this->system)
        {
            // System groups has all privileges
            $privileges = array();
            foreach (array_keys(Auth::instance()->privileges()) as $privilege)
            {
                $privileges[$privilege] = TRUE;
            }
            return $privileges;
        }
        elseif (isset($this->_properties['privileges']))
        {
            return $this->_properties['privileges'];
        }
        else
        {
            return $this->default_privileges();
        }
    }*/
    /**
     * Get active[!] privileges for this group
     */
    public function get_privileges()
    {  
        if ( ! isset($this->_properties['privileges']))
        {
            if ($this->id !== NULL) {
                $privileges = Model::fly('Model_Privilege')->find_all_by_group_id_and_active_and_system(
                    $this->id, 1, 0, 
                    array('order_by' => 'position', 'desc' => FALSE)
                );

            } else {
                $privileges = new Models('Model_Privilege',array());
            }
            $this->_properties['privileges'] = $privileges;
        } 
        return $this->_properties['privileges'];
    }
    
    public function get_privileges_hash()
    {
        if (!isset($this->_properties['privileges_hash']))
        {
            $privileges_hash = array();
            $privileges = $this->privileges;

            foreach ($privileges as $privilege) {
                $name = $privilege->name;
                
                if (isset($this->$name)) {
                    $privilege_desc['value'] = $this->$name;
                    $privileges_hash[$name] = $privilege_desc;
                }
            }
            $this->_properties['privileges_hash'] = $privileges_hash;
        }
        return $this->_properties['privileges_hash'];            
    }

    public function get_privileges_granted()
    {
        if (!isset($this->_properties['privileges_granted']))
        {
            $privileges = $this->privileges;
            $privileges_granted = clone $privileges;
            $privileges_granted_arr = array();
            foreach ($privileges_granted as $key =>$privilege) {
                if ($this->granted($privilege->name)) {
                    $privileges_granted_arr[$privilege->id] = $privilege->values();
                }
            }
            $this->_properties['privileges_granted'] = new Models('Model_Privilege',$privileges_granted_arr);
        }
        return $this->_properties['privileges_granted'];            
    }
    /**
     * Set group privileges
     * @param array $privileges
     */
    /*public function set_privileges(array $privileges)
    {
        if ( ! $this->system)
        {
            // Privileges can be changed only for non-system groups
            $this->_properties['privileges'] = $privileges;
        }
    }*/

    /**
     * Set system flag
     *
     * @param boolean $system
     */
    public function set_system($system)
    {
        // Prohibit setting system property for group - do nothing
    }

    /**
     * Is group valid to be deleted?
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->system)
        {
            $this->error('Группа является системной. Её удаление запрещено!', 'system');
            return FALSE;
        }

        return TRUE;
    }
    /**
     * Get property-section infos
     *
     * @return Models
     */
    public function get_privilegegroups()
    {        
        if ( ! isset($this->_properties['privilegegroups']))
        {
            $this->_properties['privilegegroups'] =
                Model::fly('Model_PrivilegeGroup')->find_all_by_group($this, array('order_by' => 'position', 'desc' => FALSE));
        }

        return $this->_properties['privilegegroups'];
    }

    /**
     * Get property-section infos as array for form
     *
     * @return array
     */
    public function get_privgroups()
    {
        if ( ! isset($this->_properties['privgroups']))
        {
            $result = array();

            foreach ($this->privilegegroups as $privgroup)
            {
                if ($privgroup->privilege_id !== NULL) 
                        $result[$privgroup->privilege_id]['privilege_id'] = $privgroup->privilege_id; 
                $result[$privgroup->privilege_id]['active'] = $privgroup->active;
            }
            $this->_properties['privgroups'] = $result;
        }
        return $this->_properties['privgroups'];
    }

    /**
     * Set property-section link info (usually from form - so we need to add 'section_id' field)
     *
     * @param array $propsections
     */
    public function set_privgroups(array $privgroups)
    {
        foreach ($privgroups as $privilege_id => & $privgroup)
        {
            if ( ! isset($privgroup['privilege_id']))
            {
                $privgroup['privilege_id'] = $privilege_id;
            }
        }

        $this->_properties['privgroups'] = $privgroups;
    }
    
    public function save($create = FALSE,$update_privileges = TRUE)
    {
        parent::save($create);

        if ($update_privileges)
        {
            // Link section to the properties
            Model::fly('Model_PrivilegeGroup_Mapper')->link_group_to_privileges($this, $this->privgroups);
            // Update values for additional properties
            Model_Mapper::factory('Model_PrivilegeValue_Mapper')->update_values_for_group($this);
        }        
    }
    /**
     * Delete group and all users from it
     */
    public function delete()
    {
        // Delete all users from group
        Model::fly('Model_User')->delete_all_by_group_id($this->id);
        
        // Delete privilege values for this group
        Model_Mapper::factory('Model_PrivilegeGroup_Mapper')->delete_all_by_group_id($this, $this->id);

        // Delete privilege values for this product
        Model_Mapper::factory('Model_PrivilegeValue_Mapper')->delete_all_by_group_id($this, $this->id);

        // Delete group itself
        parent::delete();
    }    
}