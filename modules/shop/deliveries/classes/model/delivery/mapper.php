<?php defined('SYSPATH') or die('No direct script access.');

class Model_Delivery_Mapper extends Model_Mapper
{
    /**
     * Cache find_all queries
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
        $this->add_column('settings', array('Type' => 'array'));
        $this->add_column('active', array('Type' => 'boolean'));
    }

    /**
     * Find model by criteria and return the model of correct class
     *
     * @param  Model $model
     * @param  Database_Expression_Where $condition
     * @param  array $params
     * @param  Database_Query_Builder_Select $query
     * @return Model
     */
    public function find_by(
        Model                         $model,
        Database_Expression_Where     $condition = NULL,
        array                         $params = NULL,
        Database_Query_Builder_Select $query = NULL
    )
    {
        $result = $this->select_row($condition, $params);

        if (empty($result))
        {
            $model->init();
            return $model;
        }

        $class = 'Model_Delivery_' . ucfirst($result['module']);
        if (strtolower(get_class($model)) != strtolower($class))
        {
            $model->init();
            // Create new model of desired class
            $model = new $class;
        }

        $model->properties($result);
        
        return $model;
    }

    /**
     * Find all delivery types supported by given payment type
     *
     * @param  Model_Delivery $delivery
     * @param  Model_Payment $payment
     * @param  array $params
     * @return Models
     */
    public function find_all_by_payment(Model_Delivery $delivery, Model_Payment $payment, array $params = NULL)
    {
        $delivery_table = $this->table_name();
        $paydeliv_table = Model_Mapper::factory('PaymentDelivery_Mapper')->table_name();

        $columns = isset($params['columns']) ? $params['columns'] : array("$delivery_table.*");

        $query = DB::select_array($columns)
            ->from($delivery_table)
            ->join($paydeliv_table, 'INNER')
                ->on("$paydeliv_table.delivery_id", '=', "$delivery_table.id")
            ->where("$paydeliv_table.payment_id", '=', (int) $payment->id);

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

        return new Models(get_class($delivery), $data);
    }
}