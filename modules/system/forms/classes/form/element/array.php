<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form input element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Array extends Form_Element_Input {
    
    public function  default_config_entry()
    {
        return 'input';
    }    
    /**
     * Cut strings longer than this parameter
     * @var string
     */
    protected $_glue = ',';
    
    public function __construct($name, array $properties = NULL, array $attributes = NULL) {
        parent::__construct($name, $properties, $attributes);
        if (isset($attributes['glue'])) $this->_glue = $attributes['glue'];
    }
    /**
     * Gets element value
     *
     * @return mixed
     */
    public function get_value()
    {
        $return_vals =array();
        if ($this->_value !== NULL) {
            $vals = explode($this->_glue,$this->_value);
            foreach ($vals as $val) {
                $return_vals[] = trim($val);
            }
            return $return_vals;
        }
        
        // Value was not yet set (lazy getting)
        // Try to get value from POST
        if ( ! $this->disabled && ! $this->ignored)
        {
            $value = $this->form()->get_post_data($this->name);

            if ($value !== NULL)
            {
                $this->set_value_from_post($value);
                $vals = explode($this->_glue,$this->_value);
                foreach ($vals as $val) {
                    $return_vals[] = trim($val);
                }
                return $return_vals;
            }
        }
        // Try to get value from the form's default source (such as associated model)
        $vals = $this->form()->get_data($this->name);

        if ($vals !== NULL)
        {
            $this->set_value($vals);

            foreach ($vals as $val) {
                $return_vals[] = trim($val);
            }
            return $return_vals;
        }

        // Default value
        $this->set_value($this->default_value);
        
        $vals = explode($this->_glue,$this->_value);
        foreach ($vals as $val) {
            $return_vals[] = trim($val);
        }
        return $return_vals;
    }
    

    /**
     * Get value to use in this element's validation
     * 
     * @return mixed
     */
    public function get_value_for_validation()
    {
        return $this->get_value();
    }

    /**
     * Get value to use when rendering the element
     *
     * @return mixed
     */
    public function get_value_for_render()
    {
        $value = $this->get_value();
        return implode($this->_glue,$value);
    }
}
