<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Organizers extends Controller_BackendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_Organizer';
        $this->_form  = 'Form_Backend_Organizer';
        $this->_view  = 'backend/form_adv';        

        return array(
            'create' => array(
                'view_caption' => 'Создание организации'
            ),
            'update' => array(
                'view_caption' => 'Редактирование организации',
                
            ),
            'delete' => array(
                'view_caption' => 'Удаление организации',
                'message' => 'Удалить организацию ":name" '
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление организаций',
                'message' => 'Удалить выбранные организации?'
            )
        );
    }

    /**
     * Create layout (proxy to acl controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {   
        return $this->request->get_controller('acl')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to acl controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('acl')->render_layout($content, $layout_script);
    }

    /**
     * Render all available section properties
     */
    public function action_index()
    {
        $this->request->response = $this->render_layout($this->widget_organizers());
    }

    /**
     * Renders list of organizers
     *
     * @return string Html
     */
    public function widget_organizers()
    {
        $organizer = new Model_Organizer();

        $order_by = $this->request->param('acl_oorder', 'id');
        $desc = (bool) $this->request->param('acl_odesc', '0');

        $per_page = 20;

        // Select all organizers
        $count = $organizer->count();
        $pagination = new Paginator($count, $per_page);

        $organizers = $organizer->find_all(array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        $view = new View('backend/organizers');
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->organizers      = $organizers;
        $view->pagination = $pagination->render('backend/pagination');

        return $view->render();
    }
  
    /**
     * Select several towns
     */
    public function action_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор организаций на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_select();
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_select());
        }
    }
    
    /**
     * Render list of towns to select several towns
     * @param  integer $select
     * @return string
     */
    public function widget_select($select = FALSE)
    {
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }

        // ----- Render section for current section group
        $order_by = $this->request->param('acl_oorder', 'name');
        $desc     = (bool) $this->request->param('acl_odesc', '0');

        $organizers = Model::fly('Model_Organizer')->find_all(array(
            'order_by' => $order_by,
            'desc'     => $desc
        ));
   
        // Set up view
        $view = new View('backend/organizers/select');

        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->organizers = $organizers;

        return $view->render();
    }
    
    /**
     * Handles the selection of access organizers for event
     */
    public function action_organizers_select()
    {
        if ( ! empty($_POST['ids']) && is_array($_POST['ids']))
        {
            $organizer_ids = '';
            foreach ($_POST['ids'] as $organizer_id)
            {
                $organizer_ids .= (int) $organizer_id . '_';
            }
            $organizer_ids = trim($organizer_ids, '_');

            $this->request->redirect(URL::uri_back(NULL, 1, array('access_organizer_ids' => $organizer_ids)));
        }
        else
        {
            // No towns were selected
            $this->request->redirect(URL::uri_back());
        }
    }    
    
    /**
     * Generate redirect url
     *
     * @param  string $action
     * @param  Model $model
     * @param  Form $form
     * @param  array $params
     * @return string
     */
    protected function  _redirect_uri($action, Model $model = NULL, Form $form = NULL, array $params = NULL)
    {
        if ($action == 'create')
        {
            return URL::uri_self(array('action'=>'update', 'id' => $model->id, 'history' => $this->request->param('history'))) . '?flash=ok';
        }

        if ($action == 'update')
        {
            return URL::uri_to('backend/acl/organizers');
        }
        if ($action == 'multi_link')
        {
            return URL::uri_back();
        }
        
        return parent::_redirect_uri($action, $model, $form, $params);
    }
    
    /**
     * Display autocomplete options for a postcode form field
     */
    public function action_ac_organizer_name()
    {
        $organizer_name = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $organizer_name = UTF8::str_ireplace("ё", "е", $organizer_name);

        if ($organizer_name == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;
       
        $organizers  = Model::fly('Model_Organizer')->find_all_like_name($organizer_name,array('limit' => $limit));
             
        if ( ! count($organizers))
        {
            $this->request->response = '';
            return;
        }

        $items = array();
        
        $pattern = new View('backend/organizer_ac');
        
        foreach ($organizers as $organizer)
        {
            $name = $organizer->name;
            $id = $organizer->id;
            
            $image_info = $organizer->image(4);
            
            $pattern->name = $name;
                $pattern->image_info = $organizer->image(4);
            
            $items[] = array(
                'caption' => $pattern->render(),
                'value' => array('name' => $name, 'id' => $id) 
            );
        }

        $this->request->response = json_encode($items);
    }
    
}
