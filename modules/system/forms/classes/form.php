<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Forms
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form extends FormComponent
{
    
    /**
     * Form elements
     * @var array array(Form_Element)
     */
    protected $_elements;

    /**
     * @var string
     */
    public $template_set = 'table';


    /**
     * Construct form, init form fields
     *
     * @param string $name
     */
    public function __construct($name = NULL)
    {
        parent::__construct($name);

        // Initialize form (setup form fields, ...)
        $this->init();

        // Post initialization
        $this->post_init();
    }

    /**
     * Default, autogenerated form name.
     *
     * [!] It's better not to rely on automatic generation, because it depends on
     * the order in which forms are rendered/created...
     * 
     * @return string
     */
    public function default_name()
    {
        static $uniq = 1;
        
        $name = trim(str_replace('form', '', strtolower(get_class($this))), '_');
        $name .= $uniq;
        $uniq++;
        return $name;
    }

    /**
     * Default action is current request uri
     *
     * @return string
     */
    public function default_action()
    {
        // Use current uri
        return Request::current()->uri;
    }

    // -------------------------------------------------------------------------
    // Initialization
    // -------------------------------------------------------------------------
    /**
     * Post init
     */
    public function post_init()
    {
        parent::post_init();

        // Add hidden input to hold the id of current tab if there are tabs in form
        $tabs = $this->get_tabs();
        if ( ! empty($tabs))
        {
            // Current tab
            $element = new Form_Element_Hidden('current_tab');
            $this->add_component($element);
        }
    }

    // -------------------------------------------------------------------------
    // Form elements
    // -------------------------------------------------------------------------
    public function add_element(Form_Element $element)
    {
        if (isset($this->_elements[$element->name]))
        {
            throw new Kohana_Exception('Form element with name ":name" already exists in form ":form"',
                array(':name' => $element->name, ':form' => get_class($this)));
        }
        
        $this->_elements[$element->name] = $element;
    }

    /**
     * Get all form elements that are children of this component and it's child components
     *
     * @return array array(FormElement)
     */
    public function get_elements()
    {
        return $this->_elements;
    }

    /**
     * Does this component have the specified element?
     *
     * @param string $name Element name
     * @return boolean
     */
    public function has_element($name)
    {
        return isset($this->_elements[$name]);
    }

    /**
     * Gets form element with specified name
     *
     * @param string $name Element name
     * @return Form_Element
     */
    public function get_element($name)
    {
        if (isset($this->_elements[$name]))
        {
            return $this->_elements[$name];
        }
        else
        {
            return NULL;
        }
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------
    /**
     * Default config entry name
     * 
     * @return string
     */
    public function default_config_entry()
    {
        return 'form';
    }
    
    /**
     * Default value for form elements layout
     *
     * @return string
     */
    public function default_layout()
    {
        return 'standart';
    }

    // -------------------------------------------------------------------------
    // Form data
    // -------------------------------------------------------------------------
    /**
     * Returns TRUE if this form was submitted in the request
     *
     * @return boolean
     */
    public function is_submitted()
    {
        return isset($_POST[$this->name]);
    }

    /**
     * Get value from form post data
     * 
     * @param  string $key
     * @return string
     */
    public function get_post_data($key)
    {
        if ( ! isset($_POST[$this->name]))
            return NULL;
        
        $data =& $_POST[$this->name];

        if (strpos($key, '[') !== FALSE)
        {
            // Form element name is a hash key
            $path = str_replace(array('[', ']'), array('.', ''), $key);
            $value = Arr::path($data, $path);
        }
        elseif (isset($data[$key]))
        {
            $value = $data[$key];
        }
        else
        {
            $value = NULL;
        }

        return $value;
    }

    /**
     * Get value from other possible data sources
     * 
     * @param string $key
     */
    public function get_data($key)
    {
        return NULL;
    }

    /**
     * Get Form values for all elements except ignored elements and elements with NULL values
     *
     * @return array
     */
    public function get_values()
    {
        $data = array();
        foreach ($this->get_elements() as $element)
        {
            // Skip ignored values and elements that can't have values by definition
            if ($element->ignored || $element->without_value)
                continue;

            $value = $element->value;
            
            // Skip NULL values
            if ($value === NULL)
                continue;

            if (strpos($element->name, '[') !== FALSE)
            {
                // Form element is a hash key
                $path = preg_split('/[\[\]]/', $element->name, -1, PREG_SPLIT_NO_EMPTY);
                $count = count($path);

                $arr =& $data;

                foreach ($path as $i => $key)
                {
                    if ($i < $count - 1)
                    {
                        if ( ! isset($arr[$key]))
                        {
                            $arr[$key] = array();
                        }
                        $arr =& $arr[$key];
                    }
                    else
                    {
                        $arr[$key] = $value;
                    }
                }
            }
            else
            {
                $data[$element->name] = $value;
            }
        }

        return $data;
    }

    /**
     * Get Form value for specified element.
     * If element is ignored - returns NULL
     *
     * @param string $name Form element name
     * @return mixed
     */
    public function get_value($name)
    {
        $element = $this->get_element($name);

        if ($element === NULL)
        {
            throw new Kohana_Exception('Element ":name" was not found in form ":form"',
                array(':name' => $name, ':form' => get_class($this))
            );
        }

        if ($element->ignored)
        {
            return NULL;
        }
        else
        {
            return $element->value;
        }
    }

    /**
     * Set default values for form elements
     *
     * @param array $values
     */
    public function set_defaults(array $values)
    {
        foreach ($this->get_elements() as $element)
        {
            $name = $element->name;
            
            if (strpos($name, '[') !== FALSE)
            {
                // Form element name is a hash key
                $path = str_replace(array('[', ']'), array('.', ''), $name);
                $value = Arr::path($values, $path);
            }
            elseif (isset($values[$name]))
            {
                $value = $values[$name];
            }
            else
            {
                $value = NULL;
            }

            if ($value !== NULL)
            {
                // Set default value
                $element->default_value = $value;
            }
        }
    }


    /**
     * Validate all elements in form, except disabled and ignored ones
     *
     * @return boolean
     */
    function validate()
    {        
        $result = TRUE;

        $context = $this->get_values();

        foreach ($this->_elements as $element)
        {
            if ($element->disabled || $element->ignored)
            {
                // Skip disabled and ignored elements
                continue;
            }

            if ( ! $element->validate($context))
            {
                $result = FALSE;
            }
        }

        // If validation failed - check if there are tabs in form and
        // make the tab with the error current
        foreach ($this->get_components() as $component)
        {
            if (    ($component instanceof Form_Fieldset_Tab)
                 && ($component->contains_errors()))
            {
                $this->get_element('current_tab')->value = $component->id;
            }
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Rendering
    // -------------------------------------------------------------------------
    /**
     * Renders form
     *
     * @return string Form html
     */
    public function render()
    {
        if ($this->view_script !== NULL)
        {
            // Render form using View
            $view = new View($this->view_script);
            
            $view->form = $this;
            
            $html = $view->render();
        }
        else
        {
            // Render form from template
            $html = $this->get_template('form');

            if (Template::has_macro($html, 'messages'))
            {
                // Form messages & errors
                Template::replace($html, 'messages', $this->render_messages());
            }

            // Form opening tag
            Template::replace($html, 'form_open', $this->render_form_open());

            // Hidden elements
            Template::replace($html, 'hidden', $this->render_hidden());

            // Form elements
            Template::replace($html, 'elements', $this->render_components());

            // Form closing tag
            Template::replace($html, 'form_close', $this->render_form_close());
        }

        // Render form javascripts
        $this->render_js();

        return $html;
    }

    /**
     * Render form open tag
     * 
     * @return string
     */
    public function render_form_open()
    {
        return Form_Helper::open($this->action, $this->attributes());
    }

    /**
     * Render form close tag
     * 
     * @return string
     */
    public function render_form_close()
    {
        return Form_Helper::close();
    }

    /**
     * Render javascript for this form
     */
    public function render_js()
    {
        // Link required scripts
        $this->add_scripts();
        
        $js =
            "var f, e, v;\n\n"
          . "f = new jForm('{$this->name}', '{$this->id}')\n";


        foreach ($this->get_elements() as $element)
        {
            if ($element->without_value)
                continue;
            
            $element_js = $element->render_js();
            if ($element_js !== NULL)
            {
                $js .= $element_js;
            }
        }

        $js .=
            "\nf.init();\n";


        $layout = Layout::instance();

        $layout->add_script(
"
$(document).ready(function(){
    $js
});
", TRUE);
        
    }

    /**
     * Include required javascripts
     */
    public function add_scripts()
    {
        static $scripts_installed = FALSE;
        
        if ( ! $scripts_installed)
        {
            jQuery::add_scripts();

            Layout::instance()->add_script(Modules::uri('forms') . '/public/js/forms.js');

            $scripts_installed = TRUE;
        }
    }

    /**
     * Render form via magic method
     *
     * @return string Form html
     */
    public function  __toString()
    {
        return $this->render();
    }

    /**
     * Renders all not-hidden components
     *
     * @param  array $include Component to render (NULL - render all)
     * @param  array $exclude Components not to render
     * @return string
     */
    public function render_components(array $include = NULL, array $exclude = NULL)
    {
        $html = '';

        foreach ($this->get_components() as $component)
        {
            if ( ! ($component instanceof Form_Element_Hidden)
                && ($include === NULL ||   in_array($component->name, $include))
                && ($exclude === NULL || ! in_array($component->name, $exclude))
                && $component->render
            )
            {
                $html .= $component->render();
            }
        }

        return $html;
    }

    /**
     * Renders hidden elements
     *
     * @return string
     */
    public function render_hidden()
    {
        $html = '';

        foreach ($this->get_elements() as $element)
        {
            if ($element->type == 'hidden')
            {
                $html .= $element->render();
            }
        }

        return $html;
    }

    /**
     * Renders form messages and errors
     *
     * @return string
     */
    public function render_messages()
    {
        $html = '';

        $messages = array_merge($this->_messages, FlashMessages::fetch_all($this->name));

        foreach ($messages as $message)
        {
            $html .= View_Helper::flash_msg($message['text'], $message['type']);
        }

        return $html;
    }

    // -------------------------------------------------------------------------
    // Errors & messages
    // -------------------------------------------------------------------------
    /**
     * Manually add error to form or a form element
     *
     * @param string $name Element name
     * @param string $text Error text
     * @return Form
     */
    function error($text, $element_name = NULL)
    {
        if ($element_name === NULL ||  ! isset($this->_elements[$element_name]))
        {
            // General error
            parent::error($text);
        }
        else
        {
            // Error for specific element
            $this->get_element($element_name)->error($text);
        }

        return $this;
    }

    /**
     * Adds many errors at once / return component errors
     *
     * Errors are in format array(field_name => array(error))
     *
     * @param array $errors
     * @return Form
     */
    function errors(array $errors = NULL)
    {
        if ($errors !== NULL)
        {
            // ----- Add errors
            foreach ($errors as $error)
            {
                $element_name = isset($error['field']) ? $error['field'] : NULL;

                $this->error($error['text'], $element_name);
            }
        }

        // ----- Get errors
        // General errors
        $errors = array();

        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                $errors[] = $message;
            }
        }

        // Element errors
        foreach ($this->_elements as $element)
        {
            foreach ($element->errors() as $error)
            {
                $error['field'] = $element->name;
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * Add a flash message to form
     *
     * @param  string $text
     * @param  integer $type
     * @return Form
     */
    function flash_message($text, $type = FlashMessages::MESSAGE)
    {
        FlashMessages::add($text, $type, $this->name); // use form name as category
        return $this;
    }

    // -------------------------------------------------------------------------
    // Tabs
    // -------------------------------------------------------------------------
    /**
     * Get form tabs
     * 
     * @return array
     */
    public function get_tabs()
    {
        if  ( ! isset($this->_properties['tabs']))
        {
            $tabs = array();
            
            // Generate all tabs from "Form_Fieldset_Tab" elements
            foreach ($this->get_components() as $component)
            {
                if ($component instanceof Form_Fieldset_Tab)
                {
                    $tabs[] = array(
                        'id'      => $component->id,
                        'caption' => $component->label
                    );
                }
            }

            if ( ! empty($tabs))
            {
                $this->_properties['tabs'] = array(
                    'prefix' => $this->id,
                    'tabs'   => $tabs
                );
            }
            else
            {
                // Form has no tabs
                $this->_properties['tabs'] = array();
            }
        }

        return $this->_properties['tabs'];
    }

    /**
     * Render form tabs (if any)
     */
    public function render_tabs()
    {
        $html = '';

        $tabs = $this->tabs;
        if ( ! empty($tabs))
        {
            // Add javascripts to initialize tabs
            Layout::instance()->add_script('$(function() { var tabs = new Tabs("' . $tabs['prefix'] . '"); });', TRUE);

            $html .= '<div class="tabs panel_tabs">';
            foreach ($tabs['tabs'] as $tab)
            {
                $html .= '<a href="#' . $tab['id'] . '">' . $tab['caption'] . '</a>';
            }
            $html .= '</div>';
        }

        return $html;
    }
}
