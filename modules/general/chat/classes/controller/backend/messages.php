<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Messages extends Controller_BackendCRUD
{
    /**
     * Setup actions
     * 
     * @return array
     */
    public function setup_actions()
    {        
        $this->_model = 'Model_Message';
        $this->_form  = 'Form_Backend_Message';

        return array(
            'create' => array(
                'view_caption' => 'Создание сообщения'
            ),
            'delete' => array(
                'view_caption' => 'Удаление сообщения',
                'message' => 'Удалить сообщение ":message_preview"?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление сообщений',
                'message' => 'Удалить выбранные сообщения?'
            )
        );
    }
    /**
     * Prepare message for create/delete action
     *
     * @param  string $action
     * @param  string|Model_Message $message
     * @param  array $params
     * @return Model_Message
     */
    protected function  _model($action, $message, array $params = NULL)
    {
        $message = parent::_model($action, $message, $params);

        if ($action == 'create')
        {
            $message->dialog_id = $this->request->param('dialog_id');
            
            $dialog = new Model_Dialog();
            $dialog->find((int) $this->request->param('dialog_id'));
            if (isset($dialog->id))
            {            
                $message->dialog_id = $dialog->id;
                $message->sender_id = $dialog->sender_id;
                $message->receiver_id = $dialog->receiver_id;
            } else {
                // user_id can be changed via url parameter
                $user_id = $this->request->param('user_id', NULL);

                if ($user_id !== NULL)
                {
                    $user_id = (int) $user_id;
                    if (
                        ($user_id == 0)
                     || ($user_id > 0 && Model::fly('Model_User')->exists_by_id($user_id))
                    )
                    {
                        $message->receiver_id = $user_id;
                    }
                }                
            }
            
        }
        return $message;
    }
    /**
     * Create layout (proxy to dialog controller)
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        return $this->request->get_controller('dialogs')->prepare_layout($layout_script);
    }

    /**
     * Render layout (proxy to dialog controller)
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        return $this->request->get_controller('dialogs')->render_layout($content, $layout_script);
    }

    /**
     * Render list of dialogs and corresponding messages using two-column panel
     */
    public function action_index()
    {
        $view = new View('backend/workspace');

        $view->content = $this->widget_messages();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }
    
    /**
     * Renders list of messages
     *
     * @return string Html
     */
    public function widget_messages()
    {
        $message = new Model_Message();

        $per_page = 5;

        $view = new View('backend/messages');
      
        $dialog_id = (int) $this->request->param('dialog_id');
        if ($dialog_id > 0)
        {
            // Show messages only from specified dialog
            $dialog = new Model_Dialog();
            $dialog->find($dialog_id);

            if ($dialog->id === NULL)
            {
                // Dialog was not found - show a form with error message
                return $this->_widget_error('Диалог с идентификатором ' . $dialog_id . ' не найден!');
            }

            $count      = $message->count_by_dialog_id($dialog->id);
            $pagination = new Pagination($count, $per_page);
            $messages = $message->find_all_by_dialog_id($dialog->id, array(
                'offset'   => $pagination->offset,
                'limit'    => $pagination->limit,
                'order_by' => 'created_at',
                'desc'     => '1'
            ), TRUE);

            // Prepare model for create action
            $model = $this->_prepare_model('create', array());

            // Prepare form for action
            $form = new Form_Backend_SmallMsg($model);

            // It's a POST action create
            if ($form->is_submitted() && $form->validate())
            {
                $message = new Model_Message($form->get_values());
                $message->save();
                
                $this->request->redirect($this->request->uri());                
            }
            
            $view->dialog = $dialog;
            $view->messages      = $messages;
            $view->pagination = $pagination->render('backend/pagination');
            $view->form = $form;
        }
        return $view->render(); 
    }
}