<?php defined('SYSPATH') or die('No direct script access.');

class Model_History_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('created_at', array('Type' => 'int unsigned'));
        
        // type and id of the item that the history entry describes
        // (i.e. as "order" "10")
        $this->add_column('item_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('item_type', array('Type' => 'varchar(15)',  'Key' => 'INDEX'));
        
        $this->add_column('user_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('user_name', array('Type' => 'varchar(255)'));

        $this->add_column('text', array('Type' => 'text'));
    }
}