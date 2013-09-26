<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validate agains a regular expression
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Regexp extends Form_Validator {

    const EMPTY_STRING  = 'EMPTY_STRING';
    const NOT_MATCH = 'NOT_MATCH';

    protected $_messages = array(
        self::EMPTY_STRING  => 'Value cannot be an empty string',
        self::NOT_MATCH     => '":value" does not match pattern ":regexp"'
    );

    /**
     * Regular expression to match against
     * @var string
     */
    protected $_regexp;

    /**
     * Allow empty strings
     * @var boolean
     */
    protected $_allow_empty = FALSE;

    /**
     * Creates Regexp validator
     *
     * @param string  $regexp               Regular expression
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     */
    public function  __construct($regexp, array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE)
    {
        parent::__construct($messages, $breaks_chain);

        $this->_regexp = $regexp;
        $this->_allow_empty = $allow_empty;
    }

    /**
     * Validate
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
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }

        if ( ! preg_match($this->_regexp, $value))
        {
            $this->_error(self::NOT_MATCH);
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Replaces :value and :regexp
     *
     * @param string $error_text
     * @return string
     */
    protected function _replace_placeholders($error_text)
    {
        $error_text = parent::_replace_placeholders($error_text);

        return str_replace(':regexp', $this->_regexp, $error_text);
    }
    
    /**
     * Render javascript for this validator
     *
     * @return string
     */
    public function render_js()
    {
        $error = $this->_messages[self::NOT_MATCH];
        $error = $this->_replace_placeholders($error);

        $js =
            "v = new jFormValidatorRegexp("
          .     "{$this->_regexp}"
          .     ", '$error'"
          .     ", " . (int)$this->_allow_empty
          .     ", " . (int)$this->_breaks_chain
          . ");\n";

        return $js;
    }
}