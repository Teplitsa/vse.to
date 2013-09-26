<?php defined('SYSPATH') or die('No direct script access.');

class Model_PropertySection extends Model
{
    /**
     * Get "active" property
     * 
     * @return boolean
     */
    public function get_active()
    {        
        if ($this->system)
        {
            // System properties are always active
            return 1;
        }
        else
        {
            return $this->_properties['active'];
        }
    }

    /**
     * Set "active" property
     *
     * @param <type> $value
     */
    public function set_active($value)
    {
        // System properties are always active
        if ($this->system)
        {
            $value = 1;
        }

        $this->_properties['active'] = $value;
    }

    /**
     * Link given property to sections
     * 
     * @param Model_Property $property
     * @param array $propsections
     */
    public function link_property_to_sections(Model_Property $property, array $propsections)
    {
        // Delete all info
        $this->delete_all_by_property_id($property->id);

        $propsection = Model::fly('Model_PropertySection');
        foreach ($propsections as $values)
        {
            $propsection->values($values);
            $propsection->property_id = $property->id;
            $propsection->save();
        }
    }

    /**
     * Link given section to properties
     *
     * @param Model_Section $section
     * @param array $propsections
     */
    public function link_section_to_properties(Model_Section $section, array $propsections)
    {
        // Delete all info
        $this->delete_all_by_section_id($section->id);

        $propsection = Model::fly('Model_PropertySection');
        foreach ($propsections as $values)
        {
            $propsection->values($values);
            $propsection->section_id = $section->id;
            $propsection->save();
        }
    }
}