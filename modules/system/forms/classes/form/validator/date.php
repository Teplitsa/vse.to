<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validate dates
 * Date is expected to be specified as an array(day,  month, year)
 * or a string in format 'yyyy-mm-dd'
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Date extends Form_Validator {

    const EMPTY_STRING  = 'EMPTY_STRING';
    const INVALID_DAY   = 'INVALID_DAY';
    const INVALID_MONTH = 'INVALID_MONTH';
    const INVALID_YEAR  = 'INVALID_YEAR';
    const INVALID       = 'INVALID';

    protected $_messages = array(
        self::EMPTY_STRING  => 'Вы не указали дату',
        self::INVALID_DAY   => 'День указан некорректно',
        self::INVALID_MONTH => 'Месяц указан некорректно',
        self::INVALID_YEAR  => 'Год указан некорректно',
        self::INVALID       => 'Invalid format for date'
    );

    /**
     * Day, month and year for current value
     */
    protected $_day;
    protected $_month;
    protected $_year;

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
     * Date should be an array of (day, month, year)
     * or a string in 'yyyy-mm-dd' format.
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        $this->_day   = NULL;
        $this->_month = NULL;
        $this->_year  = NULL;
            
        if (is_array($this->_value))
        {
            if ( ! isset($value['day']) || ! isset($value['month']) || ! isset($value['year']))
            {
                $this->_error(self::INVALID);
                return FALSE;
            }
            
            // Value is supposed to be an array(day, month, year)
            $this->_day   = $value['day'];
            $this->_month = $value['month'];
            $this->_year  = $value['year'];

            // An empty string allowed ?
            if (
                    preg_match('/^\s*$/', $this->_day)
                 && preg_match('/^\s*$/', $this->_month)
                 && preg_match('/^\s*$/', $this->_year)
            ) {
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
        }
        else
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
                $this->_day = $matches['day'];
            }

            if (isset($matches['month']))
            {
                $this->_month = $matches['month'];
            }

            if (isset($matches['year']))
            {
                $this->_year = $matches['year'];
            }
        }

        // day
        if (isset($this->_day)  && (! ctype_digit($this->_day) || (int)$this->_day <= 0 || (int)$this->_day > 31))
        {
            $this->_error(self::INVALID_DAY);
            return false;
        }

        // month
        if (isset($this->_month) && (! ctype_digit($this->_month) || (int)$this->_month <= 0 || (int)$this->_month > 12))
        {
            $this->_error(self::INVALID_MONTH);
            return false;
        }

        // year
        if (isset($this->_year) && (! ctype_digit($this->_year) || (int)$this->_year <= 0))
        {
            $this->_error(self::INVALID_YEAR);
            return false;
        }

        return true;
    }

    /**
     * Replaces :day, :month and :year
     *
     * @param string $error_text
     * @return string
     */
    protected function _replace_placeholders($error_text)
    {
        $error_text = parent::_replace_placeholders($error_text);

        return str_replace(
            array(':day', ':month', ':year'),
            array($this->_day, $this->_month, $this->_year),
            $error_text
        );
    }
}