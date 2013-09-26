<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Ajax validator
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_Ajax extends Form_Validator
{
    protected $_uri;
    
    /**
     * Construct ajax validator
     * 
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->_uri = $uri;
    }
    
    /**
     * Validate
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        return TRUE;
    }

    /**
     * Render javascript for this validator
     *
     * @return string
     */
    public function render_js()
    {        
        $js =
            "v = new jFormValidatorAjax('" . URL::site($this->_uri) . "');\n";

        return $js;
    }
}