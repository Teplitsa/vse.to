<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Cut string to the specified length
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Filter_Char extends Form_Filter {

    /**
     * Cut strings longer than this parameter
     * @var string
     */
    protected $_char = '.';

    /**
     * Creates filter
     *
     * @param integer $maxlength Maximum allowed length
     * @param string  $charlist  Characters to trim
     */
    public function  __construct($char = '.')
    {
        $this->_char = $char;
    }

    /**
     * Trims characters from value
     *
     * @param string $value
     */
    public function filter($value)
    {
        // Crop string value, if necessary
        return current(explode($this->_char,$value));
    }
}