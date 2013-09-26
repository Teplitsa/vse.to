<?php defined('SYSPATH') or die('No direct script access.');

class Model_Payment_Mapper extends Model_Mapper
{
    /**
     * Cache find all results
     * @var boolean
     */
    public $cache_find_all = FALSE;

    public function init()
    {
        parent::init();

        $this->add_column('id', array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        
        $this->add_column('caption', array('Type' => 'varchar(63)'));
        $this->add_column('description', array('Type' => 'text'));

        $this->add_column('module', array('Type' => 'varchar(31)'));
        $this->add_column('properties', array('Type' => 'array'));
        $this->add_column('active', array('Type' => 'boolean'));
    }

    /**
     * Find all payment types supported by given delivery type
     *
     * @param  Model_Payment $payment
     * @param  Model_Delivery $delivery
     * @param  array $params
     * @return Models
     */
    public function find_all_by_delivery(Model_Payment $payment, Model_Delivery $delivery, array $params = NULL)
    {
        $payment_table  = $this->table_name();
        $paydeliv_table = Model_Mapper::factory('PaymentDelivery_Mapper')->table_name();

        $columns = isset($params['columns']) ? $params['columns'] : array("$payment_table.*");

        $query = DB::select_array($columns)
            ->from($payment_table)
            ->join($paydeliv_table, 'INNER')
                ->on("$paydeliv_table.payment_id", '=', "$payment_table.id")
            ->where("$paydeliv_table.delivery_id", '=', (int) $delivery->id);

        // Add limit, offset and order by statements
        if (isset($params['known_columns']))
        {
            $known_columns = $params['known_columns'];
        }
        else
        {
            $known_columns = array();
        }

        $this->_std_select_params($query, $params, $known_columns);

        $result = $query->execute($this->get_db());

        $data = array();
        if (count($result))
        {
            // Convert to correct types
            foreach ($result as $values)
            {
                $data[] = $this->_unsqlize($values);
            }
        }

        return new Models(get_class($payment), $data);
    }
}