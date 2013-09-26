<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Display text message in form
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Text extends Form_Element
{

    /**
     * Creates form element
     *
     * @param string $name          Element name
     * @param string $value         Default value
     * @param array  $attributes    Attributes
     */
    public function __construct($name, array $properties = NULL, array $attributes = NULL)
    {
        parent::__construct($name, $properties, $attributes);

        // This element cannot have a value
        $this->without_value = 1;
    }

    /**
     * Renders text message in form
     *
     * @return string
     */
    public function render_input()
    {
        return $this->value;
    }
}
