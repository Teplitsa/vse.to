<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form input element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Hidden extends Form_Element
{

    /**
     * Template for hidden element
     *
     * @param string $type
     */
    public function default_template($type = 'element')
    {
        switch ($type)
        {
            case 'element':
                return
                    '{{input}}';

            default:
                return parent::default_template($type);
        }
    }

    /**
     * Default type of input is "hidden"
     *
     * @return string
     */
    public function default_type()
    {
        return 'hidden';
    }

    /**
     * Renders hidden element
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::hidden($this->full_name, $this->value, $this->attributes());
    }

    /**
     * Get hidden attributes
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();

        // Set element type
        if ( ! isset($attributes['type']))
        {
            $attributes['type'] = $this->type;
        }

        return $attributes;
    }
}
