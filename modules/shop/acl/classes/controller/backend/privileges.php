<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Privileges extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Privilege';
        $this->_form  = 'Form_Backend_Privilege';

        return array(
            'create' => array(
                'view_caption' => 'Создание привилегии'
            ),
            'update' => array(
                'view_caption' => 'Редактирование привилегии ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление привилегии',
                'message' => 'Удалить привилегию ":caption"?'
            )
        );
    }


    /**
     * Create layout (proxy to catalog controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('catalog')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to catalog controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('catalog')->render_layout($content, $layout_script);
    }

    /**
     * Render all available section properties
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_privileges());
    }

    /**
     * Create new property
     *
     * @return Model_Property
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите портал перед созданием привилегии!');
        }

        // New section for current site
        $privilege = new Model_Privilege();
        $privilege->site_id = (int) Model_Site::current()->id;

        return $privilege;
    }

    /**
     * Render list of section properties
     */
    public function widget_privileges()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        $order_by = $this->request->param('acl_prorder', 'position');
        $desc = (bool) $this->request->param('acl_prdesc', '0');

        $privileges = Model::fly('Model_Privilege')->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc' => $desc
        ));

        // Set up view
        $view = new View('backend/privileges');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->privileges = $privileges;

        return $view->render();
    }
}