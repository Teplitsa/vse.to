<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form element for floating point numbers
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Float extends Form_Element_Input
{
    /**
     * Return correct floating-point value
     * regardless of whether point or comma is used for decimal separation
     *
     * @return float
     */
    public function get_value()
    {
        $value = parent::get_value();

        if ($value !== NULL && $value !== FALSE)
        {
            $value = l10n::string_to_float($value);
        }
        return $value;
    }

    public function get_value_for_render()
    {
        return parent::get_value();
    }

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
            $attributes['class'] .= ' float';
        }
        else
        {
            $attributes['class'] = 'float';
        }

        return $attributes;
    }
}
