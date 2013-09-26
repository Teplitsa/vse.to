<?php defined('SYSPATH') or die('No direct script access.');

class Model_Property extends Model
{
    const TYPE_TEXT   = 1; // Text property
    const TYPE_TEXTAREA= 3; // Text area property
    const TYPE_SELECT = 2; // Property with value from given list of options
    
    const MAXLENGTH   = 512;
    const MAX_PROPERTY   = 63; // Maximum length for the property value
    const MAX_TEXTAREA = 512; // Maximum length for the textarea property
    const MAX_TEXT = 63; // Maximum length for the text property

    protected static $_types = array(
        Model_Property::TYPE_TEXT   => 'Текст',
        Model_Property::TYPE_TEXTAREA   => 'Параграф',        
        Model_Property::TYPE_SELECT => 'Список опций'
    );

    /**
     * Get all possible property types
     * 
     * @return array
     */
    public function get_types()
    {
        return self::$_types;
    }

    /**
     * Get text description for the type of the property
     * 
     * @return string
     */
    public function get_type_text()
    {
        return (isset(self::$_types[$this->type]) ? self::$_types[$this->type] : NULL);
    }

    /**
     * Default property type
     * 
     * @return integer
     */
    public function default_type()
    {
        return Model_Property::TYPE_TEXT;
    }

    /**
     * Default property type
     * 
     * @return integer
     */
    public function default_site_id()
    {
        return Model_SIte::current()->id;
    }
    
    /**
     * Set property variants
     * 
     * @param array $options
     */
    public function set_options(array $options)
    {
        $options = array_values(array_filter($options));
        $this->_properties['options'] = $options;
    }

    /**
     * Created properties are non-system
     * 
     * @return boolean
     */
    public function default_system()
    {
        return 0;
    }

    /**
     * Prohibit changing the "system" property
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
        // Do not allow to change type for system properties
        if ($this->system)
            return;

        $this->_properties['type'] = $value;
    }

    /**
     * Get property-section infos
     * 
     * @return Models
     */
    public function get_propertysections()
    {
        if ( ! isset($this->_properties['propertysections']))
        {            
            $this->_properties['propertysections'] =
                Model::fly('Model_PropertySection')->find_all_by_property($this);

        }
        return $this->_properties['propertysections'];
    }

    /**
     * Get property-section infos as array for form
     *
     * @return array
     */
    public function get_propsections()
    {
        if ( ! isset($this->_properties['propsections']))
        {
            $result = array();

            foreach ($this->propertysections as $propsection)
            {
                $result[$propsection->section_id]['active']     = (int)$propsection->active;
                $result[$propsection->section_id]['filter']     = (int)$propsection->filter;
                $result[$propsection->section_id]['sort']       = (int)$propsection->sort;
                $result[$propsection->section_id]['section_id'] = (int)$propsection->section_id;                
            }

            $this->_properties['propsections'] = $result;
        }
        return $this->_properties['propsections'];
    }

    /**
     * Set property-section link info (usually from form - so we need to add 'section_id' field)
     * 
     * @param array $propsections
     */
    public function set_propsections(array $propsections)
    {
        foreach ($propsections as $section_id => & $propsection)
        {
            if ( ! isset($propsection['section_id']))
            {
                $propsection['section_id'] = $section_id;
            }
        }
        $propsections = array_replace($this->propsections, $propsections);
        $this->_properties['propsections'] = $propsections;        
    }

    /**
     * Save property and link it to selected sections
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);

        // Link property to selected sections
        Model::fly('Model_PropertySection')->link_property_to_sections($this, $this->propsections);
    }

    /**
     * Delete property
     */
    public function delete()
    {
        // Delete all product values for this property
        Model_Mapper::factory('PropertyValue_Mapper')->delete_all_by_property_id($this, $this->id);

        // Delete section binding information
        Model::fly('Model_PropertySection')->delete_all_by_property_id($this->id);
        
        // Delete the property
        $this->mapper()->delete($this);
    }

    /**
     * Validate creation/updation of property
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        // Check that property name is unque
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
     * Validate property deletion
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