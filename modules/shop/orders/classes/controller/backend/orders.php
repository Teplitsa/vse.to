<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Orders extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Order';
        $this->_form  = 'Form_Backend_Order';
        $this->_view  = 'backend/form_adv';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание заказа',
            ),
            'update' => array(
                'view' => 'backend/order',
                'view_caption' => 'Редактирование заказа № :id',

                'subactions' => array('calc_delivery_price', 'calc_payment_price'),
                
                'redirect_uri' => URL::uri_self(array('user_id' => NULL))
            ),
            'delete' => array(
                'view_caption' => 'Удаление заказа',
                'message' => 'Удалить заказ № :id?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление заказов',
                'message' => 'Удалить выбранные заказы?'
            )
        );
    }

    /**
     * Prepare layout
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->add_script(Modules::uri('orders') . '/public/js/backend/order.js');

        $layout->add_style(Modules::uri('orders') . '/public/css/backend/orders.css');

        return $layout;
    }
    
    /**
     * Render layout
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        $view = new View('backend/workspace');
        $view->caption = 'Заказы';
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->content = $view;
        return $layout->render();
    }


    /**
     * Index action - renders the list of orders
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout(
            $this->widget_orders()
        );
    }

    /**
     * Calculate dilvery price subaction
     *
     * @param Model_Order $order
     * @param Form $form
     * @param array $params
     */
    public function _execute_calc_delivery_price(Model_Order $order, Form $form, array $params = NULL)
    {
        // Apply values to order (except delivery id and delivery price)
        $values = $form->get_values();
        unset($values['delivery_id']);
        unset($values['delivery_price']);
        $order->values($values);

        // Recalculate price & apply new value to form
        $delivery_price = $order->calc_delivery_price();
        $order->delivery_price = $delivery_price;
        
        $form->get_element('delivery_price')->value = $delivery_price;

        if ($order->has_errors())
        {
            $form->errors($order->errors());
        }
    }

    /**
     * Calculate payment price subaction
     *
     * @param Model_Order $order
     * @param Form $form
     * @param array $params
     */
    public function _execute_calc_payment_price(Model_Order $order, Form $form, array $params = NULL)
    {
        // Apply values to order (except delivery id and delivery price)
        $values = $form->get_values();
        unset($values['payment_id']);
        unset($values['payment_price']);
        $order->values($values);

        // Recalculate price & apply new value to form
        $payment = $order->calc_payment_price();
        $order->payment_price = $payment;

        $form->get_element('payment_price')->value = $payment;

        if ($order->has_errors())
        {
            $form->errors($order->errors());
        }
    }


    /**
     * Renders list of orders
     *
     * @return string
     */
    public function widget_orders()
    {
        $order = Model::fly('Model_Order');

        $order_by = $this->request->param('orders_order', 'id');
        $desc = (bool) $this->request->param('orders_desc', '1');

        $per_page = 25;

        $count = $order->count();
        $pagination = new Pagination($count, $per_page);
        
        // Select all products
        $orders = $order->find_all(array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/orders');

        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->pagination = $pagination;

        $view->orders = $orders;

        return $view->render();
    }

    /**
     * Generate redirect url
     * 
     * @param  string $action
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return string
     */
    protected function  _redirect_uri($action, Model $model = NULL, Form $form = NULL, array $params = NULL)
    {
        if ($action == 'create')
        {
            return URL::uri_to(
                'backend/orders',
                array('action'=>'update', 'id' => $model->id, 'history' => $this->request->param('history')), TRUE
            ) . '?flash=ok';
        }
        else
        {
            return parent::_redirect_uri($action, $model, $form, $params);
        }
    }

    /**
     * Prepare a view for order update action
     *
     * @param  Model $order
     * @param  Form $form
     * @param  array $params
     * @return View
     */
    protected function  _view_update(Model $order, Form $form, array $params = NULL)
    {
        $view = $this->_view('update', $order, $form, $params);

        // Render list of products in order
        $view->cartproducts = $this->request->get_controller('cartproducts')->widget_cartproducts($order);

        // Render list of comments for order
        $view->ordercomments = $this->request->get_controller('ordercomments')->widget_ordercomments($order);

        return $view;
    }
}