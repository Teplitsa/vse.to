<?php defined('SYSPATH') or die('No direct script access.');

class Model_Site_Mapper extends Model_Mapper
{
    /**
     * Cache find all results
     * @var boolean
     */
    public $cache_find_all = FALSE;

    public function init()
    {
        parent::init();

        $this->add_column('id',          array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('url',         array('Type' => 'varchar(255)', 'Key' => 'INDEX'));
        
        $this->add_column('caption',     array('Type' => 'varchar(255)'));
        $this->add_column('settings',    array('Type' => 'array'));
    }
}