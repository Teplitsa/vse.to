<?php defined('SYSPATH') or die('No direct script access.');

class Model_Question extends Model
{
    
    /**
     * @return integer
     */
    public function default_site_id()
    {
        return Model_Site::current()->id;
    }

    /**
     * @return boolean
     */
    public function default_active()
    {
        return FALSE;
    }

    /**
     * @return boolean
     */
    public function default_answered()
    {
        return FALSE;
    }

    /**
     * Get short preview of the question text
     * 
     * @return string
     */
    public function get_question_preview()
    {
        return Text::limit_chars($this->question, 128, '...', TRUE);
    }

    /**
     * Set question creation time
     *
     * @param DateTime|string $value
     */
    public function set_created_at($value)
    {
        if ($value instanceof DateTime)
        {
            $this->_properties['created_at'] = clone $value;
        }
        else
        {
            $this->_properties['created_at'] = new DateTime($value);
        }
    }
    
    /**
     * Get question creation time
     * 
     * @return DateTime
     */
    public function get_created_at()
    {
        if ( ! isset($this->_properties['created_at']))
        {
            if (empty($this->_properties['id']))
            {
                // Default value for a new question
                $this->_properties['created_at'] = new DateTime();
            }
            else
            {
                return NULL;
            }
        }
        return clone $this->_properties['created_at'];
    }

    /**
     * Get question creation date as string
     *
     * @return string
     */
    public function get_date()
    {
        return $this->created_at->format(Kohana::config('datetime.date_format'));
    }

    /**
     * @param array $newvalues
     */
    public function validate(array $newvalues)
    {
        if ( ! empty($newvalues['active']) && ( ! isset($newvalues['answer']) || $newvalues['answer'] == ''))
        {
            $this->error('Укажите ответ на вопрос');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Save question
     * 
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        if ($this->answer != '')
        {
            $this->answered = TRUE;
        }

        parent::save($force_create = FALSE);

        if ($this->notify && $this->answer != '')
        {
            $this->notify_client();
        }
    }

    /**
     * Set notification to client that his question has been answered
     */
    public function notify_client()
    {
        try {
            // Site settings
            $settings = Model_Site::current()->settings;
            $email_to     = isset($settings['email']['to'])        ? $settings['email']['to']        : '';
            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';

            if ($email_sender != '')
            {
                $email_from = array($email_from => $email_sender);
            }

            // FAQ settings
            $config = Modules::load_config('faq_' . Model_Site::current()->id, 'faq');
            $subject = isset($config['email']['client']['subject'])  ? $config['email']['client']['subject']  : '';
            $body    = isset($config['email']['client']['body'])     ? $config['email']['client']['body']     : '';


            if ($this->email != '')
            {
                $twig = Twig::instance('string');
                
                // Values for the templates
                $values = $this->values();
                $values['site'] = Model_Site::canonize_url(URL::site('', TRUE));

                // Subject
                $subject = $twig->loadTemplate($subject)->render($values);

                // Body (with optional signature)
                $body = $twig->loadTemplate($body)->render($values);
                if ($signature != '')
                {
                    $body .= "\n\n\n" . $signature;
                }

                // Init mailer
                SwiftMailer::init();
                $transport = Swift_MailTransport::newInstance();
                $mailer = Swift_Mailer::newInstance($transport);

                $message = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($email_from)
                    ->setTo($this->email)
                    ->setBody($body);

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

    /**
     * Set notification to administrator about the new question
     */
    public function notify_admin()
    {
        try {
            // Site settings
            $settings = Model_Site::current()->settings;
            $email_to     = isset($settings['email']['to'])        ? $settings['email']['to']        : '';
            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';

            if ($email_sender != '')
            {
                $email_from = array($email_from => $email_sender);
            }

            // FAQ settings
            $config = Modules::load_config('faq_' . Model_Site::current()->id, 'faq');
            $subject = isset($config['email']['admin']['subject'])  ? $config['email']['admin']['subject']  : '';
            $body    = isset($config['email']['admin']['body'])     ? $config['email']['admin']['body']     : '';


            if ($email_to != '')
            {
                $twig = Twig::instance('string');

                // Values for the templates
                $values = $this->values();
                $values['site'] = Model_Site::canonize_url(URL::site('', TRUE));

                // Subject
                $subject = $twig->loadTemplate($subject)->render($values);

                // Body
                $body = $twig->loadTemplate($body)->render($values);

                // Init mailer
                SwiftMailer::init();
                $transport = Swift_MailTransport::newInstance();
                $mailer = Swift_Mailer::newInstance($transport);

                $message = Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($email_from)
                    ->setTo($email_to)
                    ->setBody($body);

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
