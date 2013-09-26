<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form element for itegers
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Integer extends Form_Element_Input
{
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
}
