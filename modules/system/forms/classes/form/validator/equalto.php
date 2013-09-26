<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validates the equality of value to another value.
 * (Such as password confirmation)
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_EqualTo extends Form_Validator {

    const NOT_EQUAL = 'NOT_EQUAL';

    protected $_messages = array(
        self::NOT_EQUAL => 'Value is not equal to target value'
    );

    /**
     * Name of element to compare value with
     * @var string
     */
    protected $_target;

    /**
     * Creates validator
     *
     * @param array   $messages     Error messages templates
     * @param boolean $breaks_chain Break chain after validation failure
     */
    public function  __construct($target, array $messages = NULL, $breaks_chain = TRUE)
    {
        parent::__construct($messages, $breaks_chain);

        $this->_target = $target;
    }

    /**
     * Validate!
     *
     * @param array $context
     */
    protected function _is_valid(array $context = NULL)
    {
        if ( ! is_array($context))
        {
            // Context is required for confirmation
            throw new Exception('Context was not supplied to _is_valid function of validator ":validator"',
                array(':validator' => get_class($this))
            );
        }

        if ( ! isset($context[$this->_target]))
        {
            throw new Exception('Unable to compare with :target - no such key in context data. (":validator")',
                array(':target' => $this->_target, ':validator' => get_class($this))
            );
        }

        $value = (string) $this->_value;
        $target_value = (string) $context[$this->_target];

        if ($value !== $target_value)
        {
            $this->_error(self::NOT_EQUAL);
            return false;
        }

        return true;
    }
    
    /**
     * Render javascript for this validator
     *
     * @return string
     */
    public function render_js()
    {
        $error = $this->_messages[self::NOT_EQUAL];
        $error = $this->_replace_placeholders($error);

        $js =
            "v = new jFormValidatorEqualTo('" . $this->_target . "', '" . $error . "');\n";

        return $js;
    }
}