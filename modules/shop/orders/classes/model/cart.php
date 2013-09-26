<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cart extends Model
{
    /**
     * Maximum number of distinct products that can be but into the cart
     */
    const MAX_DISTINCT_COUNT = 500;

    /**
     * Maximum allowed quantity for the product in cart
     */
    const MAX_QUANTITY = 10;

    /**
     * @var Model_Cart
     */
    protected static $_instance;

    /**
     * Get cart instance
     *
     * @return Model_Cart
     */
    public static function instance()
    {
        if (self::$_instance === NULL)
        {
            $cart = new Model_Cart();
            self::$_instance = $cart;
        }
        return self::$_instance;
    }

    /**
     * Get cart instance with session mapper
     *
     * @return Model_Cart
     */
    public static function session()
    {
        $cart = self::instance();
        $cart->mapper('Model_Cart_Mapper_Session');
        return $cart;
    }

    /**
     * @param array $properties
     */
    public function  __construct(array $properties = array())
    {
        $this->mapper('Model_Cart_Mapper_Db');        
        parent::__construct($properties);
    }
    
    /**
     * Add a product to cart by id
     * 
     * @param  integer $product_id
     * @param  integer $quantity
     * @return Model_Product
     */
    public function add_product($product_id, $quantity = 1)
    {
        if ( ! preg_match('/^\d+$/', $quantity))
        {
            $this->error('Количество товара указано неверно!');
            return;
        }

        $quantity = (int) $quantity;
        if ($quantity == 0)
        {
            $this->error('Вы не указали количество товара');
            return;
        }
        
        if ($this->distinct_count >= self::MAX_DISTINCT_COUNT)
        {
            $this->error('Максимальное количество различных товаров в корзине не должно превышать ' . self::MAX_DISTINCT_COUNT);
            return;
        }
        
        $product = new Model_Product();
        $product->find_by(array(
            'id' => $product_id,
            'active' => 1,
            'section_active' => 1,
            'site_id' => Model_Site::current()->id
        ));
        if ( ! isset($product->id))
        {
            $this->error('Указанный товар не найден');
            return;
        }

        $cartproducts = $this->cartproducts;

        if ( ! isset($cartproducts[$product->id]))
        {
            // Add new product to cart
            $cartproduct = new Model_CartProduct();
            $cartproduct->from_product($product);

            $new_quantity = $quantity;
        }
        else
        {
            // Update quantity for product already in cart
            $cartproduct = $cartproducts[$product->id];
            $new_quantity = $cartproduct->quantity + $quantity;
        }

        // Validate quantity
        if ($new_quantity > self::MAX_QUANTITY)
        {
            $new_quantity = self::MAX_QUANTITY;
        }
        /*
        if ($new_quantity > $product->quantity)
        {
            $new_quantity = $product->quantity;
        }
        */

        // Update quantity
        $cartproduct->quantity = $new_quantity;
        $cartproducts[$product->id] = $cartproduct;

        // Update cartproducts info
        $this->save($this);

        // Invalidate possibly cached properties
        unset($this->_properties['cartproducts']);
        unset($this->_properties['sum']);

        return $product;
    }

    /**
     * Remove product from cart
     * 
     * @param integer $product_id
     */
    public function remove_product($product_id)
    {
        $cartproducts = $this->cartproducts;
        unset($cartproducts[$product_id]);
        $this->mapper()->save($this);
    }

    /**
     * Check if product with this id is in cart
     * 
     * @param integer $product_id
     */
    public function has_product($product_id)
    {
        $cartproducts = $this->cartproducts;
        return (isset($cartproducts[$product_id]));
    }

    /**
     * Update products in cart with supplied values (FRONTEND)
     *
     * @param  array $quantities array(product_id=>quantity)
     * @return boolean
     */
    public function update_products(array $quantities, $save = TRUE)
    {
        $cartproducts = array();

        $count = 0;
        $product = new Model_Product();
        foreach ($quantities as $product_id => $quantity)
        {
            if ($this->distinct_count >= self::MAX_DISTINCT_COUNT)
            {
                $this->error('Максимальное количество различных товаров в корзине не должно превышать ' . self::MAX_DISTINCT_COUNT);
                return;
            }

            $product->find_by(array(
                'id' => $product_id,
                'active' => 1,
                'section_active' => 1,
                'site_id' => Model_Site::current()->id
            ));
            if ( ! isset($product->id))
            {
                $this->error('Товар с идентификатором ' . $product_id . ' не найден в каталоге');
                continue;
            }

            $quantity = (int) $quantity;

            if ( ! isset($cartproducts[$product->id]))
            {
                // Add the product for the first time
                $cartproduct = new Model_CartProduct();
                $cartproduct->from_product($product);

                $new_quantity = $quantity;
            }
            else
            {
                // Product has already been added (duplicate values for product_id)
                $cartproduct = $cartproducts[$product->id];
                $new_quantity = $cartproduct->quantity + $quantity;
            }

            // Validate quantity
            if ($new_quantity > self::MAX_QUANTITY)
            {
                $new_quantity = self::MAX_QUANTITY;
            }
            /*
            if ($new_quantity > $product->quantity)
            {
                $new_quantity = $product->quantity;
            }
            */

            $cartproduct->quantity = $new_quantity;
            $cartproducts[$product->id] = $cartproduct;

        }
        
        if ( ! $this->has_errors())
        {
            $this->cartproducts = $cartproducts;
            if ($save)
            {
                // Save cart products to session
                $this->save();
            }
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Remove all products from cart
     */
    public function clean()
    {
        $this->cartproducts = array();
        $this->save();
    }

    /**
     * Find all products in cart
     *
     * @param  array $params
     * @return Models (of Model_CartProduct)
     */
    public function get_cartproducts(array $params = NULL)
    {
        if ( ! isset($this->_properties['cartproducts']))
        {
            $this->_properties['cartproducts'] = $this->mapper()->get_cartproducts($this, $params);
        }
        return $this->_properties['cartproducts'];
    }

    /**
     * Get quantities of products in cart in format array(product_id=>quantity)
     * 
     * @return array
     */
    public function get_quantities()
    {
        $quantities = array();
        foreach ($this->cartproducts as $cartproduct)
        {
            $quantities[$cartproduct->product_id] = $cartproduct->quantity;
        }
        return $quantities;
    }

    /**
     * Serialize cart contents to a string
     */
    public function serialize()
    {
        $str = '';
        foreach ($this->quantities as $product_id => $quantity)
        {
            $str .= $product_id . '_' . $quantity . '-';
        }
        return trim($str, '-');
    }

    /**
     * Set cart contents form a serialized string
     *
     * @param  string $str
     * @return boolean
     */
    public function unserialize($str)
    {
        $quantities = array();
        foreach (explode('-', $str) as $pair)
        {
            $product_id = (int) strtok($pair, '_');
            $quantity   = (int) strtok('_');
            
            $quantities[$product_id] = $quantity;
        }
        return $this->update_products($quantities, FALSE);
    }

    /**
     * Get the total price of all products in cart - without any discounts
     *
     * @return Money
     */
    public function calculate_sum()
    {
        $sum = new Money();
        foreach ($this->cartproducts as $cartproduct)
        {
            $sum->add($cartproduct->price->mul($cartproduct->quantity));
        }
        return $sum;
    }

    /**
     * Getter for sum of all products in cart
     * @return Money
     */
    public function get_sum()
    {
        if ( ! isset($this->_properties['sum']))
        {
            $this->_properties['sum'] = $this->calculate_sum();
        }
        return $this->_properties['sum'];
    }

    /**
     * Calculate overall cart price with all discounts appliedt
     */
    public function get_total_sum()
    {
        return $this->sum;
    }

    /**
     * Get the total number of all products in cart
     *
     * @return integer
     */
    public function get_total_count()
    {
        $total_count = 0;
        
        foreach ($this->cartproducts as $cartproduct)
        {
            $total_count += $cartproduct->quantity;
        }

        return $total_count;
    }

    /**
     * Get the number of distinct products in cart
     * 
     * @return integer
     */
    public function get_distinct_count()
    {
        return count($this->cartproducts);
    }
}