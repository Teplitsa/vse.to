<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cart_Mapper_DB extends Model_Mapper
{
    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('order_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        // Discounts for this cart (applied coupons)
        $this->add_column('discount_percent', array('Type' => 'float'));
        $this->add_column('discount_sum',     array('Type' => 'money'));

        // Bonuses used

        // Cart summary
        $this->add_column('sum',               array('Type' => 'money'));
        $this->add_column('total_sum',         array('Type' => 'money'));

    }
}