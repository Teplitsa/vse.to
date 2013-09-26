<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Orders extends Controller_Frontend
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

        if ($this->request->action == 'checkout_success')
        {
            $layout->caption = 'Ваш заказ принят в обработку!';
        }
        else
        {
            $layout->caption = 'Оформление заказа';
        }
        
        Breadcrumbs::append(array('uri' => URL::uri_to('frontend/orders'), 'caption' => 'Оформление заказа'));
        
        return $layout;
    }
    /**
     * Checkout
     */
    public function action_checkout()
    {
        $cart = Model_Cart::session();
        
        $serialized = $this->request->param('cart');
        if ($serialized != '')
        {
            $cart->unserialize($serialized);
        }

        if ($cart->has_errors())
        {
            FlashMessages::add_many($cart->errors());
            $this->request->redirect(URL::uri_to('frontend/cart'));
        }

        $form = new Form_Frontend_Checkout();

        if ($form->is_submitted() && $form->validate())
        {
            if ( ! count($cart->cartproducts))
            {
                $form->error('В корзине нет товаров');
            }
            else
            {
                $order = new Model_Order();

                $order->values($form->get_values());
                $order->products = $cart->cartproducts;
                $order->save();

                $cart->clean();

                // Send email notifications
                $order->notify();

                $this->request->redirect(URL::uri_to('frontend/orders', array('action' => 'checkout_success')));
            }
        }

        $view = new View('frontend/checkout');
        $view->form = $form;
        $this->request->response = $this->render_layout($view);
    }

    /**
     * New order was created succesfully
     */
    public function action_checkout_success()
    {
        $this->request->response = $this->render_layout(Widget::render_widget('blocks', 'block', 'order_succ'));
    }

}