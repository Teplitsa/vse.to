<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form select element consisting of checkboxes
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_CheckSelect extends Form_Element {

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
     * @param array  $properties
     * @param array  $attributes
     */
    public function __construct($name, array $options = NULL, array $properties = NULL, array $attributes = NULL)
    {
        parent::__construct($name, $properties, $attributes);
        $this->_options = $options;
    }

    /**
     * Return input type
     * @return string
     */
    public function default_type()
    {
        return 'checkselect';
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
     * Renders select element, constiting of checkboxes
     *
     * @return string
     */
    public function render_input()
    {
        $value  = $this->value;

        if ( ! is_array($value)) {
            $value = array();
        }

        $html = '';

        foreach ($this->_options as $option => $label)
        {
            $name = "$this->full_name[$option]";

            if (isset($value[$option]))
            {
                $checked = (bool) $value[$option];
            }
            else
            {
                $checked = (bool) $this->default_selected;
            }

            if (is_array($label))
            {
                $label = $label['label'];
                $disabled = ! empty($label['disabled']);
            }
            else
            {
                $disabled = FALSE;
            }

            $option_template = $this->get_template('option');

            if ( ! $this->disabled && ! $disabled)
            {
                $checkbox =
                    Form_Helper::hidden($name, '0')
                  . Form_Helper::checkbox($name, '1', $checked, array('class' => 'checkbox'));
            }
            else
            {
                $checkbox =
                    Form_Helper::checkbox($name, '1', $checked, array('class' => 'checkbox', 'disabled' => 'disabled'));
            }

            Template::replace($option_template, array(
                'checkbox' => $checkbox,
                'label' => $label
            ));

            $html .= $option_template;
        }

        return $html;
    }

    /**
     * No javascript for this element
     *
     * @return string
     */
    public function render_js()
    {
        return FALSE;
    }
}
