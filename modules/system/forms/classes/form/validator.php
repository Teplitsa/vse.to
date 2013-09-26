<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract form validator
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Form_Validator {

    /**
     * Reference to form element
     * @var Form_Element
     */
    protected $_form_element;

    /**
     * Messages for each type of the error
     * @var array
     */
    protected $_messages = array();

    /**
     * Error messages
     * @var array
     */
    protected $_errors = array();

    /**
     * Value to be validated
     * @var mixed
     */
    protected $_value;

    /**
     * Break chain after validation failure
     * @var boolean
     */
    protected $_breaks_chain = TRUE;

    /**
     * Creates validator
     *
     * @param array   $messages     Error messages templates
     * @param boolean $breaks_chain Break chain after validation failure
     */
    public function  __construct(array $messages = NULL, $breaks_chain = TRUE)
    {
        if ($messages !== NULL)
        {
            foreach ($messages as $type => $message)
            {
                $this->_messages[$type] = $message;
            }
        }

        $this->_breaks_chain = $breaks_chain;
    }

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
     * Should this validator break chain after failure
     *
     * @return boolean
     */
    public function breaks_chain()
    {
        return $this->_breaks_chain;
    }

    /**
     * Actual validation is done here.
     *
     * @param array $context    Entire form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        return true;
    }

    /**
     * Perform validation of value
     *
     * @param mixed $value
     * @param array $context    Entire form data
     * @return boolean
     */
    public function validate($value, array $context = NULL)
    {
        $this->reset();

        $this->_value = $value;

        $result = $this->_is_valid($context);

        if ( ! is_bool($result))
        {
            throw new Kohana_Exception('Result of _is_valid() function is not boolen in validator :validator',
                array(':validator' => get_class($this))
            );
        }

        if ( ! $result && !$this->has_errors())
        {
            throw new Kohana_Exception('Validator :validator returned false for value ":value", but did not supplied any error messages',
                array(':validator' => get_class($this), ':value' => $this->_value)
            );
        }

        return $result;
    }

    /**
     * Resets validator: clears error messages, ...
     */
    public function reset()
    {
        $this->_errors = array();
    }

    /**
     * Adds an error message.
     * A message template must specified in $this->_messages for given type.
     *
     * @param string  $type     Type of the error.
     * @param boolean $encode   Encode html chars when replacing value
     */
    protected function _error($type)
    {
        if (isset($this->_messages[$type]))
        {
            $this->_errors[] = array(
                'text' => $this->_replace_placeholders($this->_messages[$type]),
                'type' => FlashMessages::ERROR
            );
        }
        else
        {
            throw new Kohana_Exception('There is no message for type :type in validator :validator',
                array(':type' => $type, ':validator' => get_class($this))
            );
        }
    }

    /**
     * Replaces placeholders in error message
     *
     * @param string $error_text
     * @return string
     */
    protected function _replace_placeholders($error_text)
    {
        if (strpos($error_text, ':value') !== FALSE)
        {
            $error_text = str_replace(':value', $this->_value, $error_text);
        }
        if ($this->_form_element !== NULL)
        {
            $error_text = str_replace(':label', UTF8::strtolower($this->_form_element->label), $error_text);
        }
        return $error_text;
    }

    /**
     * Are there any validation errors?
     *
     * @return boolean
     */
    public function has_errors()
    {
        return !empty($this->_errors);
    }

    /**
     * Returns validation errors
     *
     * @return array
     */
    public function get_errors()
    {
        return $this->_errors;
    }

    /**
     * Render javascript for this validator
     * 
     * @return string
     */
    public function render_js()
    {
        return NULL;
    }
}