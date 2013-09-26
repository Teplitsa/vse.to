<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form select element displayed as a grid of checkboxes
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_CheckGrid extends Form_Element {

    /**
     * Options for horizontal iteration
     * @var array
     */
    protected $_x_options = array();
    
    /**
     * Options for veritcal iteration
     * @var array
     */
    protected $_y_options = array();

    /**
     * Creates form element
     *
     * @param string $name          Element name
     * @param array  $options       Select options
     * @param array  $properties
     * @param array  $attributes
     */
    public function __construct($name, array $x_options = NULL, array $y_options = NULL, array $properties = NULL, array $attributes = NULL)
    {
        parent::__construct($name, $properties, $attributes);
        
        $this->_x_options = $x_options;
        $this->_y_options = $y_options;
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

        // Grid header
        $html = '<table class="light_table"><tr class="table_header"><th></th>';

        foreach ($this->_x_options as $i => $label)
        {
            // Pattern to match checkboxes in a column (for "toggle_all" checkbox)
            $pattern = $this->full_name . "[*][$i]";

            $html .=
                '<th><label style="white-space: nowrap;">'
              .     Form_Helper::checkbox('toggle_all-' . $pattern, '1', FALSE, array('class' => 'checkbox toggle_all'))
              .     '&nbsp;'
              .     $label
              . '</label></th>';
        }
        $html .= '</tr>';

        // Grid elements
        foreach ($this->_y_options as $j => $label_y)
        {
            $html .= '<tr><td>' . $label_y . '</td>';

            foreach ($this->_x_options as $i => $label_x)            
            {
                if (isset($value[$j][$i]) && (int) $value[$j][$i] > 0)
                {
                    $checked = TRUE;
                }
                else
                {
                    $checked = FALSE;
                }

                $name = $this->full_name . "[$j][$i]";
                $checkbox =
                    Form_Helper::hidden($name, '0')
                  . Form_Helper::checkbox($name, '1', $checked, array('class' => 'checkbox'));

                $html .= '<td class="c">' . $checkbox . '</td>';
            }

            $html .= '</tr>';
        }
        $html .= '</table>';

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
