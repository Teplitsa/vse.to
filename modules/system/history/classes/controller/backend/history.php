<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_History extends Controller_Backend
{
    /**
     * Prepare layout
     * 
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->add_style(Modules::uri('history') . '/public/css/backend/history.css');

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
        $view->caption = 'Журнал';
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Index action - renders the history
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_history());
    }

    /**
     * Renders history
     *
     * @return string
     */
    public function widget_history()
    {
        $per_page = 10;
        
        $history = Model::fly('Model_History');

        $item_type = $this->request->param('item_type');

        if ($item_type != '')
        {
            $count      = $history->count_by_item_type($item_type);
            $pagination = new Pagination($count, $per_page);
            $entries = $history->find_all_by_item_type($item_type, array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => 'created_at',
                'desc'     => TRUE
            ));
        }
        else
        {
            $count      = $history->count();
            $pagination = new Pagination($count, $per_page);
            $entries = $history->find_all($item_type, array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => 'created_at',
                'desc'     => TRUE
            ));
        }


        // Set up view
        $view = new View('backend/history');

        $view->entries    = $entries;
        $view->pagination = $pagination;

        return $view->render();
    }
}