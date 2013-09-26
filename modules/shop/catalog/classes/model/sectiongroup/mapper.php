<?php defined('SYSPATH') or die('No direct script access.');

class Model_SectionGroup_Mapper extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('id',       array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('system', array('Type' => 'boolean'));
        $this->add_column('site_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('name',    array('Type' => 'varchar(31)', 'Key' => 'INDEX'));
        $this->add_column('caption', array('Type' => 'varchar(31)'));
        

//        $this->cache_find_all = TRUE;
    }

}