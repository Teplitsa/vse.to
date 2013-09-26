<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Select from a list of options
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Options extends Form_Element {

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
    public function __construct($name, array $properties = NULL, array $attributes = NULL)
    {
        parent::__construct($name, $properties, $attributes);

        for ($i = 0; $i < $this->options_count; $i++)
        {
            $element = new Form_Element_Input($this->name . "[$i]", array('label' => '', 'layout' => 'standart'), $attributes);
            $this->add_component($element);
        }
    }

    /**
     * Add filter to the every option
     * 
     * @param  Form_Filter $validator
     * @return Form_Element_Options
     */
    public function add_filter(Form_Filter $filter)
    {
        foreach ($this->get_components() as $component)
        {
            if ($component instanceof Form_Element)
            {
                $component->add_filter($filter);
            }
        }
        return $this;
    }
    
    /**
     * Add validator to the every option
     *
     * @param  Form_Validator $validator
     * @return Form_Element_Options
     */
    public function add_validator(Form_Validator $validator)
    {
        foreach ($this->get_components() as $component)
        {
            if ($component instanceof Form_Element)
            {
                $component->add_validator($validator);
            }
        }
        return $this;
    }

    /**
     * Return input type
     * @return string
     */
    public function default_type()
    {
        return 'options';
    }

    /**
     * Default name for parameter in url that holds the number of options
     * @return string
     */
    public function default_options_count_param()
    {
        return 'options_count';
    }
    
    /**
     * Default caption of the incremental link
     * @return string
     */
    public function default_option_caption()
    {
        return 'добавить опцию';
    }
    /**
     * Get number of options for input
     *
     * @return integer
     */
    public function get_options_count()
    {
        $options_count = Request::current()->param($this->options_count_param);

        if ($options_count !== NULL)
            return $options_count;

        if (isset($this->_properties['options_count']))
            return $this->_properties['options_count'];

        return 0;
    }

    /**
     * Render form element
     * 
     * @return string
     */
    public function render()
    {
        $html = parent::render();

        // Url to increase options count
        $url_params = array();
        $url_params[$this->options_count_param] = $this->options_count + 1;
        Template::replace($html, 'inc_options_count_url', URL::self($url_params));
        Template::replace($html, 'option_caption',$this->option_caption);

        return $html;
    }

    /**
     * Renders select element, constiting of checkboxes
     *
     * @return string
     */
    public function render_input()
    {
        $html = '';

        foreach ($this->get_components() as $component)
        {
            $html .= $component->render();
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
