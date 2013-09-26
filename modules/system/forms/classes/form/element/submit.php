<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form input element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Submit extends Form_Element_Input
{    
    /**
     * Default type of input is "submit"
     *
     * @return string
     */
    public function default_type()
    {
        return 'submit';
    }

    /**
     * Renders submit button
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::input($this->full_name, $this->label, $this->attributes());
    }

    /**
     * No js for submit buttons
     *
     * @return string
     */
    public function render_js()
    {
        return FALSE;
    }
}
