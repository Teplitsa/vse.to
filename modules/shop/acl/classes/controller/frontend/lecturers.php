<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Lecturers extends Controller_Frontend
{
    /**
     * Prepare layout
     *
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = 'layouts/acl')
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
    
    public function action_index() {
        //TODO CARD of the USER
    }

    
    public function action_show() {        
        $view = new View('frontend/workspace');

        $view->content = $this->widget_lecturer();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();        
        
    }    
    /**
     * Redraw lecturer images widget via ajax request
     */
    public function action_ajax_lecturer_images()
    {        
        $request = Widget::switch_context();
        $lecturer_id = $request->param('lecturer_id',NULL);
        
        $lecturer = Model::fly('Model_Lecturer')->find($lecturer_id);
        if ( ! isset($lecturer->id))
        {
            FlashMessages::add('Лектор не найден', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('lecturers')
                ->widget_lecturer_images($lecturer);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }    

    /**
     * Renders lecturer settings
     *
     * @param  boolean $select
     * @return string
     */
    public function widget_lecturer($view = 'frontend/lecturer')
    {           
        // ----- Current lecturer
        $lecturer_id = $this->request->param('lecturer_id',NULL);

        if (!$lecturer_id) {
            $lecturer = Model_Lecturer::current();
        } else {
            $lecturer = Model::fly('Model_Lecturer')->find($lecturer_id);
        }
        // Set up view
        $view = new View($view);

        $view->lecturer = $lecturer;

        return $view->render();
    }

    /**
     * Render images for lecturer (when in lecturer profile)
     *
     * @param  Model_Lecturer $lecturer
     * @return Widget
     */
    public function widget_lecturer_images(Model_Lecturer $lecturer)
    {
        $widget = new Widget('frontend/lecturers/lecturer_images');
        $widget->id = 'lecturer_' . $lecturer->id . '_images';
        $widget->ajax_uri = URL::uri_to('frontend/acl/lecturer/images');
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $images = Model::fly('Model_Image')->find_all_by_owner_type_and_owner_id('lecturer', $lecturer->id, array(
            'order_by' => 'position',
            'desc'     => FALSE
        ));
        
        if ($images->valid()) {
            $image_id = (int) $this->request->param('image_id');

            if ( ! isset($images[$image_id]))
            {
                $image_id = $images->at(0)->id;
            }

            $widget->image_id = $image_id; // id of current image
            $widget->images   = $images;
            $widget->lecturer  = $lecturer;
        }
        
        return $widget;
    }    
    
    /**
     * Renders list of lecturers
     *
     * @return string Html
     */
    public function widget_lecturers()
    {
        $lecturer = new Model_Lecturer();

        $order_by = $this->request->param('acl_lorder', 'id');
        $desc = (bool) $this->request->param('acl_ldesc', '0');

        $per_page = 20;

        // Select all users
        $count = $lecturer->count();
        $pagination = new Paginator($count, $per_page);

        $lecturers = $lecturer->find_all(array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        $view = new View('frontend/lecturers');
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->lecturers      = $lecturers;
        $view->pagination = $pagination->render('pagination');

        return $view->render();
    }    

    /**
     * Renders list of lecturers for lecturer-select dialog
     *
     * @return string Html
     */
    public function widget_lecturer_select($view_script = 'frontend/lecturer_select',array $ids = NULL)
    {
        $lecturer = new Model_Lecturer();

        $order_by = $this->request->param('acl_lorder', 'id');
        $desc = (bool) $this->request->param('acl_ldesc', '0');
        
        $conditions = array();
        if (!empty($ids)) {
            $conditions['ids'] = $ids;
        }
        
        $per_page = 5;

        // Select all lecturers
        $count = $lecturer->count();
        $pagination = new Paginator($count, $per_page);

        $lecturers = $lecturer->find_all_by($conditions,array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        $view = new View($view_script);
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->lecturers      = $lecturers;
        $view->pagination = $pagination->render('pagination_small');
        
        Layout::instance()->add_style(Modules::uri('acl') . '/public/css/frontend/acl.css');

        return $view->render();
    }
    
    /**
     * Renders list of lecturers for lecturer-select dialog
     *
     * @return string Html
     */
    public function widget_lecturer_select_form($view_script = 'frontend/lecturer_select')
    {
        $lecturer = new Model_Lecturer();

        $order_by = $this->request->param('acl_lorder', 'id');
        $desc = (bool) $this->request->param('acl_ldesc', '0');

        $per_page = 20;

        // Select all lecturers
        $count = $lecturer->count();
        $pagination = new Paginator($count, $per_page);

        $lecturers = $lecturer->find_all(array(
            'offset'   => $pagination->offset,
            'limit'    => $pagination->limit,
            'order_by' => $order_by,
            'desc'     => $desc
        ));

        $view = new View($view_script);
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->lecturers      = $lecturers;
        $view->pagination = $pagination->render('pagination');

        return $view->render();
    }
    
    /**
     * Display autocomplete options for a postcode form field
     */
    public function action_ac_lecturer_name()
    {           
        $lecturer_name = isset($_POST['value']) ? trim(UTF8::strtolower($_POST['value'])) : NULL;
        $lecturer_name = UTF8::str_ireplace("ё", "е", $lecturer_name);

        if ($lecturer_name == '')
        {
            $this->request->response = '';
            return;
        }

        $limit = 7;
        
        $lecturers  = Model::fly('Model_Lecturer')->find_all_like_name($lecturer_name,array('limit' => $limit));
            
//        if ( ! count($lecturers))
//        {
//            $this->request->response = '';
//            return;
//        }

        $items = array();
        
        $pattern = new View('frontend/lecturer_ac');
        
        $i=0;
        foreach ($lecturers as $lecturer)
        {
            $name = $lecturer->name;
            $id = $lecturer->id;
            
            $image_info = $lecturer->image(4);
            
            $pattern->name = $name;
            $pattern->image_info = $lecturer->image(4);
            $pattern->num = $i;
            $items[] = array(
                'caption' => $pattern->render(),
                'value' => array('name' => $name, 'id' => $id) 
            );
            $i++;
        }
   
        $items[] = array('caption' => '<a data-toggle="modal" href="#LectorModal" class="active">Добавить нового лектора</a>');
        
        $this->request->response = json_encode($items);
    }
    
    /**
     * Add breadcrumbs for current action
     */
    public function add_breadcrumbs(array $request_params = array())
    {
        if (empty($request_params)) {
            list($name, $request_params) = URL::match(Request::current()->uri);
        }
        
        if ($request_params['action'] == 'show') {
            Breadcrumbs::append(array(
                'uri' => URL::uri_to('frontend/acl/lecturers',array('action' => 'show','lecturer_id' => $request_params['lecturer_id'])),
                'caption' => 'Лектор'));
        } 
    }     
}
