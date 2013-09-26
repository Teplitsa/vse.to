<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Cut string to the specified length
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Filter_Crop extends Form_Filter {

    /**
     * Cut strings longer than this parameter
     * @var string
     */
    protected $_maxlength = 0;

    /**
     * Creates filter
     *
     * @param integer $maxlength Maximum allowed length
     * @param string  $charlist  Characters to trim
     */
    public function  __construct($maxlength = 0)
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