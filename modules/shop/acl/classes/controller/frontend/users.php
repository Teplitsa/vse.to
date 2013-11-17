<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Users extends Controller_FrontendRES
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_User';
        $this->_form  = 'Form_Frontend_User';
        $this->_view  = 'frontend/form_adv';        

        return array(
            'create' => array(
                'view_caption' => 'Создание пользователя'
            ),
            'update' => array(
                'view_caption' => 'Редактирование пользователя'
            )
        );        
    }
    
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
    public function render_layout($content, $layout_script = 'layouts/acl')
    {
        return $this->request->get_controller('acl')->render_layout($content, $layout_script);
    }
    
    /**
     * Prepare user for update action
     *
     * @param  string $action
     * @param  string|Model_User $user
     * @param  array $params
     * @return Model_User
     */
    protected function  _model($action, $user, array $params = NULL)
    {
        $params['id'] = Model_User::current()->id;
        return parent::_model($action, $user, $params);
    }
    
    
    public function _view_create(Model_User $model, Form_Frontend_User $form, array $params = NULL)
    {
        $organizer = new Model_Organizer();

        $form_organizer = new Form_Frontend_Organizer($organizer);
        if ($form_organizer->is_submitted())
        {
            $form_organizer->validate();

            // User is trying to log in
            if ($form_organizer->validate())
            {   
                $vals = $form_organizer->get_values();

                if ($organizer->validate($vals))
                {                    
                    
                    $organizer->values($vals);
                    $organizer->save();
                    
                    $form->get_element('organizer_name')->set_value($organizer->name);
                    $form->get_element('organizer_id')->set_value($organizer->id);
                }
            }
        }
        $modal = Layout::instance()->get_placeholder('modal');        
        $modal = $form_organizer->render().' '.$modal;
        Layout::instance()->set_placeholder('modal',$modal);

        $view = new View('frontend/users/control');
        $view->user = $model;
        $view->form = $form;

        return $view;
    }
        
    protected function  _redirect_uri($action, Model $model = NULL, Form $form = NULL, array $params = NULL)
    {
        if($action == 'create')
        {
            return URL::uri_to('frontend/acl/users/control',array('action' => 'confirm'));
        }
        if ($action == 'update')
        {            
            $result = Auth::instance()->login(
                $model->email,
                $model->password);

            if ($result)
            {   
                //$this->request->redirect(URL::uri_to('frontend/acl/users/control',array('action' => 'control')));
                //$this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Auth::instance()->get_user()->town->alias)));
                $this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Model_Town::ALL_TOWN)));
            }
            else
            {
                $this->_action_404();
                return; 
            }
        }
        
        return parent::_redirect_uri($action, $model, $form, $params);
    }     
    
    public function _view_update(Model_User $model, Form_Frontend_User $form, array $params = NULL)
    {
        $organizer = new Model_Organizer();

        $form_organizer = new Form_Frontend_Organizer($organizer);
        if ($form_organizer->is_submitted())
        {
            $form_organizer->validate();

            // User is trying to log in
            if ($form_organizer->validate())
            {   
                $vals = $form_organizer->get_values();

                if ($organizer->validate($vals))
                {                    
                    
                    $organizer->values($vals);
                    $organizer->save();
                    
                    $form->get_element('organizer_name')->set_value($organizer->name);
                    $form->get_element('organizer_id')->set_value($organizer->id);
                }
            }
        }
        $modal = Layout::instance()->get_placeholder('modal');        
        $modal = $form_organizer->render().' '.$modal;
        Layout::instance()->set_placeholder('modal',$modal);

        $view = new View('frontend/users/control');
        $view->user = $model;
        $view->form = $form;

        return $view;
    }
    
    
    public function action_index() {
        //TODO CARD of the USER
    }
    
    public function action_control() {        
        $view = new View('frontend/workspace');

        $view->content = $this->widget_user();
        
        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();        
        
    }
    
    public function action_edit() {
            $view = new View('frontend/workspace');
            $view->content = new View('frontend/users/profedit');

            $defpass = '0000000000';

            $user = Model_User::current();	

            $form = new Form_Frontend_ProfileEdit;
            if ($form->is_submitted()) {
                    if ($form->validate()) {
                            $data = $form->get_values();
                            if ($data['password'] == $defpass) {
                            $data['password'] = $user->password;
                            }		
                            $user->update_props($form->get_values());
                            $user->save(); 
                            $this->request->redirect('/acl/users/control');
                    }
            }

            $form->set_defaults(array(
                'first_name' => $user->first_name, 
                'last_name' => $user->last_name,
                'town_id' => $user->town_id,
                'email' => $user->email, 
                'password' => $defpass,
                'organizer_name' => $user->organizer_name,								 
                'info' => $user->info,						 
            ));


            $layout = $this->prepare_layout();
            $layout->content = $view;
            $view->content->form = $form;
            $this->request->response = $layout->render();  
    }

        
    /**
     * Redraw user images widget via ajax request
     */
    public function action_ajax_user_images()
    {
        $request = Widget::switch_context();

        $user = Model_User::current();
        if ( ! isset($user->id))
        {
            FlashMessages::add('Пользователь не найден', FlashMessages::ERROR);
            $this->_action_ajax();
            return;
        }

        $widget = $request->get_controller('users')
                ->widget_user_images($user);

        $widget->to_response($this->request);
        $this->_action_ajax();
    }    

    /**
     * Renders user account settings
     *
     * @param  boolean $select
     * @return string
     */
    public function widget_user($view = 'frontend/user')
    {           
        // ----- Current user
        $user_id = $this->request->param('user_id',NULL);

        if (!$user_id) {
            $user = Model_User::current();
        } else {
            $user = Model::fly('Model_User')->find($user_id);
        }
        
        if ( $user->id === NULL)
        {
            $this->_action_404('Указанный пользователь не найден');
            return;
        }        

        // Set up view
        $view = new View($view);

        $view->user = $user;

        return $view->render();
    }

    /**
     * Render images for user (when in user profile)
     *
     * @param  Model_User $user
     * @return Widget
     */
    public function widget_user_images(Model_User $user)
    {
        $widget = new Widget('frontend/users/user_images');
        $widget->id = 'user_' . $user->id . '_images';
        $widget->ajax_uri = URL::uri_to('frontend/acl/user/images');
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $images = Model::fly('Model_Image')->find_all_by_owner_type_and_owner_id('user', $user->id, array(
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
            $widget->user  = $user;
        }
        
        return $widget;
    }    
    
    /**
     * Perpare user model for creation
     * 
     * @param  string|Model $model
     * @param  array $params
     * @return Model
     */
    protected function _model_create($model, array $params = NULL)
    {
        $user = parent::_model('create', $model, $params);
        $user->group_id = $this->request->param('group_id');
        return $user;
    }

    /**
     * Renders list of users
     *
     * @return string Html
     */
    public function widget_users()
    {
        $user = new Model_User();

        $order_by = $this->request->param('acl_uorder', 'id');
        $desc = (bool) $this->request->param('acl_udesc', '0');

        $per_page = 10;

        $group_id = (int) $this->request->param('group_id');
        if ($group_id > 0)
        {
            // Show users only from specified group
            $group = new Model_Group();
            $group->find($group_id);

            if ($group->id === NULL)
            {
                // Group was not found - show a form with error message
                return $this->_widget_error('Группа с идентификатором ' . $group_id . ' не найдена!');
            }

            $count      = $user->count_by_group_id($group->id);
            $pagination = new Paginator($count, $per_page);
            $users = $user->find_all_by_group_id_and_active($group->id,true, array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            ), TRUE);
        }
        else
        {
            $group = NULL;
            // Select all users
            $count = $user->count();
            $pagination = new Paginator($count, $per_page);

            $users = $user->find_all_by_active(true,array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            ));
        }

        $view = new View('backend/users');
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->group = $group;

        $view->users      = $users;
        $view->pagination = $pagination->render('backend/pagination');

        return $view->render();
    }

    /**
     * Renders list of users for user-select dialog
     *
     * @return string Html
     */
    public function widget_user_select($view = 'frontend/user_select')
    {
        $user = new Model_User();

        $order_by = $this->request->param('acl_uorder', 'id');
        $desc = (bool) $this->request->param('acl_udesc', '0');

        $per_page = 20;

        $group_id = (int) $this->request->param('group_id');
        if ($group_id > 0)
        {
            // Show users only from specified group
            $group = new Model_Group();
            $group->find($group_id);

            if ($group->id === NULL)
            {
                // Group was not found - show a form with error message
                return $this->_widget_error('Группа с идентификатором ' . $group_id . ' не найдена!');
            }

            $count      = $user->count_by_group_id($group->id);
            $pagination = new Paginator($count, $per_page);
            $users = $user->find_all_by_group_id_and_active($group->id,true, array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            ), TRUE);
        }
        else
        {
            $group = NULL;
            // Select all users
            $count = $user->count();
            $pagination = new Paginator($count, $per_page);

            $users = $user->find_all_by_active(true,array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => $order_by,
                'desc'     => $desc
            ));
        }

        $view = new View($view);
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->group = $group;

        $view->users      = $users;
        $view->pagination = $pagination->render('pagination');

        return $view->render();
    }
    
    /**
     * Renders user account settings
     *
     * @param  boolean $select
     * @return string
     */
    public function widget_user_card($user,array $fields = array())
    {
        if (empty($fields)) {
            $fields = array('image','name'); 
        }
        $view = 'frontend/user_card';
        $widget = new Widget($view);
        $widget->id = 'user_card' . $user->id;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $widget->user = $user;
        $widget->fields = $fields;
        
        return $widget;
    }

    /**
     * Render list of external links for user (when in user profile)
     *
     * @param  Model_User $user
     * @return Widget
     */
    public function widget_links(Model_User $user)
    {
        $widget = new Widget('frontend/users/links');
        $widget->id = 'user_' . $user->id . '_links';

        $links = $user->links;
                
        if ($links->valid()) {
            $widget->links   = $links;
            $widget->user  = $user;
        }
        
        return $widget;
    }
    
    /**
     * Add breadcrumbs for current action
     */
    public function add_breadcrumbs(array $request_params = array())
    {
        if (empty($request_params)) {
            list($name, $request_params) = URL::match(Request::current()->uri);
        }
        
        if ($request_params['action'] == 'control') {
            Breadcrumbs::append(array(
                'uri' => URL::uri_to('frontend/acl/users/control',array('action' => 'control')),
                'caption' => 'Профайл'));
        }        
    }
    
    
    public function action_confirm()
    {
        $view = new View('frontend/users/confirm');
        
        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();         
    }
}
