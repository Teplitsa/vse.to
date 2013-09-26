<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend extends Form_Model
{
    /**
     * @var string
     */
    public $template_set = 'table';

    /**
     * Get the form name or generate one.
     * 
     * @return string
     */
    public function default_name()
    {
        return trim(str_replace('frontend', '', parent::default_name()), '_');
    }
}