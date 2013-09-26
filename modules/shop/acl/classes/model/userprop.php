<?php defined('SYSPATH') or die('No direct script access.');

class Model_UserProp extends Model
{
    const TYPE_TEXT   = 1; // Checkbox userprop
    const TYPE_SELECT = 2; // Userprop with value from given list of options
    const MAXLENGTH   = 63; // Maximum length for the property value
    
    protected static $_types = array(
        Model_UserProp::TYPE_TEXT   => 'Текст',
        Model_UserProp::TYPE_SELECT => 'Список опций'
    );
    
    /**
     * Get all possible userprop types
     * 
     * @return array
     */
    public function get_types()
    {
        return self::$_types;
    }

    /**
     * Get text description for the type of the userprop
     * 
     * @return string
     */
    public function get_type_text()
    {
        return (isset(self::$_types[$this->type]) ? self::$_types[$this->type] : NULL);
    }

    /**
     * Default userprop type
     * 
     * @return integer
     */
    public function default_type()
    {
        return Model_UserProp::TYPE_TEXT;
    }

    /**
     * Set userprop variants
     * 
     * @param array $options
     */
    public function set_options(array $options)
    {
        $options = array_values(array_filter($options));
        $this->_properties['options'] = $options;
    }

    /**
     * Created userprops are non-system
     * 
     * @return boolean
     */
    public function default_system()
    {
        return 0;
    }

    /**
     * Prohibit changing the "system" userprop
     *
     * @param boolean $value
     */
    public function set_system($value)
    {
       //Intentionally blank
    }

    /**
     * @param integer $value
     */
    public function set_type($value)
    {
        // Do not allow to change type for system userprops
        if ($this->system)
            return;

        $this->_properties['type'] = $value;
    }

    /**
     * Get user-props infos
     * 
     * @return Models
     */
    public function get_userproperties()
    {
        if ( ! isset($this->_properties['userproperties']))
        {
            $this->_properties['userproperties'] =
                Model::fly('Model_UserPropUser')->find_all_by_userprop($this);
        }
        return $this->_properties['userproperties'];
    }

    /**
     * Get user-props infos as array for form
     *
     * @return array
     */
    public function get_userprops()
    {
        if ( ! isset($this->_properties['userprops']))
        {
            $result = array();
            
            foreach ($this->userproperties as $userprop)
            {
                $result[$userprop->user_id]['active'] = $userprop->active;
            }
            
            $this->_properties['userprops'] = $result;
        }
        return $this->_properties['userprops'];
    }

    /**
     * Set user-prop link info (usually from form - so we need to add 'user_id' field)
     * 
     * @param array $privgroups
     */
    public function set_userprops(array $userprops)
    {
        foreach ($userprops as $user_id => & $userprop)
        {
            if ( ! isset($userprop['user_id']))
            {
                $userprop['user_id'] = $user_id;
            }
        }

        $this->_properties['userprops'] = $userprops;        
    }

    /**
     * Save userprop and link it to selected groups
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);
        // Link property to selected sections
        Model::fly('Model_UserPropUser')->link_userprop_to_users($this, $this->userprops);
    }

    /**
     * Delete privilege
     */
    public function delete()
    {
        // Delete all user values for this privilege
        Model_Mapper::factory('Model_UserPropValue_Mapper')->delete_all_by_userprop_id($this, $this->id);

        // Delete userprop binding information
        Model::fly('Model_UserPropUser')->delete_all_by_userprop_id($this->id);
        
        // Delete the userprop
        $this->mapper()->delete($this);
    }

    /**
     * Validate creation/updation of userprop
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        // Check that privilege name is unque
        if ( ! isset($newvalues['name']))
        {
            $this->error('Вы не указали имя!', 'name');
            return FALSE;
        }

        if ($this->exists_another_by_name($newvalues['name']))
        {
            $this->error('Свойство с таким именем уже существует!', 'name');
            return FALSE;
        }

        return TRUE;
    }
    
    /**
     * Validate userprop deletion
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ($this->system)
        {
            $this->error('Свойство является системным. Его удаление запрещено!');
            return FALSE;
        }

        return parent::validate_delete($newvalues);
    }
}