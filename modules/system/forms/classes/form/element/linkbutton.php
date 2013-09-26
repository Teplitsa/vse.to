<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Link, rendered as button
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_LinkButton extends Form_Element
{
    /**
     * Use the same entry as the submit button
     * 
     * @return string
     */
    public function  default_config_entry()
    {
        return 'submit';
    }

    /**
     * This element cannot have value
     * 
     * @return boolean
     */
    public function get_without_value()
    {
        return TRUE;
    }

    /**
     * Renders link like button
     * $this->_value is used as url
     *
     * @return string
     */
    public function render_input()
    {
        $attributes = $this->attributes();

        // Add "button" class
        if (isset($attributes['class']))
        {
            $attributes['class'] .= ' button_adv';
        }
        else
        {
            $attributes['class'] = 'button_adv';
        }

        return '<a href="' . $this->url . '" class="' . $attributes['class'] . '"><span class="icon">' . $this->label . '</span></a>';
    }
}
