<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Flashblocks extends Controller_BackendCRUD
{
    /**
     * Setup actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Flashblock';
        $this->_form  = 'Form_Backend_Flashblock';
        $this->_view  = 'backend/form_adv';

        return array(
            'create' => array(
                'view_caption' => 'Создание flash блока'
            ),
            'update' => array(
                'view_caption' => 'Редактирование flash блока ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление flash блока',
                'message' => 'Удалить flash блок ":caption"?'
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
            $this->_action_error('Выберите сайт для работы с flash блоками!');
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
        $this->request->response = $this->render_layout($this->widget_flashblocks());
	}

    /**
     * Renders blocks list
     *
     * @return string
     */
    public function widget_flashblocks()
    {
        $site_id = (int) Model_Site::current()->id;
        
        $flashblock = new Model_Flashblock();

        $order_by = $this->request->param('flashblocks_order', 'position');
        $desc     = (bool) $this->request->param('flashblocks_desc', '0');

        // Select all menus for current site
        $flashblocks = $flashblock->find_all_by_site_id($site_id, array('order_by' => $order_by, 'desc' => $desc));

        // Set up view
        $view = new View('backend/flashblocks');

        $view->order_by = $order_by;
        $view->desc     = $desc;

        $view->flashblocks = $flashblocks;

        return $view;
    }
}
