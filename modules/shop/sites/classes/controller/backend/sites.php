<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Sites extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Site';
        $this->_form  = 'Form_Backend_Site';
        $this->_view  = 'backend/form_adv';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание сайта'
            ),
            'update' => array(
                'view_caption' => 'Редактирование настроек сайта ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление сайта',
                'message' => 'Удалить сайт ":caption"?'
            )
        );
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
        $this->request->response = $this->render_layout($this->widget_sites());
    }

    /**
     * Prepare site model for updating
     * 
     * @param  Model_Site|string $model
     * @param  array $params
     * @return Model_Site
     */
    public function  _model_update($model, array $params = NULL)
    {
        if ( ! Kohana::config('sites.multi'))
        {
           return Model_Site::current();
        }
        else
        {
            return parent::_model('update', $model, $params);
        }
    }

    /**
     * Renders list of sites
     *
     * @return string
     */
    public function widget_sites()
    {
        $site = Model::fly('Model_Site');

        $order_by = $this->request->param('sites_order', 'id');
        $desc = (bool) $this->request->param('sites_desc', '0');

        // Select all products
        $sites = $site->find_all();

        // Set up view
        $view = new View('backend/sites');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->sites = $sites;

        return $view->render();
    }

    /**
     * Site selection menu
     * 
     * @return string
     */
    public function widget_menu()
    {
        $site = Model::fly('Model_Site');

        // Select all products
        $sites = $site->find_all();

        // Set up view
        $view = new View('backend/sites_menu');

        $view->sites = $sites;
        $view->current = Model_Site::current();

        return $view->render();
    }
}