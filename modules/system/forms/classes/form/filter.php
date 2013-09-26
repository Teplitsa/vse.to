<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract form filter
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Form_Filter
{
    /**
     * Form element to which this filter is attached
     * @var Form_Element
     */
    protected $_form_element;

    /**
     * Set form element
     *
     * @param Form_Element $form_element
     * @return Validator
     */
    public function set_form_element(Form_Element $form_element)
    {
        $this->_form_element = $form_element;
        return $this;
    }

    /**
     * Get form element
     *
     * @return Form_Element
     */
    public function get_form_element()
    {
        return $this->_form_element;
    }

    /**
     * Filter given value
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        return $value;
    }
}