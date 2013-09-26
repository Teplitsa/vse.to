<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Simple template helper
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Template
{
    /**
     * Check if the template contains the specified macros (or any macros at all, if $macro is NULL)
     *
     * @param  string $template
     * @param  string $macro
     * @return boolean
     */
    public static function has_macro($template, $macro = NULL)
    {
        if ($macro !== NULL)
        {
            self::_make_macro_cb($macro);
        }
        else
        {
            $macro = '{{';
        }

        return (strpos($template, $macro) !== FALSE);
    }

    /**
     * Replace macroses in template (recursively)
     * 
     * @param string $template
     * @param array|string $values
     * @param array|string $values2
     */
    public static function replace(& $template, $values, $values2 = NULL)
    {
        if (is_array($values))
        {
            $values = array_filter($values, array('Template', '_prepare_value_cb'));

            $macroses = array_keys($values);
            $values   = array_values($values);
            
            $macroses = array_map(array('Template', '_make_macro_cb'), $macroses);
        }
        else
        {
            $macroses = $values;
            $values   = $values2;

            $macroses = self::_make_macro_cb($macroses);
        }

        $count = 0;
        $loop_prevention = 0;

        do {
            $count = 0;
            if (self::has_macro($template))
            {
                $template = str_replace($macroses, $values, $template, $count);
            }

            $loop_prevention++;
        }
        while ($count > 0 && $loop_prevention < 100);

        if ($loop_prevention >= 100)
        {
            throw new Exception('Endless loop in ' . __FUNCTION__ . '!');
        }
    }

    /**
     * Replace macroses in template and return the result
     * 
     * @param  string $template
     * @param  array|string $values
     * @param  array|string $values2
     * @return string
     */
    public static function replace_ret($template, $values, $values2 = NULL)
    {
        $result = $template;
        self::replace($result, $values, $values2);
        return $result;
    }


    protected static function _prepare_value_cb($value)
    {
        return ( ! is_array($value));
    }

    protected static function _make_macro_cb($value)
    {
        return '{{'.$value.'}}';
    }

}
