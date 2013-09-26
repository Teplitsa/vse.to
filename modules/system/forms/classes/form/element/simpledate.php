<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A date selection input
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_SimpleDate extends Form_Element_Input
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
     * Default date format string
     * Format is the same as for the strftime() function
     * 
     * @return string
     */
    public function default_format()
    {
        return Kohana::config('datetime.date_format');
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
     * Format of the value to be returned by get_value() function
     *  'date'      - string representation in 'Y-m-d' format
     *  'timestamp' - unix timestamp (default)
     *
     * @return string
     */
    public function default_value_format()
    {
        return 'timestamp';
    }

    /**
     * Set value from model
     * 
     * @param  mixed $value
     * @return Form_Element_SimpleDate
     */
    public function set_value($value)
    {
        if ($value instanceof DateTime)
        {
            $value = $value->format('Y-m-d');
        }
        
        if ($this->value_format == 'timestamp')
        {
            $this->_value = l10n::date($this->format, $value);
        }
        else // == 'date'
        {
            if ($value == '')
            {
                $this->_value = '';
            }
            else
            {
                    $this->_value = l10n::datetime_convert($value, 'Y-m-d', $this->format);
            }
        }

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
            // Allow not to specify dates
            return $value;
        }
        
        if ($this->value_format == 'timestamp')
        {
            return l10n::timestamp($this->format, $value, 0, 0, 0);
        }
        else
        {
            return l10n::datetime_convert($value, $this->format, 'Y-m-d');
        }
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
