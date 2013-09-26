<?php defined('SYSPATH') or die('No direct script access.');

class Model_Privilege extends Model
{
    /**
     * Registered node types
     * @var array
     */
    protected static $_privilege_types = array();

    /**
     * Add new acl type
     *
     * @param string $type
     * @param array  $type_info
     */
    public static function add_privilege_type($type, array $type_info)
    {
        self::$_privilege_types[$type] = $type_info;
    }

    /**
     * Returns list of all registered privilege types
     *
     * @return array
     */
    public static function privilege_types()
    {
        return self::$_privilege_types;
    }

    /**
     * Get privilege type information
     *
     * @param string $type Type name
     * @return array
     */
    public static function privilege_type($type)
    {
        if (isset(self::$_privilege_types[$type]))
        {
            return self::$_privilege_types[$type];
        }
        else
        {
            return NULL;
        }
    }
    
    public static function privilege_name(Request $request = NULL) {
        if ($request === NULL)
        {
            $request = Request::current();
        }

        $controller = $request->controller;
        $action = $request->action;
        
        foreach (self::$_privilege_types as $type => $privilege) {
            if (isset($privilege['controller']) && isset($privilege['action'])) {
                if ($privilege['controller'] == $controller && $privilege['action'] == $action) {
                    if (isset($privilege['route_params'])) {
                        $valid = TRUE;
                        foreach ($privilege['route_params'] as $param => $value) {
                            $param_value = $request->param($param,NULL);
                            $valid = $valid & ($param_value == $value);
                        }
                        if (!$valid) continue;
                    }
                    return $type;                    
                }
            }
        }
        return NULL;
    }
    
    /**
     * Get frontend URI to this node
     *
     * @return string
     */
    public function get_frontend_uri()
    {
        $type_info = self::privilege_type($this->name);

        if ($type_info === NULL)
        {
            throw new Kohana_Exception('Type info for privilege type ":type" was not found! (May be you have fogotten to register this privilege type?)',
                    array(':type' => $this->name));
        }

        $url_params = array();

        if (isset($type_info['frontend_route_params']))
        {
            $url_params += $type_info['frontend_route_params'];
        }
        return URL::uri_to($type_info['frontend_route'], $url_params);
    }
    
    public function get_readable() {
        $type_info = self::privilege_type($this->name);

        if ($type_info === NULL)
        {
            throw new Kohana_Exception('Type info for privilege type ":type" was not found! (May be you have fogotten to register this privilege type?)',
                    array(':type' => $this->name));
        }

        return $type_info['readable'];
    }
    
    public function valid(Request $request = NULL) {
        if ($request === NULL)
        {
            $request = Request::current();
        }
        
        $type_info = self::privilege_type($this->name);

        if ($type_info === NULL)
        {
            return FALSE;
        }
        
        $valid = TRUE;
        
        if (isset($type_info['access_route_params'])) {
            foreach ($type_info['access_route_params'] as $param => $value) {
                $param_value = $request->param($param,NULL);
                $valid = ($param_value ==$value); 
            }
        }
        return $valid;
    }
    /**
     * Set privilege variants
     * 
     * @param array $options
     */
    public function set_options(array $options)
    {
        $options = array_values(array_filter($options));
        $this->_properties['options'] = $options;
    }

    /**
     * Created privileges are non-system
     * 
     * @return boolean
     */
    public function default_system()
    {
        return 0;
    }

    /**
     * Prohibit changing the "system" privilege
     *
     * @param boolean $value
     */
    public function set_system($value)
    {
       //Intentionally blank
    }

    /**
     * Get privilege-group infos
     * 
     * @return Models
     */
    public function get_privilegegroups()
    {
        if ( ! isset($this->_properties['privilegegroups']))
        {
            $this->_properties['privilegegroups'] =
                Model::fly('Model_PrivilegeGroup')->find_all_by_privilege($this);
        }
        return $this->_properties['privilegegroups'];
    }

    /**
     * Get privilege-groups infos as array for form
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
                $result[$privgroup->group_id]['active'] = $privgroup->active;
            }
            
            $this->_properties['privgroups'] = $result;
        }
        return $this->_properties['privgroups'];
    }

    /**
     * Set privilege-group link info (usually from form - so we need to add 'group_id' field)
     * 
     * @param array $privgroups
     */
    public function set_privgroups(array $privgroups)
    {
        foreach ($privgroups as $group_id => & $privgroup)
        {
            if ( ! isset($privgroup['group_id']))
            {
                $privgroup['group_id'] = $group_id;
            }
        }

        $this->_properties['privgroups'] = $privgroups;        
    }

    /**
     * Save privilege and link it to selected groups
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);
        // Link property to selected sections
        Model::fly('Model_PrivilegeGroup')->link_privilege_to_groups($this, $this->privgroups);
    }

    /**
     * Delete privilege
     */
    public function delete()
    {
        // Delete all user values for this privilege
        Model_Mapper::factory('Model_PrivilegeValue_Mapper')->delete_all_by_privilege_id($this, $this->id);

        // Delete privilege binding information
        Model::fly('Model_PrivilegeGroup')->delete_all_by_privilege_id($this->id);
        
        // Delete the privilege
        $this->mapper()->delete($this);
    }

    /**
     * Validate creation/updation of privilege
     *
     * @param  array $newvalues
     * @return boolean
     */
    /*public function validate(array $newvalues)
    {
        // Check that privilege name is unque
        if ( ! isset($newvalues['name']))
        {
            $this->error('Вы не указали имя!', 'name');
            return FALSE;
        }
        
        if ($this->exists_another_by_name($newvalues['name']))
        {
            $new_privgroups = $newvalues['privgroups'];            
            $old_privileges = $this->find_another_all_by_name($newvalues['name']);
            foreach ($old_privileges as $old_privilege) {
                $old_privgroups = $old_privilege->get_privgroups();
                
                foreach ($old_privgroups as $old_key => $old_privgroup) {
                    if ($old_privgroup['active']) {
                        if (isset($new_privgroups[$old_key]) && $new_privgroups[$old_key]['active']) {
                            $this->error('Привилегия с таким именем уже существует!', 'name');
                            return FALSE;                        
                        }
                    } 
                }
            }          
        }

        return TRUE;
    }*/
    
    /**
     * Validate privilege deletion
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->system)
        {
            $this->error('Привилегия является системной. Её удаление запрещено!');
            return FALSE;
        }

        return parent::validate_delete($newvalues);
    }
}