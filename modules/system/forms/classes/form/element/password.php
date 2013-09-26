<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Form password element
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Element_Password extends Form_Element_Input {

    /**
     * @return string
     */
    public function get_type()
    {
        return 'password';
    }

    /**
     * Use the same templates as input element
     * 
     * @return string
     */
    public function  default_config_entry()
    {
        return 'input';
    }
}
