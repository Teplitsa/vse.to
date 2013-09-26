<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_CartProducts extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_CartProduct';
        $this->_form  = 'Form_Backend_CartProduct';
        $this->_view  = 'backend/form';
        
        return array(
            'create' => array(
                'view_caption' => 'Добавление товара в заказ № :order_id'
            ),
            'update' => array(
                'view_caption' => 'Редактирование товара в заказе'
            ),
            'delete' => array(
                'view_caption' => 'Удаление товара из заказа',
                'message' => 'Удалить товар из заказа № :order_id?'
            )
        );
    }

    /**
     * Create layout (proxy to orders controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('orders')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to orders controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('orders')->render_layout($content, $layout_script);
    }

    /**
     * Prepare cartproduct model for create action
     *
     * @param  string|Model_CartProduct $cartproduct
     * @param  array $params
     * @return Model_CartProduct
     */
    protected function  _model_create($cartproduct, array $params = NULL)
    {
        $cartproduct = parent::_model('create', $cartproduct, $params);

        $order_id = (int) $this->request->param('order_id');
        if ( ! Model::fly('Model_Order')->exists_by_id($order_id))
        {
            throw new Controller_BackendCRUD_Exception('Указанный заказ не существует!');
        }

        $cartproduct->cart_id = $order_id;

        // Set defaults from actual product in catalog
        $product = new Model_Product();
        $product->find((int) $this->request->param('product_id'));
        if (isset($product->id))
        {
            $cartproduct->from_product($product);
        }
        
        return $cartproduct;
    }

    /**
     * Renders list of order products
     *
     * @param  Model_Order $order
     * @return string
     */
    public function widget_cartproducts($order)
    {
        $cartproducts = $order->products;

        // Set up view
        $view = new View('backend/cartproducts');

        $view->order         = $order;
        $view->cartproducts = $cartproducts;

        return $view->render();
    }
}