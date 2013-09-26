<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Checkbox that can enables/disables form fields
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Checkbox_Enable extends Form_Element_Checkbox
{
    /**
     * Element names that are enabled/disabled by this checkbox
     * @var array
     */
    protected $_dep_elements = array();

    /**
     * Get template config entry for this element
     *
     * @return string
     */
    public function get_config_entry()
    {
        // Use the same template as of the ordinary checkbox
        return 'checkbox';
    }

    /**
     * Set dependent elements
     * 
     * @param  array $dep_elements
     * @return Form_Element_Checkbox_Enable
     */
    public function set_dep_elements(array $dep_elements)
    {
        $this->_dep_elements = $dep_elements;
        return $this;
    }

    /**
     * Get dependent elements
     * 
     * @return array
     */
    public function get_dep_elements()
    {
        return $this->_dep_elements;
    }

    /**
     * Initialize this form element
     */
    public function init()
    {        
        // Enable/disable dependent fields according to the value
        foreach ($this->dep_elements as $element_name)
        {
            $element = $this->form()->get_element($element_name);
            $element->ignored = ! ((boolean) $this->value);
        }

        parent::init();
    }

    /**
     * Render javascript for checkbox enabler
     *
     * @return string
     */
    public function render_js()
    {
        $js =
            "\ne = new jFormElementCheckboxEnable('" . $this->name . "', '" . $this->id . "');\n"
          . "e.set_dep_elements(['" . implode("','", $this->get_dep_elements()) . "']);\n";

        // Add validators
        foreach ($this->get_validators() as $validator)
        {
            $validator_js = $validator->render_js();
            if ($validator_js !== NULL)
            {
                $js .=
                    $validator_js
                  . "e.add_validator(v);\n";
            }
        }

        $js .=
            "f.add_element(e);\n";
        return $js;

    }

}