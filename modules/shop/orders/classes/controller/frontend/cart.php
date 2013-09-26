<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Cart extends Controller_Frontend
{
    /**
     * Prepare layout
     * 
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        //$layout->add_script(Modules::uri('orders') . '/public/js/frontend/cart.js');
        return $layout;
    }
    
    /**
     * Add product to cart
     */
    public function action_add_product()
    {
        $product_id = (int) $this->request->param('product_id');

        $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

        $cart = Model_Cart::session();
        $product = $cart->add_product($product_id, $quantity);

        if ($cart->has_errors())
        {
            FlashMessages::add_many($cart->errors());
        }
        else
        {
            FlashMessages::add('Товар "' . $product->caption . '" добавлен в корзину');
        }

        if ( ! Request::$is_ajax)
        {
            $this->request->redirect(URL::uri_back());
        }
        else
        {
            $request = Widget::switch_context();

            if ( ! $cart->has_errors())
            {
                // "add to cart" button
                $request->get_controller('products')
                    ->widget_add_to_cart($product, $this->request->param('widget_style'))
                    ->to_response($this->request);

                // cart summary
                $request->get_controller('cart')
                    ->widget_summary()
                    ->to_response($this->request);
            }

            // encode response for an ajax request
            $this->_action_ajax();
        }
    }

    /**
     * Remove product from cart
     */
    public function action_remove_product()
    {
        $product_id = (int) $this->request->param('product_id');
        
        $cart = Model_Cart::session();
        $cart->remove_product($product_id);

        if ( ! Request::$is_ajax)
        {
            $this->request->redirect(URL::uri_back());
        }
        else
        {
            // Redraw corresponding widgets
            $request = Widget::switch_context();

            // cart contents
            $request->get_controller('cart')
                ->widget_cart()
                ->to_response($this->request);

            // cart summary
            $request->get_controller('cart')
                ->widget_summary()
                ->to_response($this->request);

            // encode response for an ajax request
            $this->_action_ajax();
        }
    }

    /**
     * Render cart contents
     */
    public function action_index()
    {
        Breadcrumbs::append(array('uri' => $this->request->uri, 'caption' => 'Корзина'));
        
        $this->request->response = $this->render_layout($this->widget_cart());
    }

    /**
     * Render cart summary
     * 
     * @return Widget
     */
    public function widget_summary()
    {
        $widget = new Widget('frontend/cart/summary');
        $widget->id = 'cart_summary';

        $widget->cart = Model_Cart::session();
        
        return $widget;
    }

    /**
     * Render full cart contents
     * 
     * @return Widget
     */
    public function widget_cart()
    {
        $cart = Model_Cart::session();
        
        $form = new Form_Frontend_Cart();

        // Get cart (from session or from post data)
        // & initialize cart form using products from cart
        if ($form->is_submitted())
        {
            $quantities = $form->get_post_data('quantities');
            $form->init_fields($quantities);
        }
        else
        {            
            $form->init_fields($cart->quantities);
        }        

        /*
        if ($form->is_submitted() && $form->get_value('recalculate'))
        {
            if ($form->validate())
            {
                // Update cart contents
               $cart->update_products_from_post();
            }
        }
        */

        if ($form->is_submitted() && $form->validate())
        {
            // Proceed to checkout
            $values = $form->get_values();
            $cart->update_products($values['quantities']);

            if ( ! $cart->has_errors())
            {
                $serialized = $cart->serialize();
                $uri = URL::uri_to('frontend/orders', array('action' => 'checkout', 'cart' => $serialized));
                $this->request->redirect($uri);
            }
            else
            {
                // An error occured while updating cart contents
                $form->errors($cart->errors());
            }
        }
       
        $widget = new Widget('frontend/cart/contents');
        $widget->id = 'cart';

        $widget->cart = $cart;
        $widget->form = $form;
        $widget->cartproducts = $cart->cartproducts;
        
        return $widget;
    }
}