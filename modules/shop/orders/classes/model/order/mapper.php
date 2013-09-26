<?php defined('SYSPATH') or die('No direct script access.');

class Model_Order_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('created_at', array('Type' => 'int unsigned'));

        $this->add_column('site_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('status_id', array('Type' => 'tinyint unsigned', 'Key' => 'INDEX'));

        $this->add_column('sum', array('Type' => 'money'));

        $this->add_column('name',    array('Type' => 'varchar(63)'));
        $this->add_column('phone',   array('Type' => 'varchar(63)'));
        $this->add_column('email',   array('Type' => 'varchar(31)'));
        $this->add_column('address', array('Type' => 'text'));
        $this->add_column('comment', array('Type' => 'text'));
    }

}