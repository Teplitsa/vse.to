<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Acl extends Controller_Frontend
{

    public function before()
    {
        if ($this->request->action === 'login' || $this->request->action === 'logout')
        {
            // Allow everybody to login & logout
            return TRUE;
        }

        return parent::before();
    }
    /**
     * Prepare layout
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {               
        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('acl') . '/public/css/frontend/acl.css');

        if ($this->request->action == 'user_select')
        {
            // Add user select js scripts
            $layout->add_script(Modules::uri('acl') . '/public/js/frontend/user_select.js');
            $layout->add_script(
                "var user_selected_url = '" . URL::to('frontend/acl', array('action' => 'user_selected', 'user_id' => '{{id}}')) . "';"
            , TRUE);
        }
        
        if ($this->request->action == 'lecturer_select')
        {
            // Add lecturer select js scripts
            $layout->add_script(Modules::uri('acl') . '/public/js/frontend/lecturer_select.js');
            $layout->add_script(
                "var lecturer_selected_url = '" . URL::to('frontend/acl', array('action' => 'lecturer_selected', 'lecturer_id' => '{{id}}')) . "';"
            , TRUE);
        }        
        
        return $layout;
    }
    
    /**
     * Render acl with given name
     * 
     * @param  string $name
     * @return string
     */
    public function widget_acl()
    {
        // Find acl by name
        $user = Auth::instance()->get_user();
        
        $privileges = $user->privileges_granted;
       
        $view = new View('privilege_templates/default');
        
        $view->privileges = $privileges;

        return $view;
    }
    
    /**
     * Render auth form
     */
    public function widget_login()
    {        
        $user = Model_User::current();
        $view = new View('frontend/login');
        
        if ($user->id) {
            $view->user = $user;            
        } else {
            $form_log = new Form_Frontend_Login($user);

            if ($form_log->is_submitted())
            {
                // User is trying to log in
                if ($form_log->validate())
                {
                    $result = Auth::instance()->login(
                        $form_log->get_element('email')->value,
                        $form_log->get_element('password')->value,
                        $form_log->get_element('remember')->value
                    );

                    if ($result)
                    {
                        //$this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Auth::instance()->get_user()->town->alias)));
                        //$this->request->redirect(Request::current()->uri);
                        $this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Model_Town::ALL_TOWN)));              
                    }
                    else
                    {
                        $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
                    }
                } else {
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
                }
            }
            
            $form_reg = new Form_Frontend_Register();

            if ($form_reg->is_submitted())
            {
                // User is trying to log in
                if ($form_reg->validate())
                {
                    $new_user = new Model_User();

                    if ($new_user->validate($form_reg->get_values())) {
                        $new_user->values($form_reg->get_values());
                        $new_user->save();            

                        $result = Auth::instance()->login(
                            $form_reg->get_element('email')->value,
                            $form_reg->get_element('password')->value
                        );
                        if ($result)
                        {
                            //$this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Auth::instance()->get_user()->town->alias)));
                            //$this->request->redirect(Request::current()->uri);
                            $this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Model_Town::ALL_TOWN)));
                        }
                        else
                        {
                            $form_reg->errors(Auth::instance()->errors());
                        }                    
                    } else {
                        $form_reg->errors($new_user->errors());
                    }
                }
            }
            
            $form_notify = new Form_Frontend_Notify();

            $modal = Layout::instance()->get_placeholder('modal');        
            $modal = $form_log->render().' '.$form_reg->render().' '.$form_notify->render().' '.$modal;
            Layout::instance()->set_placeholder('modal',$modal);
            
            //Layout::instance()->set_placeholder('modal',$form_log->render().' '.$form_reg->render().' '.$form_notify->render());
        }
        return $view;
    }
    
    /**
     * Render auth form
     */
    public function widget_register()
    {        
        $view = new View('frontend/register');
        
        $form = new Form_Frontend_Register();

        if ($form->is_submitted())
        {            
            // User is trying to log in
            if ($form->validate())
            {
                $new_user = new Model_User($form->get_values());
                
                if ($new_user->validate_save()) {
                    $new_user->save();            

                    $result = Auth::instance()->login(
                        $form->get_element('email')->value,
                        $form->get_element('password')->value
                    );
                   
                    if ($result)
                    {
                        //$this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Auth::instance()->get_user()->town->alias)));
                        //$this->request->redirect(Request::current()->uri);                        
                        $this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Model_Town::ALL_TOWN)));
                    }
                    else
                    {
                        $form->errors(Auth::instance()->errors());
                    }                    
                } else {
                    $form->errors($new_user->errors());
                }
            }
        }

        Layout::instance()->set_placeholder('modal',$form->render());
       
        return $view;
    }
    
    public function action_logout()
    {
        Auth::instance()->logout();
        $this->request->redirect('');
    }

    public function action_relogin()
    {
        $view = new View('frontend/relogin');

        $form_log = new Form_Frontend_Relogin();

        if ($form_log->is_submitted())
        {
            // User is trying to log in
            if ($form_log->validate())
            {
                $result = Auth::instance()->login(
                    $form_log->get_element('email')->value,
                    $form_log->get_element('password')->value,
                    $form_log->get_element('remember')->value
                );

                if ($result)
                {
                    //$this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Auth::instance()->get_user()->town->alias)));
                    //$this->request->redirect(Request::current()->uri);
                    $this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Model_Town::ALL_TOWN)));               
                }
                else
                {
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
                }
            }
        }

        $view->form = $form_log;
        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();        
    }
    
  public function widget_pasrecovery()
    {  
        $view = new View('frontend/pasrecovery');
        
        
        $form_log = new Form_Frontend_Pasrecovery();

        if ($form_log->is_submitted())
        {
            // User is trying to log in
            if ($form_log->validate())
            {
                $user = Model::fly('Model_User')->find_by_email($form_log->get_element('email')->value);
                $result = FALSE;
                
                if ($user->id)
                {
                    $result=$user->password_recovery();
                }

                if ($result)
                {
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin','stat' => 'ok')));

                }
                else
                {
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
                }
            }
        }
        
        $modal = Layout::instance()->get_placeholder('modal');
        Layout::instance()->set_placeholder('modal',$modal.' '.$form_log->render());
        
        return $view;
    }
    
    public function action_newpas()
    {
        $user = Model::fly('Model_User')->find_by_recovery_link('http://vse.to'.URL::site().$this->request->uri);
        if ($user->id) {
            $form_log = new Form_Frontend_Newpas();

            if ($form_log->is_submitted())
            {
                // User is trying to log in
                if ($form_log->validate())
                {
                    $user->password = $form_log->get_element('password')->value;
                    $user->recovery_link = '';
                    $user->save();
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin','stat' => 'try')));
                   
                }
            }
        } else {
            $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
        }
        $view = new View('frontend/newpas');        
        
        $view->form = $form_log;

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();         
    }

    public function action_activation()
    {
        $user = Model::fly('Model_User')->find_by_activation_link('http://vse.to'.URL::site().$this->request->uri);
        if ($user->id) {
            $user->activation_link = '';
            $user->active = TRUE;
            $user->save();
            $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin','stat' => 'try')));
        } else {
            $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
        }
    }
    
    public function action_pasrecovery()
    {
        $view = new View('frontend/pasrecovery');

        $form_log = new Form_Frontend_Pasrecovery();

        if ($form_log->is_submitted())
        {
            // User is trying to log in
            if ($form_log->validate())
            {
                
                $result = Auth::instance()->login(
                    $form_log->get_element('email')->value,
                    $form_log->get_element('password')->value,
                    $form_log->get_element('remember')->value
                );

                if ($result)
                {
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin','stat' => 'ok')));

                }
                else
                {
                    $this->request->redirect(URL::uri_to('frontend/acl',array('action'=>'relogin')));
                }
            }
        }

        $view->form = $form_log;

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();        
    }
    
    /**
     * Renders logout button
     *
     * @return string
     */
    public function widget_logout()
    {                
        $view = new View('backend/logout');
        return $view;
    }
    

    /**
     * Render user-select dialog
     */
    public function action_user_select()
    {
        $layout = $this->prepare_layout();
        
        if (empty($_GET['window']))
        {
            $view = new View('frontend/workspace');
            $view->caption = 'Выбор пользователя';
//            $view->content = $this->request->get_controller('users')->widget_user_select();
            $view->content = $this->widget_user_select();

            $layout->content = $view;
        }
        else {
            $view = new View('frontend/workspace');
//            $view->content = $this->request->get_controller('users')->widget_user_select();
            $view->content = $this->widget_user_select();
            $layout->caption = 'Выбор пользователя';
            $layout->content = $view->render();
        }

        $this->request->response = $layout->render();
    }

    public function widget_user_select()
    {
        $view = new View('frontend/workspace_2col');

        $view->column1  = $this->widget_groups();
        $view->column2  = $this->request->get_controller('users')->widget_user_select();

        return $view;
    }
    
    public function widget_groups()
    {
        $group = new Model_Group();

        $group_id = (int) $this->request->param('group_id');
        
        $group_ids = array(Model_Group::EDITOR_GROUP_ID,Model_Group::USER_GROUP_ID);
        
        $groups = new Models('Model_Group',array());
        
        foreach ($group_ids as $group_id) {
            $groups[] = $group->find($group_id);        
        }
        
        $view = new View('frontend/groups');
        $view->group_id = $group_id;

        $view->groups = $groups;

        return $view->render();
    }    
    
    /**
     * Generate the response for ajax request after user is selected
     * to inject new values correctly into the form
     */
    public function action_user_selected()
    {
        $user = new Model_User();
        $user->find((int) $this->request->param('user_id'));

        $values = $user->values();
        $values['user_name'] = $user->name;
        $this->request->response = JSON_encode($values);
    }   

    /**
     * Render lecturer-select dialog
     */
    public function action_lecturer_select()
    {
        $layout = $this->prepare_layout();

        if (empty($_GET['window']))
        {
            $view = new View('frontend/workspace');
            $view->caption = 'Выбор лектора';
            $view->content = $this->request->get_controller('lecturers')->widget_lecturer_select_form();
            $layout->content = $view;
        }
        else {
            $view = new View('frontend/workspace');
            $view->content = $this->request->get_controller('lecturers')->widget_lecturer_select_form();

            $layout->caption = 'Выбор лектора';
            $layout->content = $view->render();
        }

        $this->request->response = $layout->render();
    }

    /**
     * Generate the response for ajax request after lecturer is selected
     * to inject new values correctly into the form
     */
    public function action_lecturer_selected()
    {
        $lecturer = new Model_Lecturer();
        $lecturer->find((int) $this->request->param('lecturer_id'));
        $values = $lecturer->values();
        $values['lecturer_name'] = $lecturer->name;
        $this->request->response = JSON_encode($values);
    } 
    
    /**
     * Login with social network
     */
    public function action_login_social()
    {
        $ulogin = Ulogin::factory();
        $layout = $this->prepare_layout();
        $layout->caption = 'Вход через социальные сети';

        if (!$ulogin->mode())
        {
            $layout->content = $ulogin->render();
            $this->request->response = $layout->render();
        }
        else
        {
            try
            {
                $userPassword = $ulogin->login();
                
                if($userPassword)
                {
                    $layout->content = 'Вы успешно вошли! Ваш временный пароль: '.$userPassword
                            .'. <br />Вы можете входить на сайта как через комбинацию email-пароль, так и через выбранную социальную сеть.'
                            .'. <br /><br />Пожалуйста, проверьте свой город на странице настроек учетной записи.';
                    $this->request->response = $layout->render();
                }
                else
                {
                    $this->request->redirect(URL::uri_to('frontend/area/towns',array('action'=>'choose', 'are_town_alias' => Model_Town::ALL_TOWN)));                
                }
            }
            catch(Kohana_Exception $e)
            {
                $layout->content = 'Произошла ошибка: '.$e->getMessage();
                $this->request->response = $layout->render();
            }
        }        
    }
}
