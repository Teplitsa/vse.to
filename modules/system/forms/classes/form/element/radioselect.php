<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form select element consisting of radios
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_RadioSelect extends Form_Element {

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
     * @return string
     */
    public function get_type()
    {
        return 'radioselect';
    }

    /**
     * Use the same templates as checkselect element
     *
     * @return string
     */
    public function  default_config_entry()
    {
        return 'checkselect';
    }

    /**
     * Renders select element, constiting of checkboxes
     *
     * @return string
     */
    public function render_input()
    {
        $value  = $this->value;

        $html = '';

        foreach ($this->_options as $option => $label)
        {
            $name = "$this->full_name";

            if ($value == $option) {
                $checked = TRUE;
            } else {
                $checked = FALSE;
            }

            $option_template = $this->get_template('option');

            if ( ! $this->disabled)
            {
                $checkbox =
                    Form_Helper::radio($name, $option, $checked, array('class' => 'checkbox'));
            }
            else
            {
                $checkbox =
                    Form_Helper::radio($name, $option, $checked, array('class' => 'checkbox', 'disabled' => 'disabled'));
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
