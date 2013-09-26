<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form element for entering prices
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Money extends Form_Element_Input
{
    /**
     * Sets the value for the element
     *
     * @param mixed $value
     * @return Form_Element
     */
    public function set_value($value)
    {
        if ($value instanceof Money)
        {
            // Obtain value from Money object
            $value = $value->amount;
        }

        return parent::set_value($value);
    }

    /**
     * Get value as Money object
     * 
     * @return Money
     */
    public function get_value()
    {
        $value = parent::get_value();

        $money = new Money();
        $money->amount = $value;
        return $money;
    }

    /**
     * Get raw value for validation
     * 
     * @return string
     */
    public function get_value_for_validation()
    {
        return parent::get_value();
    }

    /**
     * Use the same templates as input element
     *
     * @return string
     */
    public function  default_config_entry()
    {
        return 'input';
    }
    
    /**
     * Get element
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();

        // Add "integer" HTML class
        if (isset($attributes['class']))
        {
            $attributes['class'] .= ' integer';
        }
        else
        {
            $attributes['class'] = 'integer';
        }

        return $attributes;
    }

    /**
     * Append currency label to input field
     * 
     * @return string
     */
    public function default_append()
    {
        return '&nbsp;&nbsp;руб.';
    }
    
    /**
     * Renders input
     * By default, 'text' type is used
     *
     * @return string
     */
    public function render_input()
    {

        return Form_Helper::input($this->full_name, sprintf("%.2f", $this->value_for_validation), $this->attributes());
    }
}
