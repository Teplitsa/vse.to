<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend extends Form_Model
{
    /**
     * Get the form name or generate one.
     * 
     * @return string
     */
    public function default_name()
    {
        return trim(str_replace('backend', '', parent::default_name()), '_');
    }
}