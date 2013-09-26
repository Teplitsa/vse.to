<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Links extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Link';
        $this->_form  = 'Form_Backend_Link';

        return array(
            'create' => array(
                'view_caption' => 'Создание внешней ссылки'
            ),
            'update' => array(
                'view_caption' => 'Редактирование внешней ссылки ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление внешней ссылки',
                'message' => 'Удалить ссылки ":caption"?'
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
        $this->request->response = $this->render_layout($this->widget_links());
    }

    /**
     * Create new user link
     *
     * @return Model_Link
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите портал перед созданием внешней ссылки!');
        }

        // New link for current site
        $link = new Model_Link();
        $link->site_id = (int) Model_Site::current()->id;

        return $link;
    }
    
    /**
     * Render list of user links
     */
    public function widget_links()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        $order_by = $this->request->param('acl_uliorder', 'position');
        $desc = (bool) $this->request->param('acl_ulidesc', '0');

        $links = Model::fly('Model_Link')->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc' => $desc
        ));
        // Set up view
        $view = new View('backend/links');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->links = $links;

        return $view->render();
    }    
}