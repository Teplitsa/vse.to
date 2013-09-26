<?php defined('SYSPATH') or die('No direct script access.');

class Model_Delivery_Courier extends Model_Delivery
{
    /**
     * Calculate delivery price for order
     * 
     * @param  Model_Order $order 
     * @return float
     */
    public function  calculate_price(Model_Order $order)
    {
        $price = 0.0;

        $zone = new Model_CourierZone();
        $zone->find((int) $order->zone_id);
        if ( ! isset($zone->id))
        {
            $this->error('Не указана зона доставки');
            return $price;
        }

        return $zone->price;
    }
}