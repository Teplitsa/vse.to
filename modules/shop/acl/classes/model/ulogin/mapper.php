<?php defined('SYSPATH') or die('No direct script access.');

class Model_Ulogin_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('user_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('network', array('Type' => 'varchar(255)'));
        $this->add_column('identity', array('Type' => 'varchar(255)'));
    }
}