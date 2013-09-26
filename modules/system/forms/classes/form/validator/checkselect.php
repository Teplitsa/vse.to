<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validates that check select values are among allowed options
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_CheckSelect extends Form_Validator {

    const INVALID_TYPE = 'INVALID_TYPE';
    const INVALID      = 'INVALID';

    protected $_messages = array(
        self::INVALID_TYPE => 'Invalid value. Array is expected',
        self::INVALID      => 'Поле ":label" имеет некорректное значение!'
    );

    /**
     * List of valid options for value
     * @var array
     */
    protected $_options = array();

    /**
     * Creates validator
     *
     * @param array   $options              List of valid options
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     */
    public function  __construct(array $options, array $messages = NULL, $breaks_chain = TRUE)
    {
        $this->_options = $options;

        parent::__construct($messages, $breaks_chain);
    }

    /**
     * Set up options for validator
     *
     * @param array $options
     */
    public function set_options(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Validate that value is among valid options
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        if ( ! is_array($this->_value))
        {
            $this->_error(self::INVALID_TYPE);
            return FALSE;
        }

        if (count(array_diff(array_keys($this->_value), $this->_options)))
        {
            // There are some options in value that a not among valid $options
            $this->_error(self::INVALID);
            return FALSE;
        }

        return TRUE;
    }
}