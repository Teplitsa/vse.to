<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_OrderStatuses extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_OrderStatus';
        $this->_form  = 'Form_Backend_OrderStatus';

        return array(
            'create' => array(
                'view_caption' => 'Создание статуса заказа'
            ),
            'update' => array(
                'view_caption' => 'Редактирование статуса заказа ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление статуса заказа',
                'message' => 'Удалить статус заказа ":caption"?'
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
     * Index action - renders the list of order statuses
     */
    public function action_index()
    {
        $this->request->response =$this->render_layout($this->widget_statuses());
    }

    /**
     * Renders list of order statuses
     *
     * @return string
     */
    public function widget_statuses()
    {
        $status = Model::fly('Model_OrderStatus');

        $order_by = 'id';
        $desc     = FALSE;

        // Select all order statuses
        $statuses = $status->find_all(array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/orderstatuses');

        $view->order_by = $order_by;
        $view->desc     = $desc;

        $view->statuses = $statuses;

        return $view->render();
    }
}