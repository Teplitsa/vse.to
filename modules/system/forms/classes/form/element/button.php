<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form button element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Button extends Form_Element {

    /**
     * Use the same entry as the submit button
     *
     * @return string
     */
    public function  default_config_entry()
    {
        return 'submit';
    }

    /**
     * This component cannot have value
     * 
     * @return boolean
     */
    public function get_without_value()
    {
        return TRUE;
    }

    /**
     * Render button
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::button($this->full_name, $this->label, $this->attributes());
    }

    /**
     * Get button attributes
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();

        // Set element type
        if ( ! isset($attributes['type']))
        {
            $attributes['type'] = $this->type;
        }

        // Add HTML class for form button
        if (isset($attributes['class']))
        {
            $attributes['class'] .= ' button ' . $this->type;
        }
        else
        {
            $attributes['class'] = 'button ' . $this->type;
        }

        return $attributes;
    }
}
