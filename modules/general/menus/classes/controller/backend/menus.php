<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Menus extends Controller_BackendCRUD
{
    /**
     * Setup actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Menu';
        $this->_form  = 'Form_Backend_Menu';

        return array(
            'create' => array(
                'view_caption' => 'Создание меню'
            ),
            'update' => array(
                'view_caption' => 'Редактирование меню ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление меню',
                'message' => 'Удалить меню ":caption"?'
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

        if ($this->request->is_forwarded())
            // Request was forwarded in parent::before()
            return TRUE;

        // Check that there is a site selected
        if (Model_Site::current()->id === NULL)
        {
            $this->_action_error('Выберите сайт для работы с меню!');
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
     * Default action
     */
	public function action_index()
	{
            $this->request->response = $this->render_layout($this->widget_menus());
	}

    /**
     * Renders menu list
     *
     * @return string
     */
    public function widget_menus()
    {
        $site_id = (int) Model_Site::current()->id;
        
        $menu = new Model_Menu();

        $order_by = $this->request->param('menus_order', 'id');
        $desc     = (bool) $this->request->param('menus_desc', '0');

        // Select all menus for current site
        $site_id = Model_Site::current()->id;
        $menus = $menu->find_all_by_site_id($site_id, array('order_by' => $order_by, 'desc' => $desc));

        // Set up view
        $view = new View('backend/menus');

        $view->order_by = $order_by;
        $view->desc     = $desc;

        $view->menus = $menus;

        return $view;
    }
}
