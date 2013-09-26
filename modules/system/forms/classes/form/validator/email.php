<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validates value to be a valid e-mail address.
 * Utilizes Kohana native Validate::email.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Email extends Form_Validator {

    const EMPTY_STRING  = 'EMPTY_STRING';
    const INVALID       = 'INVALID';

    protected $_messages = array(
        self::EMPTY_STRING  => 'Вы не указали :label!',
        self::INVALID       => 'Некорректный формат e-mail адреса!'
    );

    /**
     * Allow empty strings
     * @var boolean
     */
    protected $_allow_empty = FALSE;

    /**
     * Creates validator
     *
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     */
    public function  __construct(array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE)
    {
        parent::__construct($messages, $breaks_chain);

        $this->_allow_empty = $allow_empty;
    }

    /**
     * Validate using Kohanas native Validate::email
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        $value = (string) $this->_value;

        if (preg_match('/^\s*$/', $value))
        {
            // An empty string allowed ?
            if (!$this->_allow_empty)
            {
                $this->_error(self::EMPTY_STRING);
                return false;
            }
            else
            {
                return true;
            }
        }

        if ( ! Validate::email($value))
        {
            $this->_error(self::INVALID);
            return false;
        }

        return true;
    }
}