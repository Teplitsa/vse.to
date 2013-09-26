<?php defined('SYSPATH') or die('No direct script access.');

class Model_CourierZone extends Model
{
    /**
     * Default zone delivery price
     * 
     * @return float
     */
    public function default_price()
    {
        return 0.0;
    }
}