<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_PLists extends Controller_BackendCRUD
{
    /**
     * Configure actions
     * @var array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_PList';
        $this->_form  = 'Form_Backend_PList';

        return array(
            'create' => array(
                'view_caption' => 'Создание списка товаров'
            ),
            'update' => array(
                'view' => 'backend/plist',
                'view_caption' => 'Редактирование списка товаров ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление списка товаров',
                'message' => 'Удалить список товаров ":caption"?'
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
        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('catalog') . '/public/css/backend/catalog.css');
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
     * Render all available lists of products
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_plists());
    }

    /**
     * Create new list of products
     *
     * @return Model_PList
     */
    protected function _model_create($model, array $params = NULL)
    {
        if (Model_Site::current()->id === NULL)
        {
            throw new Controller_BackendCRUD_Exception('Выберите магазин для создания списка товаров!');
        }

        // New list of products for the selected site
        $plist = new Model_PList();
        $plist->site_id = (int) Model_Site::current()->id;

        return $plist;
    }

    /**
     * Generate redirect url
     *
     * @param  string $action
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return string
     */
    protected function  _redirect_uri($action, Model $model = NULL, Form $form = NULL, array $params = NULL)
    {
        if ($action == 'create')
        {
            return URL::uri_to(
                'backend/catalog/plists',
                array('action'=>'update', 'id' => $model->id, 'history' => $this->request->param('history')), TRUE
            ) . '?flash=ok';
        }
        else
        {
            return parent::_redirect_uri($action, $model, $form, $params);
        }
    }

    /**
     * Prepare a view for product list update action
     *
     * @param  Model $plist
     * @param  Form $form
     * @param  array $params
     * @return View
     */
    protected function  _view_update(Model $plist, Form $form, array $params = NULL)
    {
        $view = $this->_view('update', $plist, $form, $params);

        // Render list of products in list
        $view->plistproducts = $this->request->get_controller('plistproducts')->widget_plistproducts($plist);

        return $view;
    }
    
    /**
     * Render list of product lists
     */
    public function widget_plists()
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите магазин!');
        }

        $order_by = $this->request->param('cat_lorder', 'id');
        $desc = (bool) $this->request->param('cat_ldesc', '0');

        $plists = Model::fly('Model_PList')->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc' => $desc
        ));

        // Set up view
        $view = new View('backend/plists');

        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->plists = $plists;

        return $view->render();
    }
}