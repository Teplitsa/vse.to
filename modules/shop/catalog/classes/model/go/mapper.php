<?php defined('SYSPATH') or die('No direct script access.');

class Model_Go_Mapper extends Model_Mapper_Resource
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('telemost_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        
//        $this->cache_find_all = TRUE;
    }
}