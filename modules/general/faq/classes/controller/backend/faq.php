<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Faq extends Controller_BackendCRUD
{
    /**
     * Setup actions
     *
     * @return array
     */
    public function setup_actions()
    {
        $this->_model = 'Model_Question';
        $this->_form  = 'Form_Backend_Question';

        return array(
            'create' => array(
                'view_caption' => 'Новый вопрос'
            ),
            'update' => array(
                'view_caption' => 'Редактирование вопроса'
            ),
            'delete' => array(
                'view_caption' => 'Удаление вопроса',
                'message' => 'Удалить вопрос ":question_preview"?'
            ),
            'multi_delete' => array(
                'view_caption' => 'Удаление вопросов',
                'message' => 'Удалить выбранные вопросы?'
            )
        );
    }

    /**
     * @return boolean
     */
    public function before()
    {
        if ( ! parent::before())
        {
            return FALSE;
        }

        // Check that there is a site selected
        if (Model_Site::current()->id === NULL)
        {
            $this->_action_error('Выберите сайт для работы с вопросами!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Create layout and link module stylesheets
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('faq') . '/public/css/backend/faq.css');
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
     * Default action
     */
	public function action_index()
	{
        $this->request->response = $this->render_layout($this->widget_questions());
	}

    /**
     * Renders questions list
     *
     * @return string
     */
    public function widget_questions()
    {
        $site_id = (int) Model_Site::current()->id;
        
        $order_by = $this->request->param('faq_order', 'created_at');
        $desc     = (bool) $this->request->param('faq_desc', '1');

        $per_page = 20;
        $count = Model::fly('Model_Question')->count_by_site_id($site_id);
        $pagination = new Pagination($count, $per_page);

        $questions = Model::fly('Model_Question')->find_all_by_site_id($site_id, array(
            'order_by' => $order_by,
            'desc'     => $desc,
            'limit'    => $pagination->limit,
            'offset'   => $pagination->offset
        ));

        // Set up view
        $view = new View('backend/questions');

        $view->order_by = $order_by;
        $view->desc     = $desc;

        $view->questions = $questions;
        $view->pagination = $pagination->render('backend/pagination');

        return $view;
    }

    /**
     * FAQ module config
     */
    public function action_config()
    {
        $config = Modules::load_config('faq_' . Model_Site::current()->id, 'faq');
        if ($config === FALSE)
        {
            $config = array();
        }
        
        $form = new Form_Backend_FaqConfig();
        $form->set_defaults($config);

        if ($form->is_submitted() && $form->validate())
        {
            $config = $form->get_values();
            unset($config['submit']);
            
            Modules::save_config('faq_' . Model_Site::current()->id, $config);

            $form->flash_message('Настройки модуля "Вопрос-ответ" сохранены');
            $this->request->redirect($this->request->uri);
        }

        $view = new View('backend/form_adv');
        $view->caption = 'Настройки модуля "Вопрос-ответ"';
        $view->form = $form;

        $this->request->response = $this->render_layout($view->render());
    }
}
