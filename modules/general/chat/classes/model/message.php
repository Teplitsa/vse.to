<?php defined('SYSPATH') or die('No direct script access.');

class Model_Message extends Model
{
    /**
     * Default active for message
     * 
     * @return bool
     */
    public function default_user_id()
    {
        return Auth::instance()->get_user()->id;
    }
    
    /**
     * Default active for message
     * 
     * @return bool
     */
    public function default_active()
    {
        return TRUE;
    }
    
    /**
     * Get short preview of the message text
     * 
     * @return string
     */
    public function get_message_preview()
    {
        return Text::limit_chars($this->message, 128, '...', TRUE);
    }

    /**
     * Set message creation time
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
     * Get message creation time
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
     * Get message creation date as string
     *
     * @return string
     */
    public function get_date()
    {
        return $this->created_at->format(Kohana::config('datetime.datetime_format'));
    }

    /**
     * @param array $newvalues
     */
    public function validate(array $newvalues)
    {
        if ( ! isset($newvalues['message']) || $newvalues['message'] == '')
        {
            $this->error('Укажите текст сообщения');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Get dialog for this message
     *
     * @return Model_Dialog
     */
    public function get_dialog()
    {
        if ( ! isset($this->_properties['dialog']))
        {
            $dialog = new Model_Dialog();

            if ($this->dialog_id != 0)
            {
                $dialog->find($this->dialog_id);
            }

            $this->_properties['dialog'] = $dialog;
        }

        return $this->_properties['dialog'];
    }
    
    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------
    /**
     * Validate message creation
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_create(array $newvalues)
    {
        return (
                $this->validate_dialog_id($newvalues)
             && $this->validate_receiver_id($newvalues)
        );
    }

    /**
     * Prohibit invalid dialog_id values
     *
     * @param array   $newvalues New values for model
     * @return boolean
     */
    public function validate_dialog_id(array $newvalues)
    {
        if ( isset($newvalues['dialog_id']))
        {
            if (!Model::fly('Model_Dialog')->exists_by_id($this->dialog_id))
            {
                $this->error('Указанный диалог не найден!');
                return FALSE;
            }
        }
        return TRUE;
    }    
    
    public function validate_receiver_id(array $newvalues)
    {
        if ( !isset($newvalues['receiver_id']) || ! Model::fly('Model_User')->exists_by_id($newvalues['receiver_id']))
        {
            $this->error('Указан неверный получатель!');
            return FALSE;      
        }
        return TRUE;
    }    
    /**
     * Save message
     * 
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        if (!isset($this->dialog_id))
        {
            $dialog = new Model_Dialog($this->values());
                        
            $dialog->save();
                        
            $this->dialog_id = $dialog->id;
        }        
        
        parent::save($force_create);
        
        //$this->notify_client();
        
    }

    /**
     * Set notification to client that his message has been answered
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
