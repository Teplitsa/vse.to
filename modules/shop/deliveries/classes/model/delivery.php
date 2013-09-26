<?php defined('SYSPATH') or die('No direct script access.');

class Model_Delivery extends Model
{
    /**
     * Registered delivery modules
     * @var array
     */
    protected static $_modules = array();

    /**
     * Register delivery module
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
     * Get registered delivery modules
     * 
     * @return array
     */
    public static function modules()
    {
        return self::$_modules;
    }

    /**
     * Get delivery type caption by id
     * 
     * @param integer $id
     */
    public static function caption($id)
    {
        // Obtain all deliveries (CACHED!)
        $deliveries = Model::fly('Model_Delivery')->find_all(array('key' => 'id'));
        
        if (isset($deliveries[$id]))
        {
            return $deliveries[$id]->caption;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * All delivery models use the same mapper
     * 
     * @param  string $mapper
     * @return Model_Delivery_Mapper
     */
    public function mapper($mapper = NULL)
    {
        if ($mapper !== NULL || $this->_mapper !== NULL)
        {
            return parent::mapper($mapper);
        }

        // All payment models uses Model_Delivery_Mapper as mapper
        $this->_mapper = Model_Mapper::factory('Model_Delivery_Mapper');

        return $this->_mapper;
    }

    /**
     * Determine delivery module from model class name
     * 
     * @return string
     */
    public function default_module()
    {
        $class = get_class($this);

        if (strpos($class, 'Model_Delivery_') === 0)
        {
            return strtolower(substr($class, strlen('Model_Delivery_')));
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Default delivery price
     * 
     * @return Money
     */
    public function default_price()
    {
        return new Money();
    }

    /**
     * Module caption for this delivery type
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
     * Get id of all payment types that this delivery type support
     */
    public function get_payment_ids()
    {
        if ( ! isset($this->_properties['payment_ids']))
        {
            $payment_ids = array();

            $payments = Model::fly('Model_Payment')->find_all_by_delivery($this, array('columns' => array('id')));
            foreach ($payments as $payment)
            {
                $payment_ids[$payment->id] = TRUE;
            }

            $this->_properties['payment_ids'] = $payment_ids;
        }

        return $this->_properties['payment_ids'];
    }
    
    /**
     * Save payment type and link it to selected delivery types
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        parent::save($force_create);

        // Link delivery type to the selected payment types
        $payment_ids = array_keys(array_filter($this->payment_ids));
        Model_Mapper::factory('PaymentDelivery_Mapper')->link_delivery_to_payments($this, $payment_ids);
    }


    /**
     * Calculate delivery price for specified order
     * 
     * @param  Model_Order $order
     * @return float
     */
    public function calculate_price(Model_Order $order)
    {
        return $this->default_price();
    }
}