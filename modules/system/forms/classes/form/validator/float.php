<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Floating point number validator.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Float extends Form_Validator_Regexp {

    const INVALID       = 'INVALID';
    const TOO_SMALL     = 'TOO_SMALL';
    const TOO_BIG       = 'TOO_BIG';

    protected $_messages = array(
        self::INVALID       => 'Invalid value specified: should be string or integer or float',
        self::EMPTY_STRING  => 'Вы не указали поле ":label!"',
        self::NOT_MATCH     => 'Неверный формат числа',
        self::TOO_SMALL     => 'Некорректное значение!',
        self::TOO_BIG       => 'Некорректное значение!',
    );

    /**
     * Minimum allowed value
     * @var integer
     */
    protected $_min;

    /**
     * Maximum allowed value
     * @var integer
     */
    protected $_max;

    /**
     * Inclusive or non-inclusive min/max
     * @var boolean
     */
    protected $_inclusive;

    /**
     * Creates float number validator
     *
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     */
    public function  __construct($min = NULL, $max = NULL, $inclusive = TRUE, array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE)
    {
        $this->_min = $min;
        $this->_max = $max;
        $this->_inclusive = $inclusive;

        parent::__construct('/^\s*[+-]?\d+([.,]\d+)?\s*$/', $messages, $breaks_chain, $allow_empty);
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

        if (!is_string($value) && !is_int($value) && !is_double($value))
        {
            $this->_error(self::INVALID);
            return FALSE;
        }

        if ( ! parent::_is_valid($context))
        {
            return FALSE;
        }

        $value = l10n::string_to_float($value);

        if (     $this->_min !== NULL
             && (
                    $value <  $this->_min && $this->_inclusive
                 || $value <= $this->_min && ! $this->_inclusive
            )
        )
        {
            $this->_error(self::TOO_SMALL);
            return FALSE;
        }
       
        if (     $this->_max !== NULL
             && (
                    $value >  $this->_max && $this->_inclusive
                 || $value >= $this->_max && ! $this->_inclusive
            )
        )
        {
            $this->_error(self::TOO_BIG);
            return FALSE;
        }

       return TRUE;
    }

    /**
     * Replaces :min and :max
     *
     * @param string $error_text
     * @return string
     */
    protected function _replace_placeholders($error_text)
    {
        $error_text = parent::_replace_placeholders($error_text);

        return str_replace(
            array(':min', ':max'),
            array($this->_min, $this->_max),
            $error_text
        );
    }
}