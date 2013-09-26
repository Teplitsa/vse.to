<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Trim whitespace and cut strings to specified length
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Filter_TrimCrop extends Form_Filter {

    /**
     * Cut strings longer than this parameter
     * @var string
     */
    protected $_maxlength = 0;

    /**
     * List of characters to trim. (@see trim)
     * Defaults to tabs and whitespaces
     * @var string
     */
    protected $_charlist = " \t";

    /**
     * Creates filter
     *
     * @param integer $maxlength Maximum allowed length
     * @param string  $charlist  Characters to trim
     */
    public function  __construct($maxlength = 0, $charlist = NULL)
    {
        $this->_maxlength = $maxlength;
    }

    /**
     * Trims characters from value
     *
     * @param string $value
     */
    public function filter($value)
    {
        // Trim string value
        $value = trim((string) $value, $this->_charlist);

        // Crop string value, if necessary
        if ($this->_maxlength > 0 && strlen($value) > $this->_maxlength)
        {
            return UTF8::substr($value, 0, $this->_maxlength);
        }
        else
        {
            return $value;
        }
    }
}