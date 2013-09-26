<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Pages extends Controller_BackendCRUD
{
    /**
     * Configure actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Page';
        $this->_form  = 'Form_Backend_Page';

        return array(
            'update' => array(
                'view_caption' => 'Редактирование страницы ":caption"'
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
     * Prepare page model for update action
     *
     * @param  string|Model $page
     * @param  array $params
     * @return Model
     */
    protected function _model_update($page, array $params = NULL)
    {
        // Find page by given node id
        $page = new Model_Page();
        $page->find_by_node_id((int) $this->request->param('node_id'));
        
        if ( ! isset($page->id))
        {
            throw new Controller_BackendCRUD_Exception('Текстовая страница не найдена');
        }
        
        return $page;
    }
}
