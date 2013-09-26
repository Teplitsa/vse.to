<?php defined('SYSPATH') or die('No direct script access.');

class Model_CartProduct_Mapper extends Model_Mapper
{

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));

        $this->add_column('cart_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('product_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        $this->add_column('marking',  array('Type' => 'varchar(127)'));
        $this->add_column('caption',  array('Type' => 'varchar(255)'));
        $this->add_column('price',    array('Type' => 'money'));
        $this->add_column('quantity', array('Type' => 'int unsigned'));
    }
}