<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Indexpage extends Controller_Frontend
{
    /**
     * Display an index page
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

        // Find all images for this page
        $owner = 'indexpage_' . $page->id;
        $images = Model::fly('Model_Image')->find_all_by_owner($owner);

        $layout = $this->prepare_layout();
        $layout->images = $images;
        $this->request->response = $layout->render();
    }
}
