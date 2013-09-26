<?php defined('SYSPATH') or die('No direct script access.');

class Model_Country_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('name',   array('Type' => 'varchar(255)'));
        $this->add_column('alpha2', array('Type' => 'char(2)'));
        $this->add_column('alpha3', array('Type' => 'char(3)'));
    }
}