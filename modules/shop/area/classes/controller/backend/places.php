<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Places extends Controller_BackendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_Place';
        $this->_form  = 'Form_Backend_Place';
        $this->_view  = 'backend/form_adv';        

        return array(
            'create' => array(
                'view_caption' => 'Создание площадки'
            ),
            'update' => array(
                'view_caption' => 'Редактирование площадки',
                
            ),
            'delete' => array(
                'view_caption' => 'Удаление площадки',
                'message' => 'Удалить площадку ":name" '
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление площадок',
                'message' => 'Удалить выбранные площадки?'
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
        $layout = $this->prepare_layout();
        
        $view = new View('backend/workspace_2col');

        $view->column1  = $this->request->get_controller('towns')->widget_towns('backend/towns/place_select');
        $view->column2  = $this->widget_places('backend/places/list');
        
        $view->caption = 'Выбор площадки на портале "' . Model_Site::current()->caption . '"';

        $layout->content = $view->render();
        
        $this->request->response = $layout->render();
    }

    /**
     * Render list of places
     * @return string
     */
    public function widget_places($view_script = 'backend/places/list') {
        $place = new Model_Place();

        // current site
        $site_id = Model_Site::current()->id;
        if ($site_id === NULL)
        {
            return $this->_widget_error('Выберите портал!');
        }
        $order_by = $this->request->param('are_porder', 'name');
        $desc     = (bool) $this->request->param('are_pdesc', '0');
        $per_page = 20;

        $town_alias = $this->request->param('are_town_alias');

        if ($town_alias !='')
        {
            // Show users only from specified group
            $town = new Model_Town();
            $town->find_by_alias($town_alias);

            if ($town->id === NULL)
            {
                // Group was not found - show a form with error message
                return $this->_widget_error('Указанный город не найдена!');
            }

            $count      = $place->count_by_town_id($town->id);
            $pagination = new Pagination($count, $per_page);
            $places = $place->find_all_by_town_id($town->id, array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            ), TRUE);
            
        } else {
            $town = NULL;
            // Select all users
            $count = $place->count();
            $pagination = new Pagination($count, $per_page);

            $places = $place->find_all(array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            ));
        }
       
        // Set up view
        $view = new View($view_script);

        $view->order_by = $order_by;
        $view->desc = $desc;
        $view->places = $places;
        $view->town = $town;
        
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
            $layout->caption = 'Выбор площадки на портале "' . Model_Site::current()->caption . '"';
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

        $organizers = Model::fly('Model_Place')->find_all(array(
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
            return URL::uri_back();
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
        
        $organizers  = Model::fly('Model_Place')->find_all_like_name($organizer_name,array('limit' => $limit));
            
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
