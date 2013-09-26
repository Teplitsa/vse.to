<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form input element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Input extends Form_Element {

    /**
     * Default type of input is "text"
     *
     * @return string
     */
    public function default_type()
    {
        return 'text';
    }

    /**
     * Renders input
     * By default, 'text' type is used
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::input($this->full_name, $this->value_for_render, $this->attributes());
    }

    public function render_alone_autoload()
    {
        return Form_Helper::autoload($this->id);
    }
    
    /**
     * Get input attributes
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

        // Add HTML class for form element (equal to "type")
        if (isset($attributes['class']))
        {
            $attributes['class'] .= ' ' . $this->type;
        }
        else
        {
            $attributes['class'] = $this->type;
        }
        return $attributes;
    }
}
