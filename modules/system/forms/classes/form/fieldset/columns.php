<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Render elements in columns
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Fieldset_Columns extends Form_Fieldset
{
    /**
     * Add a child component to the desired column
     *
     * @param  FormComponent $component
     * @param  integer $column
     * @return FormComponent
     */
    public function add_component(FormComponent $component, $column = 1)
    {
        parent::add_component($component);

        if ( ! isset($component->column))
        {
            $component->column = $column;
        }
        return $this;
    }

    // -------------------------------------------------------------------------
    // Rendering
    // -------------------------------------------------------------------------
    /**
     * Renders all elements in fieldset in columns
     *
     * @return string
     */
    public function render_components()
    {
        $html = '';
        $columns = array();

        // Render each column
        foreach ($this->get_components() as $component)
        {

            $column_i = (int) $component->column;
            if ( ! isset($columns[$column_i]))
            {
                $columns[$column_i] = '';
            }
            
            $columns[$column_i] .= $component->render();
        }

        // Join columns together        
        $template  = $this->get_template('column');
        
        $html = '';
        foreach ($columns as $i => $column)
        {
            $html .= Template::replace_ret($template, array(
                'elements' => $column,
                'last' => ($i >= count($columns)) ? 'last' : '',
                'class' => isset($this->column_classes[$i]) ? $this->column_classes[$i] : ''
            ));
        }

        return $html;
    }
}