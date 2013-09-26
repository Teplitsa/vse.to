<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_UserProps extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_UserProp';
        $this->_form  = 'Form_Backend_UserProp';

        return array(
            'create' => array(
                'view_caption' => 'Создание характеристики'
            ),
            'update' => array(
                'view_caption' => 'Редактирование характеристики ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление характеристики',
                'message' => 'Удалить характеристику ":caption"?'
            )
        );
    }


    /**
     * Create layout (proxy to acl controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('acl')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to acl controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('acl')->render_layout($content, $layout_script);
    }

    /**
     * Render all available user properties
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_userprops());
    }

    /**
     * Create new user property
     *
     * @return Model_UserProp
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите портал перед созданием характеристики!');
        }

        // New userprop for current site
        $userprop = new Model_UserProp();
        $userprop->site_id = (int) Model_Site::current()->id;

        return $userprop;
    }

    /**
     * Render list of user properties
     */
    public function widget_userprops()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        $order_by = $this->request->param('acl_uprorder', 'position');
        $desc = (bool) $this->request->param('acl_uprdesc', '0');

        $userprops = Model::fly('Model_UserProp')->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc' => $desc
        ));
        // Set up view
        $view = new View('backend/userprops');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->userprops = $userprops;

        return $view->render();
    }
}