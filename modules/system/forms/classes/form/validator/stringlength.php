<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validates that length of string is in specified boundaries
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_StringLength extends Form_Validator
{

    const TOO_LONG  = 'TOO_LONG';
    const TOO_SHORT = 'TOO_SHORT';

    protected $_messages = array(
        self::TOO_SHORT => 'Колчиество символов в поле ":label" должно быть не меньше :minlength!',
        self::TOO_LONG  => 'Колчиество символов в поле ":label" не должно превышать :maxlength!'
    );

    /**
     * Maximum allowed string length
     * @var int
     */
    protected $_maxlength = 0;

    /**
     * Minimum allowed string length
     * @var int
     */
    protected $_minlength = 0;

    /**
     * Creates validator
     *
     * @param int $minlength        Minimum allowed string length
     * @param int $maxlength        Maximum allowed string length
     * @param array   $messages     Error messages templates
     * @param boolean $breaks_chain Break chain after validation failure
     */
    public function  __construct($minlength, $maxlength, array $messages = NULL, $breaks_chain = TRUE)
    {
        parent::__construct($messages, $breaks_chain);

        $this->_maxlength = $maxlength;
        $this->_minlength = $minlength;
    }

    /**
     * Validate!
     *
     * @param array $context
     */
    protected function _is_valid(array $context = NULL)
    {
        $value = (string) $this->_value;

        $strlen = UTF8::strlen($value);

        if ($this->_maxlength > 0 && $strlen > $this->_maxlength)
        {
            $this->_error(self::TOO_LONG);
            return false;
        }

        if ($strlen < $this->_minlength)
        {
            $this->_error(self::TOO_SHORT);
            return false;
        }

        return true;
    }

    /**
     * Replaces :maxlength and :minlength
     *
     * @param string $error_text
     * @return string
     */
    protected function _replace_placeholders($error_text)
    {
        $error_text = parent::_replace_placeholders($error_text);

        return str_replace(
            array(':maxlength', ':minlength'),
            array($this->_maxlength, $this->_minlength),
            $error_text
        );
    }
    
    /**
     * Render javascript for this validator
     *
     * @return string
     */
    public function render_js()
    {
        $messages = array();
        foreach ($this->_messages as $code => $error_text)
        {
            $messages[$code] = $this->_replace_placeholders($error_text);
        }
        
        $config = array(
            'messages' => $messages
        );

        $js =
            "v = new jFormValidatorStringLength("
          .     "{$this->_minlength}, {$this->_maxlength}"
          .     ", " . json_encode($config)
          . ");\n";

        return $js;
    }
}