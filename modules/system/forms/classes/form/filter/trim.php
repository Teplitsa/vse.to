<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Trim whitespace and cut strings to specified length
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Filter_Trim extends Form_Filter {

    /**
     * List of characters to trim. (@see trim)
     * Defaults to tabs and whitespaces
     * @var string
     */
    protected $_charlist = " \t";

    /**
     * Creates filter
     *
     * @param string  $charlist  Characters to trim
     */
    public function  __construct($charlist = NULL)
    {
        if ($charlist !== NULL)
        {
            $this->_charlist = $charlist;
        }
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

        return $value;
    }
}