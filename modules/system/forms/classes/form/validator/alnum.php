<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Alpha-numeric validator.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Alnum extends Form_Validator_Regexp {

    const INVALID       = 'INVALID';

    protected $_messages = array(
        self::INVALID       => 'Invalid value specified: should be float, string or integer',
        self::EMPTY_STRING  => 'Вы не указали поле ":label!"',
        self::NOT_MATCH     => 'Поле ":label" может содержать только латинские буквы, цифры и символ подчёркивания'
    );

    /**
     * Creates Alpha-Numeric validator
     *
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     * @param boolean $allow_underscore     Allow underscores
     */
    public function  __construct(array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE, $allow_underscore = TRUE)
    {
        if ($allow_underscore)
        {
            $regexp = '/^[a-zA-Z0-9_]+$/';
        }
        else
        {
            $regexp = '/^[a-zA-Z0-9]+$/';
            $this->_messages[self::NOT_MATCH] = 'Поле ":label" может содержать только латинские буквы и цифры';
        }

        parent::__construct($regexp, $messages, $breaks_chain, $allow_empty);
    }

    /**
     * Validate
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        $value = $this->_value;

        if (!is_string($value) && !is_int($value) && !is_float($value))
        {
            $this->_error(self::INVALID);
            return false;
        }

        return parent::_is_valid($context);
    }
}