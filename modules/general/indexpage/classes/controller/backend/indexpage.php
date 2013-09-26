<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_IndexPage extends Controller_Backend
{
    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        $layout->caption = 'Главная страница';
        return $layout;
    }

    /**
     * Edit index page
     */
    public function action_update()
    {
        // Find page by given node id
        $page = new Model_Page();
        $page->find_by_node_id((int) $this->request->param('node_id'));

        if ( ! isset($page->id))
        {
            $this->_action_error('Указанная страница не найдена');
            return;
        }

        $view = new View('backend/panel');
        $view->caption = 'Главная страница';

        $view->content = 
            $this->request->get_controller('images')->widget_images('indexpage_' . $page->id, 'indexpage');

        $this->request->response = $this->render_layout($view);
    }
}
