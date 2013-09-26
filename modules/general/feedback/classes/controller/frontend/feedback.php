<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Frontend_Feedback extends Controller_Frontend
{
    /**
     * Prepare layout
     * 
     * @param  string $layout_script
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        $layout = parent::prepare_layout($layout_script);

        $layout->caption = 'Обратная связь';

        Breadcrumbs::append(array('uri' => $this->request->uri, 'caption' => 'Обратная связь'));
        
        return $layout;
    }
    
    /**
     * Render feedback form
     */
    public function action_index()
    {
        $form = new Form_Frontend_Feedback();

        if ($form->is_submitted() && $form->validate())
        {
            // Send notifications
            $this->notify($form);

            if ( ! Request::$is_ajax)
            {
                $this->request->redirect(URL::uri_to('frontend/feedback', array('action' => 'success')));
            }
            else
            {
                $this->request->forward('feedback', 'success');
                return;
            }
        }
        else
        {
            $content = $form->render();
        }

        if ( ! Request::$is_ajax)
        {
            $this->request->response = $this->render_layout($content);
        }
        else
        {
            $request = Widget::switch_context();

            $widget = $request->get_controller('feedback')
                        ->widget_popup();
            $widget->content = $content;
            $widget->to_response($this->request);

            $this->_action_ajax();
        }
    }

    /**
     * Display success message after the form is submitted
     */
    public function action_success()
    {
        $content = Widget::render_widget('blocks', 'block', 'feed_succ');

        if ( ! Request::$is_ajax)
        {
            $this->request->response = $this->render_layout($content);
        }
        else
        {
            $request = Widget::switch_context();

            $widget = $request->get_controller('feedback')
                        ->widget_popup();
            $widget->content = $content;
            $widget->to_response($this->request);

            $this->_action_ajax();
        }
    }

    /**
     * Popup widget
     *
     * @return Widget
     */
    public function widget_popup()
    {
        $widget = new Widget('layouts/popup');
        $widget->id = 'popup';
        $widget->wrapper = FALSE; // wrapping is done in the view file
        $widget->context_uri = FALSE;

        $widget->content = '';

        return $widget;
    }


    /**
     * Send e-mail notification about new question
     *
     * @param Form $form Feedback form
     */
    public function notify(Form $form)
    {
        try {
            $settings = Model_Site::current()->settings;
            $email_to     = isset($settings['email']['to'])        ? $settings['email']['to']        : '';
            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';

            if ($email_sender != '')
            {
                $email_from = array($email_from => $email_sender);
            }

            if ($email_to != '')
            {
                // Init mailer
                SwiftMailer::init();
                $transport = Swift_MailTransport::newInstance();
                $mailer = Swift_Mailer::newInstance($transport);

                // --- Send message to administrator

                $message = Swift_Message::newInstance()
                    ->setSubject('Заполнена форма обратной связи на сайте ' . URL::base(FALSE, TRUE))
                    ->setFrom($email_from)
                    ->setTo($email_to);


                // Message body
                $twig = Twig::instance();

                $template = $twig->loadTemplate('mail/feedback_admin');

                $message->setBody($template->render(array(
                    'phone'   => $form->get_value('phone'),
                    'name'    => $form->get_value('name'),
                    'comment' => $form->get_value('comment')
                )));

                // Send message
                $mailer->send($message);
            }
        }
        catch (Exception $e)
        {
            if (Kohana::$environment !== Kohana::PRODUCTION)
            {
                throw $e;
            }
        }
    }
}