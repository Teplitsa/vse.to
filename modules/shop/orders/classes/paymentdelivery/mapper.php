<?php defined('SYSPATH') or die('No direct script access.');

class PaymentDelivery_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('payment_id',  array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('delivery_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
    }

    /**
     * Link payment type to given delivery types
     * 
     * @param Model_Payment $payment
     * @param array $delivery_ids
     */
    public function link_payment_to_deliveries(Model_Payment $payment, array $delivery_ids)
    {
        $this->delete_rows(DB::where('payment_id', '=', (int) $payment->id));
        foreach ($delivery_ids as $delivery_id)
        {
            $this->insert(array(
                'payment_id'  => $payment->id,
                'delivery_id' => $delivery_id
            ));
        }
    }

    /**
     * Link delivery type to given payment types
     *
     * @param Model_Delivery $delivery
     * @param array $payment_ids
     */
    public function link_delivery_to_payments(Model_Delivery $delivery, array $payment_ids)
    {
        $this->delete_rows(DB::where('delivery_id', '=', (int) $delivery->id));
        foreach ($payment_ids as $payment_id)
        {
            $this->insert(array(
                'payment_id'  => $payment_id,
                'delivery_id' => $delivery->id
            ));
        }
    }
}