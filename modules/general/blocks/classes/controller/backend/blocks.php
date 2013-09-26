<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Blocks extends Controller_BackendCRUD
{
    /**
     * Setup actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Block';
        $this->_form  = 'Form_Backend_Block';
        $this->_view  = 'backend/form_adv';

        return array(
            'create' => array(
                'view_caption' => 'Создание блока'
            ),
            'update' => array(
                'view_caption' => 'Редактирование блока ":caption"'
            ),
            'delete' => array(
                'view_caption' => 'Удаление блока',
                'message' => 'Удалить блок ":caption"?'
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
            $this->_action_error('Выберите сайт для работы с блоками!');
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
        $this->request->response = $this->render_layout($this->widget_blocks());
	}

    /**
     * Renders blocks list
     *
     * @return string
     */
    public function widget_blocks()
    {
        $site_id = (int) Model_Site::current()->id;
        
        $block = new Model_Block();

        $order_by = $this->request->param('blocks_order', 'position');
        $desc     = (bool) $this->request->param('blocks_desc', '0');

        // Select all menus for current site
        $blocks = $block->find_all_by_site_id($site_id, array('order_by' => $order_by, 'desc' => $desc));

        // Set up view
        $view = new View('backend/blocks');

        $view->order_by = $order_by;
        $view->desc     = $desc;

        $view->blocks = $blocks;

        return $view;
    }
}
