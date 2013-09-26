<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Basic class for all form components (form itself, elements, fieldsets, ...)
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class FormComponent {

    /**
     * Owner of this component(Form, Form_Fieldset, ...)
     * @var FormComponent
     */
    protected $_owner;

    /**
     * Form that this component belongs to
     * @var Form
     */
    protected $_form;

    /**
     * Child components
     * @var array array(FormComponent)
     */
    protected $_components = array();


    /**
     * Component properties
     * @var array
     */
    protected $_properties = array();

    /**
     * HTML attributes
     * @var array
     */
    protected $_attributes = array();


    /**
     * Templates
     * @var array
     */
    protected $_templates = array();


    /**
     * Proxy errors to this element
     * @var FormComponent
     */
    protected $_errors_target;

    /**
     * Errors, warining & other messages in this component
     * @var array
     */
    protected $_messages = array();

    /**
     * Component constructor
     *
     * @param string $name
     * @param array  $properties Component properties
     * @param array  $attributes Component HTML attributes(such as "class", "maxlength", ...)
     */
    public function  __construct($name, array $properties = NULL, array $attributes = NULL)
    {        
        if ($properties !== NULL)
        {
            foreach ($properties as $k => $v)
            {
                $this->$k = $v;
            }
        }

        if ($attributes !== NULL)
        {
            foreach ($attributes as $k => $v)
            {
                $this->attribute($k, $v);
            }
        }
        
        $this->name = $name;
    }

    /**
     * Initialize the component (originally called from form constructor)
     */
    public function init()
    {
    }

    /**
     * Initialize child components in post init
     * (originally called from form constructor)
     */
    public function post_init()
    {
        // Initialize child components
        foreach ($this->_components as $component)
        {
            $component->init();
            $component->post_init();
        }
    }
    // -------------------------------------------------------------------------
    // Parent-child relations
    // -------------------------------------------------------------------------
    /**
     * Set/get the owner of this component
     *
     * @param  FormComponent $owner
     * @return FormComponent
     */
    public function owner(FormComponent $owner = NULL)
    {
        if ($owner !== NULL)
        {
            $this->_owner = $owner;
        }
        return $this->_owner;
    }

    /**
     * Set/get the form this component belongs to
     *
     * @param  Form $form
     * @return Form
     */
    public function form(Form $form = NULL)
    {
        if ($this instanceof Form)
        {
            // This component is already a form
            return $this;
        }

        if ($form !== NULL)
        {
            // Change form for the component itself
            $this->_form = $form;

            // and for all child components
            foreach ($this->get_components() as $component)
            {
                $component->form($form);
            }
        }

        return $this->_form;
    }

    // -------------------------------------------------------------------------
    // Child components
    // -------------------------------------------------------------------------
    /**
     * Add a child component
     *
     * @param  FormComponent $component
     * @return FormComponent
     */
    public function add_component(FormComponent $component)
    {
        if (isset($this->_components[$component->name]))
        {
            throw new Kohana_Exception('Child component with name ":name" already exists in :component',
                array(':name' => $component->name, ':component' => get_class($this)));
        }

        // Set this as the owner for the new child component
        $component->owner($this);
        
        // Update the form for the new child component and all its sub-components
        $component->form($this->form());
        
        $this->_components[$component->name] = $component;
        return $this;
    }

    /**
     * Get child component by name
     * 
     * @param  string $name
     * @return FormComponent
     */
    public function get_component($name)
    {
        if (isset($this->_components[$name]))
        {
            return $this->_components[$name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Does this component have child component with given name?
     *
     * @return boolean
     */
    public function has_component($name)
    {
        return isset($this->_components[$name]);
    }

    /**
     * Return array of all direct child components
     *
     * @return array array(FormComponent)
     */
    public function get_components()
    {
        return $this->_components;
    }

    /**
     * Find a component with given name in this component or any of its children
     * Returns FALSE if component is not found
     *
     * @param  string $name
     * @return FormComponent|FALSE
     */
    public function find_component($name)
    {
        if ($this->has_component($name))
        {
            return $this->_components[$name];
        }
        // Search in child components
        foreach ($this->get_components() as $child)
        {
            $component = $child->find_component($name);
            if ($component !== FALSE)
            {
                return $component;
            }
        }

        return FALSE;
    }

    
    // -------------------------------------------------------------------------
    // Properties & attributes
    // -------------------------------------------------------------------------
    /**
     * Component property setter
     * 
     * @param  string $name     Name of property to set
     * @param  mixed $value    Property value
     * @return FormComponent
     */
    public function __set($name, $value)
    {
        $setter = 'set_' . strtolower($name);
        if (method_exists($this, $setter))
        {
            $this->$setter($value);
        }
        else
        {
            $this->_properties[$name] = $value;
        }
        return $this;
    }

    /**
     * Component property getter
     *
     * @param  string $name Name of property to get
     * @return mixed
     */
    public function __get($name)
    {
        $name = strtolower($name);

        $getter    = "get_$name";
        $defaulter = "default_$name";
        
        if (method_exists($this, $getter))
        {
            return $this->$getter();
        }
        elseif (isset($this->_properties[$name]))
        {
            return $this->_properties[$name];
        }
        elseif (method_exists($this, $defaulter))
        {
            $this->_properties[$name] = $this->$defaulter();
            return $this->_properties[$name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Check that specified property is set
     *
     * @param  string $name
     * @return boolean
     */
    public function  __isset($name)
    {
        $name = strtolower($name);
        $getter     = "get_$name";
        $defaulter  = "default_$name";

        if (method_exists($this, $getter))
        {
            return ($this->$getter() !== NULL);
        }
        elseif (method_exists($this, $defaulter))
        {
            return ($this->$defaulter() !== NULL);
        }
        else
        {
            return isset($this->_properties[$name]);
        }
    }

    /**
     * Default value for component id
     *
     * @return string
     */
    public function default_id()
    {
        // Leave only valid characters for id
        $name = preg_replace('/[^\w]/i', '_', $this->name);

        if ($this instanceof Form)
        {
            return 'id-' . $name;
        }
        else
        {
            return 'id-' . $this->form()->name . '-' . $name;
        }
    }


    /**
     * Get/set HTML attribute value
     *
     * @param  string $name
     * @param  mixed $value
     * @return mixed
     */
    public function attribute($name, $value = NULL)
    {
        if ($value !== NULL)
        {
            $this->_attributes[$name] = $value;    
        }
        
        if (isset($this->_attributes[$name]))
        {
            return $this->_attributes[$name];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get all attributes in array
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = $this->_attributes;

        //@FIXME: 'name' is NOT a common HTML attribute for all components
        $attributes['name'] = $this->name;
        $attributes['id']   = $this->id;
        
        return $attributes;
    }

    /**
     * Components are rendered by default
     *
     * @return boolean
     */
    public function default_render()
    {
        return TRUE;
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------
    /**
     * Sets template
     *
     * @param string $template
     * @param string $type
     * @return Form_Element
     */
    public function set_template($template, $type = 'element')
    {
        $this->_templates[$type] = $template;
        return $this;
    }

    /**
     * Gets template
     *
     * @param string $type
     * @return array
     */
    public function get_template($type = 'element')
    {
        if (isset($this->_templates[$type]))
        {
            return $this->_templates[$type];
        }
        else
        {
            return $this->default_template($type);
        }
    }

    /**
     * Template set to use for rendering
     * 
     * @return string
     */
    public function default_template_set()
    {
        return $this->form()->template_set;
    }

    /**
     * Get default config entry name for this element
     *
     * @return string
     */
    public function default_config_entry()
    {
        return strtolower(get_class($this));
    }

    /**
     * Default value for form element layout
     *
     * @return string
     */
    public function default_layout()
    {
        return $this->form()->layout;
    }

    /**
     * Default template
     *
     * @param  string $type
     * @return array
     */
    public function default_template($type = 'element')
    {
        $config_entry = 'form_templates/' . $this->template_set . '/' . $this->config_entry;
        
        $template_set = Kohana::config("$config_entry");

        if ($template_set === NULL)
        {
            throw new Kohana_Exception('Unable to find template set in config ":entry" for element ":name" (:class)',
                array(':entry' => $config_entry, ':name' => $this->name, ':class' => get_class($this)));
        }
        
        if (empty($template_set))
        {
            throw new Kohana_Exception('Empty template set in config ":entry" for element ":name" (:class)',
                array(':entry' => $config_entry, ':name' => $this->name, ':class' => get_class($this)));
        }

        // Choose appropriate layout
        $layout = $this->layout;
        if (isset($template_set[$layout]))
        {
            // Specific layout is defined for this element
            $template_set = $template_set[$layout];
        }
        else
        {
            // Use default layout
            $template_set = $template_set[0];
            
        }

        if ( ! isset($template_set[$type]))
        {
            throw new Kohana_Exception('There is no template ":type" in templates for the element element ":name" (:class)',
                array(':type' => $type, ':name' => $this->name, ':class' => get_class($this)));
        }

        return $template_set[$type];
    }

    
    // -------------------------------------------------------------------------
    // Errors & messages
    // -------------------------------------------------------------------------
    /**
     * Get/set errors target
     *
     * @param object $target
     * @return FormComponent
     */
    public function errors_target($target = NULL)
    {
        if ($target !== NULL)
        {
            $this->_errors_target = $target;
        }
        return $this->_errors_target;
    }

    /**
     * Add a message (error, warning) to this form component
     *
     * @param string $text
     * @param string $type
     * @return FormComponent
     */
    public function message($text, $type = FlashMessages::MESSAGE)
    {
        $this->_messages[] = array(
            'text'  => $text,
            'type'  => $type
        );

        return $this;
    }

    /**
     * Adds an error message to this component (or proxy error to another one)
     *
     * @param string $text Error text
     * @return FormComponent
     */
    public function error($text)
    {
        if ($this->_errors_target !== NULL)
        {
            $this->_errors_target->error($text);
        }
        else
        {
            $this->message($text, FlashMessages::ERROR);
        }

        return $this;
    }

    /**
     * Get / set all component errors at once
     *
     * @param  array $errors
     * @return array
     */
    public function errors(array $errors = NULL)
    {
        if ($errors !== NULL)
        {
            $this->_messages += $errors;
        }
        
        $errors = array();
        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                $errors[] = $message;
            }
        }
        return $errors;
    }
    
    /**
     * Does this component have errors?
     *
     * @return boolean
     */
    public function has_errors()
    {
        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Does this components or any of its subcomponents have errors?
     *
     * @return boolean
     */
    public function contains_errors()
    {
        if ($this->has_errors())
        {
            // The component itself has errors
            return TRUE;
        }

        // Check the children for errors
        foreach ($this->get_components() as $component)
        {
            if ($component->contains_errors())
            {
                return TRUE;
            }
        }

        return FALSE;
    }
}