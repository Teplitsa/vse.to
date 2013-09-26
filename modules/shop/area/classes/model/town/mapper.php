<?php defined('SYSPATH') or die('No direct script access.');

class Model_Town_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('name',     array('Type' => 'varchar(63)'));
        $this->add_column('alias',     array('Type' => 'varchar(63)'));        
        $this->add_column('phonecode',   array('Type' => 'varchar(31)'));
        $this->add_column('timezone',   array('Type' => 'varchar(31)'));        
    }
}