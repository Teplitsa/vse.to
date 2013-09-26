<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Dialogs extends Controller_FrontendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_Dialog';

        return array(
            'delete' => array(
                'view_caption' => 'Удаление диалога',
                'message' => 'Удалить диалог?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление диалогов',
                'message' => 'Удалить выбранные диалоги?'
            )
        );
    }
    /**
     * Prepare layout
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('chat') . '/public/css/frontend/chat.css');
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
        $view = new View('frontend/workspace');
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->content = $view;
        return $layout->render();
    }
    
    /**
     * Render list of dialogs
     */
    public function action_index()
    {
        $view = new View('frontend/workspace');

        $view->content = $this->widget_dialogs();
                
        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }
    /**
     * Renders list of dialogs
     *
     * @return string Html
     */
    public function widget_dialogs()
    {
        $per_page = 5;
        
        $dialog = new Model_Dialog();

        $count      = $dialog->count();

        $pagination = new Pagination($count, $per_page);

        $dialogs = $dialog->find_all(array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,            
            'order_by' => 'id',
            'desc' => '1'
        ));

        $view = new View('frontend/dialogs');

        $view->dialogs = $dialogs;
        $view->pagination = $pagination->render('pagination');
        return $view->render();
    }
    
    /**
     * Add breadcrumbs for current action
     */
    public function add_breadcrumbs(array $request_params = array())
    {
        if (empty($request_params)) {
            list($name, $request_params) = URL::match(Request::current()->uri);
        }
        
        Breadcrumbs::append(array(
            'uri' => URL::uri_to('frontend/dialogs'),
            'caption' => 'Сообщения'
        ));
    }    
}