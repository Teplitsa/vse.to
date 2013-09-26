<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form submit element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Frontend_Element_Submit extends Form_Element_Submit
{
    /**
     * Use the same templates as the standart submit element
     *
     * @return string
     */
    public function  default_config_entry()
    {
        return 'submit';
    }
    
    /**
     * Renders submit element
     *
     * @return string
     */
    public function render_input()
    {
        $attributes = $this->attributes();
        if (isset($attributes['class']))
        {
            $class = 'button_adv ' . $attributes['class'];
        }
        else
        {
            $class = 'button_adv';
        }

        //
        $attributes['class'] = 'icon';

        return
            '<span class="' . $class . '">'
          .     Form_Helper::input($this->full_name, $this->label, $attributes)
          . '</span>';
    }
}
