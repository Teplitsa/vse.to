<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A simple date selection input
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_DateSimple extends Form_Element_Input
{
    /**
     * Use the same templates as the input element
     *
     * @return string
     */
    public function  default_config_entry()
    {
        return 'input';
    }

    /**
     * Default date display format string
     * (syntax is like for the date() function)
     * 
     * @return string
     */
    public function default_format()
    {
        return Kohana::config('date.date_format');
    }

    /**
     * Render date format as default comment
     *
     * @return string
     */
    public function default_comment()
    {
        return l10n::translate_datetime_format($this->format);
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
            $attributes['class'] .= ' date';
        }
        else
        {
            $attributes['class'] = 'date';
        }

        return $attributes;
    }

    /**
     * Set value from model
     * 
     * @param  DateTime $value
     * @return Form_Element_DateSimple
     */
    public function set_value($value)
    {
        if ($value instanceof DateTime)
        {
            $value = $value->format($this->format);
        }
        $this->_value = $value;
        return $this;
    }

    /**
     * Get date value
     * 
     * @return string|integer
     */
    public function get_value()
    {
        $value = parent::get_value();

        if ($value === '')
        {
            // empty date
            return new DateTime('0000-00-00 00:00:00');
        }
        
        // convert to format that DateTime::__construct() understands
        $value = l10n::datetime_convert($value, $this->format, 'Y-m-d H:i:s');
        if ($value === FALSE)
        {
            // Convertion failed - return empty date
            return new DateTime('0000-00-00 00:00:00');
        }

        return new DateTime($value);
    }

    /**
     * @return string
     */
    public function get_value_for_validation()
    {
        return parent::get_value();
    }

    /**
     * @return string
     */
    public function get_value_for_render()
    {
        return parent::get_value();
    }
}
