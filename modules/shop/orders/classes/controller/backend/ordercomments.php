<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_OrderComments extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_OrderComment';
        $this->_form  = 'Form_Backend_OrderComment';
        $this->_view  = 'backend/form';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание комментария к заказу № :order_id'
            ),
            'update' => array(
                'view_caption' => 'Редактирование комментария к заказу'
            ),
            'delete' => array(
                'view_caption' => 'Удаление комментария к заказу',
                'message' => 'Удалить комментарий к заказу № :order_id?'
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
     * Prepare ordercomment model for create action
     *
     * @param  string|Model_OrderComment $ordercomment
     * @param  array $params
     * @return Model_OrderComment
     */
    protected function  _model_create($ordercomment, array $params = NULL)
    {
        $ordercomment = parent::_model('create', $ordercomment, $params);

        $order_id = (int) $this->request->param('order_id');
        if ( ! Model::fly('Model_Order')->exists_by_id($order_id))
        {
            throw new Controller_BackendCRUD_Exception('Указанный заказ не существует!');
        }

        $ordercomment->order_id = $order_id;

        return $ordercomment;
    }

    /**
     * Renders list of order comments
     *
     * @param  Model_Order $order
     * @return string
     */
    public function widget_ordercomments($order)
    {
        $ordercomments = $order->comments;

        // Set up view
        $view = new View('backend/ordercomments');

        $view->order         = $order;
        $view->ordercomments = $ordercomments;

        return $view->render();
    }
}