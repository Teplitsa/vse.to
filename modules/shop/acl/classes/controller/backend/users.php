<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Users extends Controller_BackendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_User';
        $this->_form  = 'Form_Backend_User';
        $this->_view  = 'backend/form_adv';        

        return array(
            'create' => array(
                'view_caption' => 'Создание пользователя'
            ),
            'update' => array(
                'view_caption' => 'Редактирование пользователя'
            ),
            'delete' => array(
                'view_caption' => 'Удаление пользователя',
                'message' => 'Удалить пользователя ":name" (:email)?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление пользователей',
                'message' => 'Удалить выбранных пользователей?'
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
     * Select several towns
     */
    public function action_select()
    {
        $layout = $this->prepare_layout();

        if ($this->request->in_window())
        {
            $layout->caption = 'Выбор редакторов на портале "' . Model_Site::current()->caption . '"';
            $layout->content = $this->widget_user_select('backend/users/select');
            $this->request->response = $layout->render();
        }
        else
        {
            $this->request->response = $this->render_layout($this->widget_user_select('backend/users/select'));
        }
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

        $per_page = 20;

        $group_id = (int) $this->request->param('group_id',Model_Group::EDITOR_GROUP_ID);
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
            $users = $user->find_all_by_group_id($group->id,true, array(
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

            $users = $user->find_all(array(
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
     * Handles the selection of access organizers for event
     */
    public function action_users_select()
    {
        if ( ! empty($_POST['ids']) && is_array($_POST['ids']))
        {
            $user_ids = '';
            foreach ($_POST['ids'] as $user_id)
            {
                $user_ids .= (int) $user_id . '_';
            }
            $user_ids = trim($user_ids, '_');

            $this->request->redirect(URL::uri_back(NULL, 1, array('access_user_ids' => $user_ids)));
        }
        else
        {
            // No towns were selected
            $this->request->redirect(URL::uri_back());
        }
    }     
    /**
     * Renders list of users for user-select dialog
     *
     * @return string Html
     */
    public function widget_user_select($view_script = 'backend/user_select')
    {
        $user = new Model_User();

        $order_by = $this->request->param('acl_uorder', 'id');
        $desc = (bool) $this->request->param('acl_udesc', '0');

        $per_page = 20;

        $group_id = (int) $this->request->param('group_id',Model_Group::EDITOR_GROUP_ID);

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

        $view = new View($view_script);
        $view->order_by = $order_by;
        $view->desc = $desc;

        $view->group = $group;

        $view->users      = $users;
        $view->pagination = $pagination->render('backend/pagination');

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
        $view = 'backend/user_card';
        $widget = new Widget($view);
        $widget->id = 'user_card' . $user->id;
        $widget->context_uri = FALSE; // use the url of clicked link as a context url

        $widget->user = $user;
        $widget->fields = $fields;
        
        return $widget;
    }   
}
