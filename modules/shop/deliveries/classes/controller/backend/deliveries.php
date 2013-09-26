<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Deliveries extends Controller_Backend
{
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
     * Index action - renders the list of delivery types
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_deliveries());
    }

    /**
     * Display form for selecting the desired delivery module
     * and redirect to the selected delivery module create action upon form submit
     */
    public function action_create()
    {
        $form = new Form_Backend_CreateDelivery();

        if ($form->is_submitted() && $form->validate())
        {
            // Redirect to the controller of the selected delivery module
            $this->request->redirect(URL::uri_to('backend/deliveries', array(
                'controller' => 'delivery_' . $form->get_value('module'),
                'action'     => 'create'
            ), TRUE));
        }

        $view = new View('backend/form');
        $view->caption = 'Добавление способа доставки';
        $view->form = $form;

        $this->request->response = $this->render_layout($view);
    }

    /**
     * Renders list of available delivery types
     *
     * @return string
     */
    public function widget_deliveries()
    {
        $delivery = Model::fly('Model_Delivery');

        $order_by = $this->request->param('pay_order', 'id');
        $desc = (bool) $this->request->param('pay_desc', '0');

        // Select all payment types
        $deliveries = $delivery->find_all(array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/deliveries');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->deliveries = $deliveries;

        return $view->render();
    }
}