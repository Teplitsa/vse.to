<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Payments extends Controller_Backend
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
     * Index action - renders the list of payment types
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_payments());
    }

    /**
     * Display form for selecting the desired payment module
     * and redirect to the selected payment module create action upon form submit
     */
    public function action_create()
    {
        $form = new Form_Backend_CreatePayment();

        if ($form->is_submitted() && $form->validate())
        {
            // Redirect to the controller of the selected payment module
            $this->request->redirect(URL::uri_to('backend/payments', array(
                'controller' => 'payment_' . $form->get_value('module'),
                'action'     => 'create'
            ), TRUE));
        }

        $view = new View('backend/form');
        $view->caption = 'Добавление способа оплаты';
        $view->form = $form;

        $this->request->response = $this->render_layout($view);
    }

    /**
     * Renders list of available payment types
     *
     * @return string
     */
    public function widget_payments()
    {
        $payment = Model::fly('Model_Payment');

        $order_by = $this->request->param('pay_order', 'id');
        $desc = (bool) $this->request->param('pay_desc', '0');

        // Select all payment types
        $payments = $payment->find_all(array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/payments');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->payments = $payments;

        return $view->render();
    }
}