<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validates that specified value is in array
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_InArray extends Form_Validator {

    const NOT_FOUND = 'NOT_FOUND';
    const EMPTY_OPTIONS  = 'EMPTY_OPTIONS';

    protected $_messages = array(
        self::EMPTY_OPTIONS  => 'Empty options!',
        self::NOT_FOUND      => 'Поле :label имеет некорректное значение!'
    );

    /**
     * List of valid options for value
     * @var array
     */
    protected $_options = array();

    /**
     * It's possible to select from empty options
     * @var boolean
     */
    protected $_allow_empty_options = TRUE;

    /**
     * Creates validator
     *
     * @param array   $options              List of valid options
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     * @param boolean $allow_underscore     Allow underscores
     */
    public function  __construct(array $options, array $messages = NULL, $breaks_chain = TRUE, $allow_empty_options = TRUE)
    {
        $this->_options = $options;
        $this->_allow_empty_options = $allow_empty_options;

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
        if (empty($this->_options) && ! $this->_allow_empty_options)
        {
            // Empty options not allowed?
            $this->_error(self::EMPTY_OPTIONS);
            return FALSE;
        }

        if ( ! in_array($this->_value, $this->_options))
        {
            $this->_error(self::NOT_FOUND);
            return FALSE;
        }

        return TRUE;
    }
}