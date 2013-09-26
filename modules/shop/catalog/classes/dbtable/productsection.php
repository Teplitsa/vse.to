<?php defined('SYSPATH') or die('No direct script access.');

class DbTable_ProductSection extends DbTable
{

    public function init()
    {
        parent::init();

        $this->add_column('product_id',        array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('section_id',        array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('distance',          array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('sectiongroup_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
    }
}