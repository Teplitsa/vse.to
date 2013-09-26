<?php defined('SYSPATH') or die('No direct script access.');

class Model_Payment extends Model
{
    /**
     * Registered payment modules
     * @var array
     */
    protected static $_modules = array();

    /**
     * Register payment module
     *
     * @param string $module
     */
    public static function register(array $module_info)
    {
        if ( ! isset($module_info['module']))
        {
            throw new Kohana_Exception('Module was not supplied in module_info for :method', array(':method' => __METHOD__));
        }
        self::$_modules[$module_info['module']] = $module_info;
    }

    /**
     * Get module info for the specified module
     * 
     * @param  string $module
     * @return array
     */
    public static function module_info($module)
    {
        if (isset(self::$_modules[$module]))
        {
            return self::$_modules[$module];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get registered payment modules
     * 
     * @return array
     */
    public static function modules()
    {
        return self::$_modules;
    }

    /**
     * Get payment type caption by id
     *
     * @param integer $id
     */
    public static function caption($id)
    {
        // Obtain all payments (CACHED!)
        $payments = Model::fly('Model_Payment')->find_all(array('key' => 'id'));

        if (isset($payments[$id]))
        {
            return $payments[$id]->caption;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * All payment models use the same mapper
     * 
     * @param  string $mapper
     * @return Model_Mapper
     */
    public function mapper($mapper = NULL)
    {
        if ($mapper !== NULL || $this->_mapper !== NULL)
        {
            return parent::mapper($mapper);
        }

        // All payment models uses Model_Payment_Mapper as mapper
        $this->_mapper = Model_Mapper::factory('Model_Payment_Mapper');

        return $this->_mapper;
    }

    /**
     * Determine payment module from model class name
     * 
     * @return string
     */
    public function default_module()
    {
        $class = get_class($this);

        if (strpos($class, 'Model_Payment_') === 0)
        {
            return strtolower(substr($class, strlen('Model_Payment_')));
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Default payment price
     * 
     * @return Money
     */
    public function default_price()
    {
        return new Money();
    }

    /**
     * Module caption for this payment type
     * 
     * @return string
     */
    public function get_module_caption()
    {
        $module_info = self::module_info($this->module);
        if (is_array($module_info))
        {
            return (isset($module_info['caption']) ? $module_info['caption'] : $module_info['module']);
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get id of all delivery types that this payment type support
     */
    public function get_delivery_ids()
    {
        if ( ! isset($this->_properties['delivery_ids']))
        {
            $delivery_ids = array();

            $deliveries = Model::fly('Model_Delivery')->find_all_by_payment($this, array('columns' => array('id')));
            foreach ($deliveries as $delivery)
            {
                $delivery_ids[$delivery->id] = TRUE;
            }

            $this->_properties['delivery_ids'] = $delivery_ids;
        }

        return $this->_properties['delivery_ids'];
    }

    /**
     * Save payment type and link it to selected delivery types
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);

        // Link payment type to the selected delivery types
        $delivery_ids = array_keys(array_filter($this->delivery_ids));
        Model_Mapper::factory('PaymentDelivery_Mapper')->link_payment_to_deliveries($this, $delivery_ids);
    }

    /**
     * Calculate payment price for specified order
     *
     * @param  Model_Order $order
     * @return float
     */
    public function calculate_price(Model_Order $order)
    {
        return $this->default_price();
    }
}