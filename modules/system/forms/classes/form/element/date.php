<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form date element.
 * Rendered as three input elements for day, month and year
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Date extends Form_Element_Input
{
    public function default_format()
    {
        return Kohana::config('date.date_format');
    }    
    /**
     * Sets date value either from string or from array
     * String format should be yyyy-mm-dd
     * Array should contain three elements: day, month and year
     *
     * @param array | string $value
     * @return Form_Element_Date
     */
    function set_value($value)
    {
        if (is_int($value) || ctype_digit($value))
        {
            //Unix timestamp
            $value = (int) $value;

            $day   = date('d', $value);
            $month = date('m', $value);
            $year  = date('Y', $value);
        }
        elseif (is_array($value))
        {
            // Expecting array (day, month, year)
            $day   = isset($value['day'])   ? $value['day']   : '00';
            $month = isset($value['month']) ? $value['month'] : '00';
            $year  = isset($value['year'])  ? $value['year']  : '0000';
        }
        else
        {
            // Expecting string in yyyy-mm-dd format
            $value = explode('-', (string)$value);

            $day   = isset($value[2]) ? $value[2] : '00';
            $month = isset($value[1]) ? $value[1] : '00';
            $year  = isset($value[0]) ? $value[0] : '0000';
        }

        $value = array(
            'day'   => $day,
            'month' => $month,
            'year'  => $year
        );

        parent::set_value($value);
    }

    /**
     * Get date value
     *
     * @return string Date in yyyy-mm-dd format
     */
    function get_value()
    {
        return "$this->day-$this->month-$this->year";
    }

    /**
     * Returns day
     *
     * @return string
     */
    function get_day()
    {
        $value = parent::get_value();
        return $value['day'];
    }

    /**
     * Returns month
     *
     * @return string
     */
    function get_month()
    {
        $value = parent::get_value();
        return $value['month'];
    }

    /**
     * Returns year
     *
     * @return string
     */
    function get_year()
    {
        $value = parent::get_value();
        return $value['year'];
    }

    /**
     * @return string
     */
    public function default_comment()
    {
        return 'Формат: ДД-ММ-ГГГГ';
    }

    /**
     * Renders date element as three text inputs for day, month and year
     *
     * @return string
     */
    public function render_input()
    {
        $attributes = $this->attributes();

        $html = '';
        
        // Day
        $attrs = $attributes;
        $attrs['id'] .= '-day';
        $attrs['class'] .= ' day';
        $attrs['maxlength'] = 2;
        $html .= Form_Helper::input($this->full_name.'[day]', $this->day, $attrs);

        $html .= '&nbsp;-&nbsp;';

        // Month
        $attrs = $attributes;
        $attrs['id'] .= '-month';
        $attrs['class'] .= ' month';
        $attrs['maxlength'] = 2;
        $html .= Form_Helper::input($this->full_name.'[month]', $this->month, $attrs);

        $html .= '&nbsp;-&nbsp;';
        
        // Year
        $attrs = $attributes;
        $attrs['id'] .= '-year';
        $attrs['class'] .= ' year';
        $attrs['maxlength'] = 4;
        $html .= Form_Helper::input($this->full_name.'[year]', $this->year, $attrs);

        return $html;
    }
}
