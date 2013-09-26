<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Faq extends Controller_Frontend
{
    public $per_page = 20;

    /**
     * Render the list of questions
     */
    public function action_index()
    {
        $site_id = (int) Model_Site::current()->id;

        $criteria = array(
            'site_id'  => $site_id,
            'answered' => 1,
            'active'   => 1
        );

        $count = Model::fly('Model_Question')->count_by($criteria);
        $pagination = new Pagination($count, $this->per_page);

        $order_by = $this->request->param('faq_order', 'created_at');
        $desc = (bool) $this->request->param('faq_pdesc', '1');

        $questions = Model::fly('Model_Question')->find_all_by($criteria, array(
            'order_by' => $order_by,
            'desc'     => $desc,
            'limit'    => $pagination->limit,
            'offset'   => $pagination->offset
        ));

        // Set up view
        $view = new View('frontend/faq');

        $view->form = $this->widget_form();
        $view->questions = $questions;
        $view->pagination = $pagination->render('pagination');

        $this->request->response = $this->render_layout($view->render());
    }

    /**
     * Render the question
     */
    public function action_question()
    {
        $site_id = (int) Model_Site::current()->id;

        $criteria = array(
            'site_id'  => $site_id,
            'answered' => 1,
            'active'   => 1
        );

        $question = new Model_Question();
        $crit       = $criteria;
        $crit['id'] = (int) $this->request->param('id');
        $question->find_by($crit);
        if ( ! isset($question->id))
        {
            $this->_action_404('Указанный вопрос не найден');
            return;
        }

        // Determine the page for the question
        // and contruct correct url back to the list of questions
        $order_by = $this->request->param('faq_order', 'created_at');
        $desc = (bool) $this->request->param('faq_pdesc', '1');
        
        $offset = $question->offset_by($criteria, array('order_by' => $order_by, 'desc' => $desc));
        $page = floor($offset / $this->per_page);
        $faq_url = URL::to('frontend/faq', array('page' => $page));


        // Set up view
        $view = new View('frontend/question');

        $view->form = $this->widget_form();
        $view->question = $question;
        $view->faq_url = $faq_url;

        $this->request->response = $this->render_layout($view->render());
    }

    /**
     * Render form to ask a question
     */
    public function widget_form()
    {
        // Form to ask question
        $form = new Form_Frontend_Question();

        if ($form->is_submitted() && $form->validate())
        {
            $question = new Model_Question();
            $question->values($form->get_values());
            $question->save();

            $question->notify_admin();

            FlashMessages::add('Ваш вопрос успешно отправлен!');
            $this->request->redirect($this->request->uri);
        }

        $view = new View('frontend/faq_form');
        $view->form = $form;
        return $view->render();
    }
}
