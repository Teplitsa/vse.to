<?php defined('SYSPATH') or die('No direct script access.');

class Model_Region_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('country_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('name', array('Type' => 'varchar(255)'));
    }
}