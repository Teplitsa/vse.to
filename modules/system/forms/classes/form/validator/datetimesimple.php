<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validate dates and times
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_DateTimeSimple extends Form_Validator {

    const EMPTY_STRING   = 'EMPTY_STRING';
    const INVALID_DAY    = 'INVALID_DAY';
    const INVALID_MONTH  = 'INVALID_MONTH';
    const INVALID_YEAR   = 'INVALID_YEAR';
    const INVALID_SECOND = 'INVALID_SECOND';
    const INVALID_MINUTE = 'INVALID_MINUTE';
    const INVALID_HOUR   = 'INVALID_HOUR';
    const INVALID        = 'INVALID';

    protected $_messages = array(
        self::EMPTY_STRING   => 'Вы не указали дату',
        self::INVALID_DAY    => 'День указан некорректно',
        self::INVALID_MONTH  => 'Месяц указан некорректно',
        self::INVALID_YEAR   => 'Год указан некорректно',
        self::INVALID_SECOND => 'Секунда указана некорректно',
        self::INVALID_MINUTE => 'Минута указана некорректно',
        self::INVALID_HOUR   => 'Час указан некорректно',
        self::INVALID        => 'Некорректный формат'
    );

    /**
     * Allow date to be empty
     * @var boolean
     */
    protected $_allow_empty = FALSE;

    /**
     * Creates validator
     *
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty dates
     */
    public function  __construct(array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE)
    {
        parent::__construct($messages, $breaks_chain);

        $this->_allow_empty = $allow_empty;
    }

    /**
     * Validate.
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        // ----- Validate date string agains a format
        $value = (string)$this->_value;

        // An empty string allowed ?
        if (preg_match('/^\s*$/', $value))
        {
            if (!$this->_allow_empty)
            {
                $this->_error(self::EMPTY_STRING);
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }

        $regexp = l10n::date_format_regexp($this->get_form_element()->format);

        if ( ! preg_match($regexp, $value, $matches))
        {
            $this->_error(self::INVALID);
            return FALSE;
        }

        if (isset($matches['day']))
        {
            $v = $matches['day'];
            if ( ! ctype_digit($v) || (int)$v <= 0 || (int)$v > 31)
            {
                $this->_error(self::INVALID_DAY);
                return FALSE;
            }
        }

        if (isset($matches['month']))
        {
            $v = $matches['month'];
            if ( ! ctype_digit($v) || (int)$v <= 0 || (int)$v > 12)
            {
                $this->_error(self::INVALID_MONTH);
                return FALSE;
            }
        }

        if (isset($matches['year']))
        {
            $v = $matches['year'];
            if ( ! ctype_digit($v) || (int)$v <= 0)
            {
                $this->_error(self::INVALID_YEAR);
                return FALSE;
            }
        }
        
        if (isset($matches['second']))
        {
            $v = $matches['second'];
            if ( ! ctype_digit($v) || (int)$v < 0 || (int)$v >= 60)
            {
                $this->_error(self::INVALID_SECOND);
                return FALSE;
            }
        }
        if (isset($matches['minute']))
        {
            $v = $matches['minute'];
            if ( ! ctype_digit($v) || (int)$v < 0 || (int)$v >= 60)
            {
                $this->_error(self::INVALID_MINUTE);
                return FALSE;
            }
        }
        if (isset($matches['hour']))
        {
            $v = $matches['hour'];
            if ( ! ctype_digit($v) || (int)$v < 0 || (int)$v >= 24)
            {
                $this->_error(self::INVALID_HOUR);
                return FALSE;
            }
        }

        return TRUE;
    }
}