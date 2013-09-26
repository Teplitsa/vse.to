<?php defined('SYSPATH') or die('No direct script access.');

class Model_OrderStatus_Mapper extends Model_Mapper
{
    /**
     * Cache find_all() results
     * @var boolean
     */
    public $cache_find_all = FALSE;

    public function init()
    {
        parent::init();

        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('caption', array('Type' => 'varchar(63)'));
        $this->add_column('system',  array('Type' => 'boolean'));

        // Reserve first MAX_SYSTEM_ID ids for system statuses
        $this->add_option('AUTO_INCREMENT', Model_OrderStatus::MAX_SYSTEM_ID + 1);        
    }
}