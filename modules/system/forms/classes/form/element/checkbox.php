<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form input element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Checkbox extends Form_Element_Input {

    /**
     * Default type of input is "checkbox"
     *
     * @return string
     */
    public function get_type()
    {
        return 'checkbox';
    }

    /**
     * Renders checkbox
     *
     * @return string
     */
    public function render_input()
    {
        return
            Form_Helper::hidden($this->full_name, '0')
          . Form_Helper::checkbox($this->full_name, '1', (bool) $this->value, $this->attributes());
    }
}
