<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Coupons extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Coupon';
        $this->_form  = 'Form_Backend_Coupon';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание купона'
            ),
            'update' => array(
                'view_caption' => 'Редактирование купона'
            ),
            'delete' => array(
                'view_caption' => 'Удаление купона',
                'message' => 'Действительно удалить купон?'
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
        $layout->add_script(Modules::uri('discounts') . '/public/js/backend/coupon.js');
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
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Index action - renders the list of sites
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_coupons());
    }

    /**
     * Prepare model for action
     * 
     * @param  string $action
     * @param  string|Model_Coupon $model
     * @param  array $params
     * @return Model_Coupon
     */
    public function  _model($action, $model, array $params = NULL)
    {
        $model = parent::_model($action, $model, $params);

        // User can be changed via url parameter
        $user_id = $this->request->param('user_id', NULL);
        if ($user_id !== NULL)
        {
            $user_id = (int) $user_id;
            if (
                ($user_id == 0)
             || ($user_id > 0 && Model::fly('Model_User')->exists_by_id($user_id))
            )
            {
                $model->user_id = $user_id;
            }
        }

        return $model;
    }

    /**
     * Renders list of coupons
     *
     * @return string
     */
    public function widget_coupons()
    {
        $coupon = Model::fly('Model_Coupon');

        $order_by = $this->request->param('coupons_order', 'id');
        $desc = (bool) $this->request->param('coupons_desc', '1');

        // Select all coupons
        $coupons = $coupon->find_all(array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/coupons');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->coupons = $coupons;

        return $view->render();
    }
}