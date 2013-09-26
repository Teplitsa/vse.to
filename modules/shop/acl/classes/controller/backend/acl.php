<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Acl extends Controller_Backend
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
        $layout->add_style(Modules::uri('acl') . '/public/css/backend/acl.css');

        if ($this->request->action == 'user_select')
        {
            // Add user select js scripts
            $layout->add_script(Modules::uri('acl') . '/public/js/backend/user_select.js');
            $layout->add_script(
                "var user_selected_url = '" . URL::to('backend/acl', array('action' => 'user_selected', 'user_id' => '{{id}}')) . "';"
            , TRUE);
        }
        
        if ($this->request->action == 'lecturer_select')
        {
            // Add lecturer select js scripts
            $layout->add_script(Modules::uri('acl') . '/public/js/backend/lecturer_select.js');
            $layout->add_script(
                "var lecturer_selected_url = '" . URL::to('backend/acl', array('action' => 'lecturer_selected', 'lecturer_id' => '{{id}}')) . "';"
            , TRUE);
        }
        
//        if ($this->request->action == 'ac_lecturer_name')
//        {   
//            // Add lecturer select js scripts
//            $layout->add_script(Modules::uri('acl') . '/public/js/backend/lecturer_name.js');
//        }   
        
//        if ($this->request->action == 'ac_organizer_name')
//        {   
//            // Add lecturer select js scripts
//            $layout->add_script(Modules::uri('acl') . '/public/js/backend/organizer_name.js');
//        }
        
        if ($this->request->action == 'select' && $this->request->controller == 'organizers')
        {
            // Add organizers select js scripts
            $layout->add_script(Modules::uri('acl') . '/public/js/backend/organizers_select.js');
        }        
        
        if ($this->request->action == 'select' && $this->request->controller == 'users')
        {
            // Add organizers select js scripts
            $layout->add_script(Modules::uri('acl') . '/public/js/backend/users_select.js');
        }
        
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
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->content = $view;
        return $layout->render();
    }
    
    /**
     * Render list of users and groups using two-column panel
     */
    public function action_index()
    {
        $view = new View('backend/workspace_2col');

        $view->column1 = $this->request->get_controller('groups')->widget_groups();
        $view->column2 = $this->request->get_controller('users')->widget_users();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }

    /**
     * Render user-select dialog
     */
    public function action_user_select()
    {
        $layout = $this->prepare_layout();

        if (empty($_GET['window']))
        {
            $view = new View('backend/workspace');
            $view->caption = 'Выбор пользователя';
            //$view->content = $this->request->get_controller('users')->widget_user_select();
            $view->content = $this->widget_user_select();

            $layout->content = $view;
        }
        else {
            $view = new View('backend/workspace');
            $view->content = $this->widget_user_select();

            $layout->caption = 'Выбор пользователя';
            $layout->content = $view->render();
        }

        $this->request->response = $layout->render();
    }

    public function widget_user_select()
    {
        $view = new View('backend/workspace_2col');

        $view->column1  = $this->request->get_controller('groups')->widget_groups('backend/group_user_select');
        $view->column2  = $this->request->get_controller('users')->widget_user_select();

        return $view;
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
            $view = new View('backend/workspace');
            $view->caption = 'Выбор лектора';
            $view->content = $this->request->get_controller('lecturers')->widget_lecturer_select();

            $layout->content = $view;
        }
        else {
            $view = new View('backend/workspace');
            $view->content = $this->request->get_controller('lecturers')->widget_lecturer_select();

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
     * Login page && authorization
     */
    public function action_login()
    {
        $user = Model_User::current();

        $form = new Form_Backend_Login($user);

        if ($form->is_submitted())
        {
            // User is trying to log in
            if ($form->validate())
            {
                $result = Auth::instance()->login(
                    $form->get_element('email')->value,
                    $form->get_element('password')->value,
                    $form->get_element('remember')->value
                );

                if ($result)
                {
                    // User was succesfully authenticated
                    // Is he authorized to access backend?
                    if (Auth::granted('backend_access'))
                    {
                        $this->request->redirect(Request::current()->uri);
                    }
                    else
                    {
                        $form->error('Доступ к панели управления запрещён!');
                    }
                }
                else
                {
                    $form->errors(Auth::instance()->errors());
                }
            }
        }

        $view = new View('backend/form');
        $view->caption = 'Аутентификация';
        $view->form = $form;

        $this->request->response = $this->render_layout($view, 'layouts/backend/login');
    }

    public function action_logout()
    {
        Auth::instance()->logout();
        $this->request->redirect('');
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
}
