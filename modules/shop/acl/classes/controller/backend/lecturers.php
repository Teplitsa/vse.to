<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Lecturers extends Controller_BackendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_Lecturer';
        $this->_form  = 'Form_Backend_Lecturer';
        $this->_view  = 'backend/form_adv';        

        return array(
            'create' => array(
                'view_caption' => 'Создание лектора'
            ),
            'update' => array(
                'view_caption' => 'Редактирование лектора'
            ),
            'delete' => array(
                'view_caption' => 'Удаление лектора',
                'message' => 'Удалить лектора ":name" '
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление лекторов',
                'message' => 'Удалить выбранных лекторов?'
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
        $this->request->response = $this->render_layout($this->widget_lecturers());
    }

    /**
     * Renders list of users
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

        $view = new View('backend/lecturers');
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->lecturers      = $lecturers;
        $view->pagination = $pagination->render('backend/pagination');

        return $view->render();
    }
    
    /**
     * Renders list of lecturers for lecturer-select dialog
     *
     * @return string Html
     */
    public function widget_lecturer_select($view_script = 'backend/lecturer_select')
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
        $view->pagination = $pagination->render('backend/pagination');

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
            
        if ( ! count($lecturers))
        {
            $this->request->response = '';
            return;
        }

        $items = array();
        
        $pattern = new View('backend/lecturer_ac');
        
        foreach ($lecturers as $lecturer)
        {
            $name = $lecturer->name;
            $id = $lecturer->id;
            
            $image_info = $lecturer->image(4);
            
            $pattern->name = $name;
            $pattern->image_info = $lecturer->image(4);
            
            $items[] = array(
                'caption' => $pattern->render(),
                'value' => array('name' => $name, 'id' => $id) 
            );
        }

        $this->request->response = json_encode($items);
    }
    
}
