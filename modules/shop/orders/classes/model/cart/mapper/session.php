<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cart_Mapper_Session
{
    /**
     * Get cart data from session
     */
    protected function _get_data()
    {
        return Session::instance()->get('cart', array());
    }

    /**
     * Write cart data to session
     */
    protected function _set_data($cart_data)
    {
        Session::instance()->set('cart', $cart_data);
    }

    /**
     * Save cart data
     * 
     * @param Model_Cart $cart
     */
    public function save(Model_Cart $cart)
    {
        $cart_data = array();
        
        foreach ($cart->cartproducts as $cartproduct)
        {
            $cart_data[$cartproduct->product_id] = $cartproduct->quantity;
        }

        $this->_set_data($cart_data);
    }

    /**
     * Get all products in cart with product info joined
     *
     * @param  Model_Cart $cart
     * @param  array $params
     * @return Models
     */
    public function get_cartproducts(Model_Cart $cart, array $params = NULL)
    {
        $cartproducts = new Models('Model_CartProduct', array(), 'product_id');
        
        $cart_data = $this->_get_data();
        if (empty($cart_data))
        {
            // No products in cart yet
            return $cartproducts;
        }
        
        $product     = new Model_Product();
        $cartproduct = new Model_CartProduct();

        $product_ids = array_keys($cart_data);
        //$params['with_images'] = TRUE;
        $products = Model::fly('Model_Product')->find_all_by(array(
            'ids' => $product_ids,
            'active' => 1,
            'section_active' => 1,
            'site_id' => Model_Site::current()->id
        ), $params);
        
        foreach ($products as $product)
        {
            if (isset($cart_data[$product->id]))
            {
                $quantity = $cart_data[$product->id];

                $cartproduct->from_product($product);
                $cartproduct->quantity = $quantity;
                    
                $cartproducts[$product->id] = $cartproduct;
            }
        }

        return $cartproducts;
    }
}