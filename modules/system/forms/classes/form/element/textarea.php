<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form textarea element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Textarea extends Form_Element {

    /**
     * Default type of input is "textarea"
     *
     * @return string
     */
    public function get_type()
    {
        return 'textarea';
    }

    /**
     * Default number of columns in textarea
     * 
     * @return integer
     */
    public function defaultattr_cols()
    {
        return 30;
    }

    /**
     * Default number of rows in textarea
     *
     * @return integer
     */
    public function defaultattr_rows()
    {
        return 5;
    }
    
    /**
     * Get HTML attributes for textarea.
     * Set default values for 'cols' and 'rows'
     * 
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();
        
        if ( ! isset($attributes['cols']))
        {
            $attributes['cols'] = $this->defaultattr_cols();
        }

        if ( ! isset($attributes['rows']))
        {
            $attributes['rows'] = $this->defaultattr_rows();
        }

        return $attributes;
    }

    /**
     * Renders textarea
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::textarea($this->full_name, $this->value, $this->attributes());
    }
}
