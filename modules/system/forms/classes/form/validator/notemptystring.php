<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validates that value is not an empty string
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_NotEmptyString extends Form_Validator {

    const EMPTY_STRING  = 'EMPTY_STRING';

    protected $_messages = array(
        self::EMPTY_STRING  => 'Вы не указали :label!',
    );

    /**
     * Validate
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        $value = (string) $this->_value;

        if (preg_match('/^\s*$/', $value))
        {
            $this->_error(self::EMPTY_STRING);
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
        $error = $this->_messages[self::EMPTY_STRING];
        $error = $this->_replace_placeholders($error);

        $js =
            "v = new jFormValidatorRegexp(/\S/, '" . $error . "');\n";

        return $js;
    }
}