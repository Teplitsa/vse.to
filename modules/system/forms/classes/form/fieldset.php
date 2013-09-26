<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Fieldset of form elements
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Fieldset extends FormComponent
{

    // -------------------------------------------------------------------------
    // Properties and attributes
    // -------------------------------------------------------------------------
    /**
     * Type is "fieldset"
     *
     * @return string
     */
    public function default_type()
    {
        return 'fieldset';
    }

    /**
     * Fieldset html attribuges
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();
        // Fieldset has no name
        unset($attributes['name']);

        // Add HTML class for form element (equal to "type")
        if (isset($attributes['class']))
        {
            $attributes['class'] .= ' ' . $this->type;
        }
        else
        {
            $attributes['class'] = $this->type;
        }
        return $attributes;

        return $attributes;
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------
    /**
     * Get default config entry name for this fieldset
     *
     * @return string
     */
    public function default_config_entry()
    {
        return substr(strtolower(get_class($this)), strlen('Form_'));
    }

    /**
     * Sets fieldset template
     *
     * @param string $template
     * @param string $type
     * @return Form_Fieldset
     */
    public function set_template($template, $type = 'fieldset')
    {
        $this->_templates[$type] = $template;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Rendering
    // -------------------------------------------------------------------------
    /**
     * Renders all elements in fieldset
     *
     * @return string
     */
    public function render_components()
    {
        $html = '';

        $template = $this->get_template('element');

        foreach ($this->_components as $component)
        {
            if ($component->render)
            {
                $html .= Template::replace_ret($template, 'element', $component->render());
            }
        }

        return $html;
    }

    /**
     * Renders all elements in fieldset
     *
     * @return string
     */
    public function render()
    {
        if ( ! Request::$is_ajax)
        {
            $template = $this->get_template('fieldset');
        }
        else
        {
            $template = $this->get_template('fieldset_ajax');
        }

        // Fieldset elements
        Template::replace($template, 'elements', $this->render_components());

        if (Template::has_macro($template, 'label'))
        {
            // Fieldset label
            Template::replace($template, 'label', $this->render_label());
        }
        // label text
        Template::replace($template, 'label_text', $this->label);

        // Fieldset id
        Template::replace($template, 'id', $this->id);

        return $template;
    }

    /**
     * Renders label for fieldset
     *
     * @return string
     */
    public function render_label()
    {
        if ($this->label !== NULL)
        {
            return Form_Helper::label(NULL, $this->label, array('required' => $this->required));
        }
        else
        {
            return '';
        }
    }
}