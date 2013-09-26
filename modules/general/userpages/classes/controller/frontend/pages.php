<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Pages extends Controller_FrontendCRUD
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
     * Display a text page
     */
    public function action_view()
    {
        // Current node
        $node = Model_Node::current();
        if ( ! isset($node->id) || ! $node->active)
        {
            $this->_action_404('Страница не найдена');
            return;
        }

        // Find page for current node
        $page = new Model_Page();
        $page->find_by_node_id($node->id);

        if ( ! isset($page->id))
        {
            $this->_action_404('Страница не найдена');
            return;
        }

        $this->request->response = $this->render_layout($page->content);
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
            throw new Controller_FrontendCRUD_Exception('Текстовая страница не найдена');
        }
        
        return $page;
    }
    
}
