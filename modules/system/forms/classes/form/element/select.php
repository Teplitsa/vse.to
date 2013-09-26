<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form select element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Select extends Form_Element {

    /**
     * Select options
     * @var array
     */
    protected $_options = array();

    /**
     * Creates form select element
     *
     * @param string $name          Element name
     * @param array  $options       Select options
     * @param array  $properties    Properties
     * @param array  $attributes    HTML attrinutes
     */
    public function __construct($name, array $options, array $properties = NULL, array $attributes = NULL)
    {
        parent::__construct($name, $properties, $attributes);
        
        $this->_options = $options;
    }

    /**
     * Set up options for select
     *
     * @param array $options
     * @return Form_Element_Select
     */
    public function set_options(array $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Renders select
     * By default, 'text' type is used
     *
     * @return string
     */
    public function render_input()
    {
        return Form_Helper::select($this->full_name, $this->_options, $this->value, $this->attributes());
    }
}
