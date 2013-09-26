<?php defined('SYSPATH') or die('No direct script access.');

class Model_OrderComment_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('order_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('created_at', array('Type' => 'int unsigned'));

        $this->add_column('user_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('user_name', array('Type' => 'varchar(255)'));

        $this->add_column('text', array('Type' => 'text'));

        $this->add_column('notify_client', array('Type' => 'boolean'));
    }
}