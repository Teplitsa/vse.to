<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract form element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Form_Element extends FormComponent
{

    /**
     * Value
     * @var mixed
     */
    protected $_value;

    /**
     * Default value
     * @var mixed
     */
    protected $_default_value;


    /**
     * Validators for element
     * @var array of Validator
     */
    protected $_validators = array();

    /**
     * Filters for element
     * @var array of Filter
     */
    protected $_filters = array();


    // -------------------------------------------------------------------------
    // Parent-child relationships
    // -------------------------------------------------------------------------
    /**
     * Set/get form for this element
     * 
     * @param  Form $form
     * @return Form
     */
    public function form(Form $form = NULL)
    {
        if ($form !== NULL)
        {
            $form->add_element($this);
        }

        return parent::form($form);
    }

    // -------------------------------------------------------------------------
    // Properties & attributes
    // -------------------------------------------------------------------------

    /**
     * Full name of the element - $form_name[$element_name] - to place in html code
     * 
     * @return string
     */
    public function default_full_name()
    {
        $parts = explode('[', $this->name, 2);
        $full_name = $this->form()->name . '[' . $parts[0] . ']';
        if (isset($parts[1]))
        {
            $full_name .= '[' . $parts[1];
        }
        return $full_name;
    }

    /**
     * Default label for element
     * 
     * @return string
     */
    public function default_label()
    {
        return $this->name;
    }

    /**
     * Default caption for element
     *
     * @return string
     */
    public function default_caption()
    {
        return $this->label;
    }

    /**
     * Disable/enable component
     * 
     * @param  boolean $disabled
     * @return FormComponent
     */
    public function set_disabled($disabled)
    {
        $this->_properties['disabled'] = $disabled;
        
        if ($disabled)
        {
            $this->_attributes['disabled'] = 'disabled';
        }
        else
        {
            unset($this->_attributes['disabled']);
        }
        
        return $this;
    }

    /**
     * Set autocomplete url for this element
     *
     * @param  string $autocomplete_url
     * @return Form_Element
     */
    public function set_autocomplete_url($autocomplete_url)
    {
        if ($autocomplete_url === NULL)
        {
            unset($this->_attributes['autocomplete']);
        }
        else
        {
            // Disable native autocomplete
            $this->_attributes['autocomplete'] = 'off';
        }

        $this->_properties['autocomplete_url'] = $autocomplete_url;
        return $this;
    }


    /**
     * Set autocomplete chunk for this element
     *
     * @param  char $autocomplete_chunk
     * @return Form_Element
     */
    public function set_autocomplete_chunk($autocomplete_chunk)
    {
        $this->_properties['autocomplete_chunk'] = $autocomplete_chunk;
        return $this;
    }
    
    /**
     * Get HTML class with element statuses (.disabled, .invalid, .valid, ...)
     * 
     * @return string
     */
    public function get_status_class()
    {
        $status_class = '';
        // Highlight input if there are errors
        if ($this->has_errors())
        {
            $status_class .= ' invalid';
        }

        // Add "disabled" class if element is disabled
        if ($this->disabled)
        {
            $status_class .= ' disabled';
        }

        return trim($status_class);
    }
    
    /**
     * Get element attributes
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();

        // Append status class
        $status_class = $this->status_class;
        if ($status_class != '')
        {
            if (isset($attributes['class']))
            {
                $attributes['class'] .= ' ' . $status_class;
            }
            else
            {
                $attributes['class'] = $status_class;
            }
        }

        return $attributes;
    }

    // -------------------------------------------------------------------------
    // Values
    // -------------------------------------------------------------------------
    /**
     * Sets the value for the element using filters
     *
     * @param mixed $value
     * @return Form_Element
     */
    public function set_value_filtered($value)
    {
        if ($value === NULL)
        {
            $this->_value = FALSE;
        }
        else
        {
            // Filter value
            foreach ($this->_filters as $filter)
            {
                $value = $filter->filter($value);
            }
            $this->_value = $value;
        }
        
        return $this;
    }

    /**
     * Set value from POST data
     * 
     * @param  string $value
     * @return Form_Element
     */
    public function set_value_from_post($value)
    {
        return $this->set_value_filtered($value);
    }

    /**
     * Set value "as-is", without using element filters
     * 
     * @param  mixed $value
     * @return Form_Element
     */
    public function set_value($value)
    {
        if ($value === NULL)
        {
            $this->_value = FALSE;
        }
        else
        {
            $this->_value = $value;
        }
        
        return $this;
    }

    /**
     * Gets element value
     *
     * @return mixed
     */
    public function get_value()
    {
        if ($this->_value !== NULL)
            return $this->_value;
        
        // Value was not yet set (lazy getting)
        // Try to get value from POST
        if ( ! $this->disabled && ! $this->ignored)
        {
            $value = $this->form()->get_post_data($this->name);
            
            if ($value !== NULL)
            {
                $this->set_value_from_post($value);
                return $this->_value;
            }
        }
        // Try to get value from the form's default source (such as associated model)
        $value = $this->form()->get_data($this->name);
        
        if ($value !== NULL)
        {
            $this->set_value($value);
            return $this->_value;
        }

        // Default value
        $this->set_value($this->default_value);
        
        return $this->_value;
    }

    /**
     * Get value to use in this element's validation
     * 
     * @return mixed
     */
    public function get_value_for_validation()
    {
        return $this->get_value();
    }

    /**
     * Get value to use when rendering the element
     *
     * @return mixed
     */
    public function get_value_for_render()
    {
        return $this->get_value();
    }

    /**
     * Set default value (without filters)
     * 
     * @param mixed $value
     */
    public function set_default_value($value)
    {
        $this->_default_value = $value;
    }

    /**
     * Get default value
     * 
     * @return mixed
     */
    public function get_default_value()
    {
        return $this->_default_value;
    }

    // -------------------------------------------------------------------------
    // Filters and validators
    // -------------------------------------------------------------------------
    /**
     * Adds filter to this element
     *
     * @param  Form_Filter $filter
     * @return Form_Element
     */
    public function add_filter(Form_Filter $filter)
    {
        $filter->set_form_element($this);

        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Get form element filters
     * 
     * @return array array(Form_Filter)
     */
    public function get_filters()
    {
        return $this->_filters;
    }

    /**
     * Adds validator to this element
     *
     * @param  Form_Validator $validator
     * @return Form_Element
     */
    public function add_validator(Form_Validator $validator)
    {
        $validator->set_form_element($this);
        
        $this->_validators[] = $validator;
        return $this;
    }

    /**
     * Gets validator by class
     *
     * @param string $class Validator class
     * @return Validator    on success
     * @return NULL         on failure
     */
    public function get_validator($class)
    {
        foreach ($this->_validators as $validator)
        {
            if (get_class($validator) === $class)
            {
                return $validator;
            }
        }

        return NULL;
    }

    /**
     * Get all validators for this element
     *
     * @return array array(Validator)
     */
    public function get_validators()
    {
        return $this->_validators;
    }

    /**
     * Validates element value.
     * Executes the chain of elemnt validators
     *
     * @param array $context  Form data
     * @return boolean
     */
    public function validate(array $context = NULL)
    {
        $result = TRUE;

        foreach ($this->_validators as $validator)
        {
            if ( ! $validator->validate($this->value_for_validation, $context))
            {
                $result = FALSE;
                $this->errors($validator->get_errors());

                if ($validator->breaks_chain())
                {
                    break;
                }
            }
        }

        return $result;
    }
    
    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------
    /**
     * Get default config entry name for this element
     * 
     * @return string
     */
    public function default_config_entry()
    { 
        return substr(strtolower(get_class($this)), strlen('Form_Element_'));
    }

    // -------------------------------------------------------------------------
    // Rendering
    // -------------------------------------------------------------------------
    /**
     * Renders the whole element
     */
    public function render()
    {
        $template = $this->get_template('element');
        
        // Input
        Template::replace($template, 'input', $this->render_input());
        
        if (Template::has_macro($template, 'label'))
        {
            // Label
            Template::replace($template, 'label', $this->render_label());
        }
        
        if (Template::has_macro($template, 'comment'))
        {
            // Comment
            Template::replace($template, 'comment', $this->render_comment());
        }

        if (Template::has_macro($template, 'errors'))
        {
            // Errors
            Template::replace($template, 'errors', $this->render_errors());
        }

        // Prepend
        Template::replace($template, 'prepend', '<span class="prepend">' . $this->prepend . '</span>');
        
        // Append
        Template::replace($template, 'append', '<span class="append">' . $this->append . '</span>');

        // Element id
        Template::replace($template, 'id', $this->id);

        // Status class
        Template::replace($template, 'status_class', $this->status_class);
        
        // Element name
        Template::replace($template, 'name', $this->name);

        // Element type
        Template::replace($template, 'type', $this->type);

        // Label text
        Template::replace($template, 'label_text', $this->label);

        return $template;
    }

    /**
     * Renders input for this element
     *
     * @return string
     */
    abstract public function render_input();

    /**
     * Renders label for element
     *
     * @return string
     */
    public function render_label()
    {
        $attributes = $this->attributes();
        
        $label_attributes['required'] = $this->required;
        
        if ( isset($attributes['label_class']))
        {
            $label_attributes['class'] = $attributes['label_class'];
        }
        
        if ($this->label !== NULL)
        {
            return Form_Helper::label($this->id, $this->label, $label_attributes);
        }
        else
        {
            return '';
        }
    }

    /**
     * Renders comment for element
     *
     * @return string
     */
    public function render_comment()
    {
        if ($this->comment !== NULL)
        {
            return $this->comment;
        }
        else
        {
            return '';
        }
    }
   

    /**
     * Renders element errors
     *
     * @return string
     */
    public function render_errors()
    {
        $html = '';

        foreach ($this->errors() as $error)
        {
            $html .= '<div>' . HTML::chars($error['text']) . '</div>';
        }

        return $html;
    }

    public function render_alone_errors()
    {
        $html = '';

        foreach ($this->errors() as $error)
        {
            $html .= '<div>' . HTML::chars($error['text']) . '</div>';
        }

        return '<div class="errors" id="'.$this->id.'-errors">'.$html.'</div>';
    }
    
    
    

    /**
     * Render javascript for this element
     * 
     * @return string
     */
    public function render_js()
    {
        $js =
            "\ne = new jFormElement('" . $this->name . "', '" . $this->id . "');\n";

        if ($this->disabled || $this->ignored)
        {
            $js .=
                "e.disabled = true;";
        }

        // Add validators
        foreach ($this->get_validators() as $validator)
        {
            $validator_js = $validator->render_js();
            if ($validator_js !== NULL)
            {
                $js .=
                    $validator_js 
                  . "e.add_validator(v);\n";
            }
        }

        // Autocomplete
        if ($this->autocomplete_url)
        {
            $js .= "e.autocomplete_url = '" . $this->autocomplete_url . "';\n";
        }

        if ($this->autocomplete_chunk)
        {
            $js .= "e.autocomplete_chunk = '" . $this->autocomplete_chunk . "';\n";
        }

        $js .=
            "f.add_element(e);\n";
        return $js;
    }
}
