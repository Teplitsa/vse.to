<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Nodes extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Node';
        $this->_form  = 'Form_Backend_Node';
        $this->_view  = 'backend/form_adv';
        
        return array(
            'create' => array(
                'view_caption' => 'Создание страницы'
            ),
            'update' => array(
                'view_caption' => 'Редактирование страницы ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление страницы',
                'message' => 'Удалить страницу ":caption" и все подстраницы?'
            )
        );
    }

    /**
     * @return boolean
     */
    public function before()
    {
        if ( ! parent::before())
        {
            return FALSE;
        }

        // Check that there is a site selected
        if (Model_Site::current()->id === NULL)
        {
            $this->_action_error('Выберите сайт для работы со страницами!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('nodes') . '/public/css/backend/nodes.css');
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
     * Render nodes tree
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_nodes());
    }

    /**
     * Create new node
     *
     * @param  string|Model $model
     * @param  array $params
     * @return Model_Node
     */
    protected function _model_create($model, array $params = NULL)
    {
        // New node (as child of current node)
        $node = new Model_Node();
        $node->parent_id = (int) $this->request->param('node_id');

        return $node;
    }

    /**
     * Delete node
     */
    protected function _execute_delete(Model $node, Form $form, array $params = NULL)
    {
        list($hist_route, $hist_params) = URL::match(URL::uri_back());

        // Id of selected node
        $node_id = isset($hist_params['node_id']) ? (int) $hist_params['node_id'] : 0;

        if ($node->id === $node_id || $node->is_parent_of($node_id))
        {
            // If current selected section or its parent is deleted - redirect back to root
            unset($hist_params['node_id']);
            $params['redirect_uri'] = URL::uri_to($hist_route, $hist_params);
        }
        
        parent::_execute_delete($node, $form, $params);
    }

    /**
     * Renders tree of nodes
     *
     * @return string
     */
    public function widget_nodes()
    {
        $site_id = Model_Site::current()->id;
        
        $node = new Model_Node();

        $order_by = $this->request->param('nodes_order', 'lft');
        $desc     = (bool) $this->request->param('nodes_desc', '0');
        $node_id  = (int) $this->request->param('node_id');

        $nodes = $node->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/nodes');

        $view->order_by = $order_by;
        $view->desc     = $desc;
        $view->node_id  = $node_id;

        $view->nodes = $nodes;

        return $view->render();
    }

    /**
     * Renders nodes menu (site structure)
     */
    public function widget_menu()
    {
        $site_id = Model_Site::current()->id;
        
        if ($site_id === NULL)
            // Current site is not selected
            return;

        $node = new Model_Node();

        $order_by = $this->request->param('nodes_menu_order', 'lft');
        $desc     = (bool) $this->request->param('nodes_menu_desc', '0');
        $node_id  = (int) $this->request->param('node_id');

        $nodes = $node->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        // Set up view
        $view = new View('backend/nodes_menu');

        $view->order_by = $order_by;
        $view->desc     = $desc;
        $view->node_id  = $node_id;

        $view->nodes = $nodes;

        // Wrap into panel
        $panel = new View('backend/panel');
        $panel->caption = 'Страницы сайта';
        $panel->content = $view;

        return $panel->render();
    }
}
