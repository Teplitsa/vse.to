<?php defined('SYSPATH') or die('No direct script access.');

/**
 * File name validator.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Filename extends Form_Validator_Regexp {

    const INVALID       = 'INVALID';

    protected $_messages = array(
        self::INVALID       => 'Invalid value specified: should be string',
        self::EMPTY_STRING  => 'Вы не указали :label!',
        self::NOT_MATCH     => 'Поле ":label" содержит недопустимые символы!'
    );

    /**
     * Creates File name validator
     *
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     */
    public function  __construct(array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE)
    {
        $regexp = '/^[^\\\\\/]+$/'; //fuck my brain O_o \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

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

        if (!is_string($value))
        {
            $this->_error(self::INVALID);
            return false;
        }

        return parent::_is_valid($context);
    }
}