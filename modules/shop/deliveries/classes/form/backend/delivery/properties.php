<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Delivery_Properties extends Form_Backend
{
    /**
     * @var Model_Delivery
     */
    protected $_delivery;

    /**
     * @var Model_Order
     */
    protected $_order;

    /**
     * Create form
     *
     * @param Model_Delivery $delivery
     * @param Model_Order $order
     * @param string $name
     */
    public function  __construct(Model_Delivery $delivery = NULL, Model_Order $order = NULL, $name = NULL)
    {
        $this->delivery($delivery);
        $this->order($order);

        parent::__construct($delivery, $name);
    }
    /**
     * Get/set delivery
     * 
     * @param  Model_Delivery $delivery
     * @return Model_Delivery
     */
    public function delivery(Model_Delivery $delivery = NULL)
    {
        if ($delivery !== NULL)
        {
            $this->_delivery = $delivery;
        }
        return $this->_delivery;
    }

    /**
     * Get/set order
     *
     * @param  Model_Order $order
     * @return Model_Order
     */
    public function order(Model_Order $order = NULL)
    {
        if ($order !== NULL)
        {
            $this->_order = $order;
        }
        return $this->_order;
    }
}
